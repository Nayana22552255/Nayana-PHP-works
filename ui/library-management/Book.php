<?php
require_once  'Database.php';

class Book
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getGenres(): array
    {
        $sql = "SELECT genre_id, genre_name FROM genres ORDER BY genre_name";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAuthors(): array
    {
        $sql = "SELECT author_id, author_name FROM authors ORDER BY author_name";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll(?string $keyword = null): array
    {
        $sql = "SELECT b.book_id, b.title, b.publication_year, b.isbn,
                       b.physical_format, b.price, g.genre_name,
                       GROUP_CONCAT(DISTINCT a.author_name ORDER BY a.author_name SEPARATOR ', ') AS author_names,
                       COUNT(DISTINCT bc.copy_id) AS total_copies,
                       COUNT(DISTINCT CASE WHEN bc.status = 'Available' THEN bc.copy_id END) AS available_copies
                FROM books b
                LEFT JOIN genres g ON g.genre_id = b.genre_id
                LEFT JOIN book_authors ba ON ba.book_id = b.book_id
                LEFT JOIN authors a ON a.author_id = ba.author_id
                LEFT JOIN book_copies bc ON bc.book_id = b.book_id";
        $params = [];
        if ($keyword !== null && trim($keyword) !== '') {
            $sql .= " WHERE b.title LIKE :keyword OR b.isbn LIKE :keyword OR a.author_name LIKE :keyword";
            $params[':keyword'] = '%' . trim($keyword) . '%';
        }
        $sql .= " GROUP BY b.book_id ORDER BY b.book_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|false
    {
        $sql = "SELECT b.*, g.genre_name,
                       GROUP_CONCAT(DISTINCT a.author_name ORDER BY a.author_name SEPARATOR ', ') AS author_names,
                       COUNT(DISTINCT bc.copy_id) AS total_copies,
                       COUNT(DISTINCT CASE WHEN bc.status = 'Available' THEN bc.copy_id END) AS available_copies
                FROM books b
                LEFT JOIN genres g ON g.genre_id = b.genre_id
                LEFT JOIN book_authors ba ON ba.book_id = b.book_id
                LEFT JOIN authors a ON a.author_id = ba.author_id
                LEFT JOIN book_copies bc ON bc.book_id = b.book_id
                WHERE b.book_id = :id
                GROUP BY b.book_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAuthorIds(int $id): array
    {
        $stmt = $this->conn->prepare('SELECT author_id FROM book_authors WHERE book_id = ?');
        $stmt->execute([$id]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function saveFromForm(array $form): void
    {
        $id = (int)($form['id'] ?? 0);
        $title = trim((string)($form['title'] ?? ''));
        $yearInput = trim((string)($form['publication_year'] ?? ''));
        $isbn = trim((string)($form['isbn'] ?? ''));
        $format = trim((string)($form['physical_format'] ?? ''));
        $description = trim((string)($form['description'] ?? ''));
        $priceInput = $form['price'] ?? '';
        $genreId = (int)($form['genre_id'] ?? 0);
        $authorIds = $form['author_ids'] ?? [];
        $copies = (int)($form['copies'] ?? 1);

        if ($title === '') throw new InvalidArgumentException('Book title is required.');
        if ($yearInput !== '' && (!ctype_digit($yearInput) || (int)$yearInput < 1901 || (int)$yearInput > 2155)) {
            throw new InvalidArgumentException('Enter a valid publication year.');
        }
        if (!is_numeric($priceInput) || (float)$priceInput < 0) throw new InvalidArgumentException('Enter a valid non-negative price.');
        if ($genreId < 1) throw new InvalidArgumentException('Please select a genre.');
        $validFormats = ['Book', 'Manga', 'Magazine', 'Comic', 'Graphic Novel', 'Textbook', 'Reference Book', 'Journal'];
        if (!in_array($format, $validFormats, true)) throw new InvalidArgumentException('Please select a valid physical format.');
        if (!is_array($authorIds)) throw new InvalidArgumentException('Invalid author selection.');
        $authorIds = array_values(array_unique(array_filter(array_map('intval', $authorIds), fn($v) => $v > 0)));

        $check = $this->conn->prepare('SELECT COUNT(*) FROM genres WHERE genre_id = ?');
        $check->execute([$genreId]);
        if ((int)$check->fetchColumn() === 0) throw new InvalidArgumentException('Selected genre does not exist.');
        $checkAuthor = $this->conn->prepare('SELECT COUNT(*) FROM authors WHERE author_id = ?');
        foreach ($authorIds as $authorId) {
            $checkAuthor->execute([$authorId]);
            if ((int)$checkAuthor->fetchColumn() === 0) throw new InvalidArgumentException('Selected author does not exist.');
        }

        $this->conn->beginTransaction();
        try {
            $values = [
                ':title' => $title,
                ':year' => $yearInput === '' ? null : (int)$yearInput,
                ':isbn' => $isbn === '' ? null : $isbn,
                ':format' => $format,
                ':description' => $description === '' ? null : $description,
                ':price' => (float)$priceInput,
                ':genre' => $genreId
            ];
            if ($id > 0) {
                $values[':id'] = $id;
                $stmt = $this->conn->prepare("UPDATE books SET title=:title, publication_year=:year, isbn=:isbn, physical_format=:format, description=:description, price=:price, genre_id=:genre WHERE book_id=:id");
                $stmt->execute($values);
                if ($stmt->rowCount() === 0 && !$this->getById($id)) throw new RuntimeException('Book not found.');
                $this->conn->prepare('DELETE FROM book_authors WHERE book_id = ?')->execute([$id]);
                $bookId = $id;
            } else {
                $stmt = $this->conn->prepare("INSERT INTO books (title, publication_year, isbn, physical_format, description, price, genre_id) VALUES (:title, :year, :isbn, :format, :description, :price, :genre)");
                $stmt->execute($values);
                $bookId = (int)$this->conn->lastInsertId();
            }

            $link = $this->conn->prepare('INSERT INTO book_authors (book_id, author_id) VALUES (?, ?)');
            foreach ($authorIds as $authorId) $link->execute([$bookId, $authorId]);

            if ($id === 0) {
                if ($copies < 1) throw new InvalidArgumentException('At least one copy is required.');
                $copyStmt = $this->conn->prepare("INSERT INTO book_copies (book_id, status, inventory_date) VALUES (?, 'Available', CURDATE())");
                for ($i = 0; $i < $copies; $i++) $copyStmt->execute([$bookId]);
            }
            $this->conn->commit();
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $check = $this->conn->prepare('SELECT COUNT(*) FROM borrowings WHERE book_id = ?');
        $check->execute([$id]);
        if ((int)$check->fetchColumn() > 0) throw new RuntimeException('This book has borrowing records and cannot be deleted.');

        $this->conn->beginTransaction();
        try {
            $this->conn->prepare('DELETE FROM book_authors WHERE book_id = ?')->execute([$id]);
            $this->conn->prepare('DELETE FROM book_copies WHERE book_id = ?')->execute([$id]);
            $stmt = $this->conn->prepare('DELETE FROM books WHERE book_id = ?');
            $stmt->execute([$id]);
            $deleted = $stmt->rowCount() > 0;
            $this->conn->commit();
            return $deleted;
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            throw $e;
        }
    }

    public function renderForm(?array $edit = null): void
    {
        $genres = $this->getGenres();
        $authors = $this->getAuthors();
        $selected = $edit ? $this->getAuthorIds((int)$edit['book_id']) : [];
        $formats = ['Book', 'Manga', 'Magazine', 'Comic', 'Graphic Novel', 'Textbook', 'Reference Book', 'Journal'];
        ?>
        <h3><?= $edit ? 'Edit Book' : 'Add New Book' ?></h3>
        <form method="post" action="index.php?page=books">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= e($edit['book_id'] ?? '') ?>">
            <p><label>Title: <input type="text" name="title" maxlength="50" required value="<?= e($edit['title'] ?? '') ?>"></label></p>
            <p><label>Publication year: <input type="number" name="publication_year" min="1901" max="2155" value="<?= e($edit['publication_year'] ?? '') ?>"></label></p>
            <p><label>ISBN: <input type="text" name="isbn" maxlength="20" value="<?= e($edit['isbn'] ?? '') ?>"></label></p>
            <p><label>Physical format:
                <select name="physical_format" required><option value="">Select format</option>
                <?php foreach ($formats as $format): ?><option value="<?= e($format) ?>" <?= ($edit['physical_format'] ?? '') === $format ? 'selected' : '' ?>><?= e($format) ?></option><?php endforeach; ?>
                </select></label></p>
            <p><label>Description:<br><textarea name="description" rows="4" cols="45"><?= e($edit['description'] ?? '') ?></textarea></label></p>
            <p><label>Price: <input type="number" name="price" min="0" step="0.01" required value="<?= e($edit['price'] ?? '0.00') ?>"></label></p>
            <p><label>Genre:
                <select name="genre_id" required><option value="">Select genre</option>
                <?php foreach ($genres as $genre): ?><option value="<?= e($genre['genre_id']) ?>" <?= (string)($edit['genre_id'] ?? '') === (string)$genre['genre_id'] ? 'selected' : '' ?>><?= e($genre['genre_name']) ?></option><?php endforeach; ?>
                </select></label></p>
            <fieldset><legend>Authors</legend>
                <?php if (!$authors): ?><p>No authors found. Add an author first.</p><?php endif; ?>
                <?php foreach ($authors as $author): ?><label><input type="checkbox" name="author_ids[]" value="<?= e($author['author_id']) ?>" <?= in_array((int)$author['author_id'], $selected, true) ? 'checked' : '' ?>> <?= e($author['author_name']) ?></label><br><?php endforeach; ?>
            </fieldset>
            <?php if (!$edit): ?><p><label>Number of copies: <input type="number" name="copies" min="1" value="1" required></label></p><?php endif; ?>
            <p><button type="submit"><?= $edit ? 'Update Book' : 'Add Book' ?></button> <a href="index.php?page=books">Cancel</a></p>
        </form>
        <?php
    }

    public function renderBookList(?string $keyword = null): void
    {
        $books = $this->getAll($keyword);
        if (!$books) { echo '<p>No books found.</p>'; return; }
        ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr><th>ID</th><th>Title</th><th>Author(s)</th><th>Genre</th><th>Year</th><th>Price</th><th>Copies</th><th>Available</th><th>Actions</th></tr>
            <?php foreach ($books as $row): ?>
            <tr>
                <td><?= e($row['book_id']) ?></td><td><?= e($row['title']) ?></td><td><?= e($row['author_names'] ?? 'Not assigned') ?></td><td><?= e($row['genre_name'] ?? 'Not assigned') ?></td><td><?= e($row['publication_year']) ?></td><td><?= e($row['price']) ?></td><td><?= e($row['total_copies']) ?></td><td><?= e($row['available_copies']) ?></td>
                <td><a href="?page=books&action=edit&id=<?= e($row['book_id']) ?>">Edit</a> |
                    <a href="?page=books&action=view&id=<?= e($row['book_id']) ?>">View</a>
                    <form method="post" action="index.php?page=books" style="display:inline" onsubmit="return confirm('Delete this book?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($row['book_id']) ?>"><button type="submit">Delete</button></form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }
}
