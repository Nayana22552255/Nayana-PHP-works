<?php
/* Library Management System - MySQL / PDO */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Author.php';
require_once __DIR__ . '/Book.php';
require_once __DIR__ . '/BookInventory.php';
require_once __DIR__ . '/Borrowing.php';
require_once __DIR__ . '/Fee.php';
require_once __DIR__ . '/Genre.php';
require_once __DIR__ . '/Member.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn) || !$conn instanceof PDO) {
    die('Database connection is not available. Check Database.php and make sure it creates $conn.');
}
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function e(mixed $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function go(string $page): void
{
    header('Location: index.php?page=' . urlencode($page));
    exit;
}

function countRows(PDO $db, string $sql, array $params = []): int
{
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $error) {
        return 0;
    }
}

function rows(PDO $db, string $sql, array $params = []): array
{
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function options(PDO $db, string $sql, string $value, string $label): array
{
    try {
        $result = rows($db, $sql);
        return array_map(static function (array $item) use ($value, $label): array {
            return [
                'id' => $item[$value] ?? '',
                'name' => $item[$label] ?? ($item[$value] ?? '')
            ];
        }, $result);
    } catch (Throwable $error) {
        return [];
    }
}

function showTable(array $data, bool $showEdit = false): void
{
    if (!$data) {
        echo '<p>No records found.</p>';
        return;
    }

    echo '<table border="1" cellpadding="6" cellspacing="0"><tr>';
    foreach (array_keys($data[0]) as $heading) {
        echo '<th>' . e(ucwords(str_replace('_', ' ', (string)$heading))) . '</th>';
    }
    if ($showEdit) {
        echo '<th>Action</th>';
    }
    echo '</tr>';

    foreach ($data as $row) {
        echo '<tr>';
        foreach ($row as $value) {
            echo '<td>' . e($value) . '</td>';
        }
        if ($showEdit) {
            $id = reset($row);
            echo '<td><a href="?page=' . e($_GET['page'] ?? 'dashboard') . '&action=edit&id=' . e($id) . '">Edit</a></td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}

function field(string $label, string $name, mixed $value = '', string $type = 'text', bool $required = false): void
{
    echo '<p><label>' . e($label) . ': <input type="' . e($type) . '" name="' . e($name) . '" value="' . e($value) . '"' . ($required ? ' required' : '') . '></label></p>';
}

function selectField(string $label, string $name, array $items, mixed $selected = null, string $valueKey = 'id', string $labelKey = 'name'): void
{
    echo '<p><label>' . e($label) . ': <select name="' . e($name) . '">';
    echo '<option value="">Select ' . e($label) . '</option>';
    foreach ($items as $item) {
        $value = $item[$valueKey] ?? '';
        $text = $item[$labelKey] ?? $value;
        $isSelected = ((string)$selected === (string)$value) ? ' selected' : '';
        echo '<option value="' . e($value) . '"' . $isSelected . '>' . e($text) . '</option>';
    }
    echo '</select></label></p>';
}

$page = (string)($_GET['page'] ?? 'dashboard');
$action = (string)($_POST['action'] ?? $_GET['action'] ?? 'list');
$message = '';
$bookFormValues = $page === 'books' && $action === 'add_author' ? $_POST : [];
$newAuthorId = 0;

/* Handle form submissions. */
try {
    // BOOKS
    if ($page === 'books' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $book = new Book($conn);
        if ($action === 'save') {
            $book->saveFromForm($_POST);
            $message = (int)($_POST['id'] ?? 0) > 0
                ? 'Book updated successfully.'
                : 'Book added successfully.';
        } elseif ($action === 'add_author') {
            $name = trim((string)($_POST['new_author_name'] ?? ''));
            if ($name === '') {
                throw new InvalidArgumentException('Author name is required.');
            }
            $biography = trim((string)($_POST['new_author_biography'] ?? '')) ?: null;
            (new Author($conn))->addAuthor($name, $biography);
            $newAuthorId = (int)$conn->lastInsertId();
            $message = 'Author added and selected. Save the book to finish.';
            $action = (int)($_POST['id'] ?? 0) > 0 ? 'edit' : 'list';
        } elseif ($action === 'delete') {
            $deleted = $book->delete((int)($_POST['id'] ?? 0));
            $message = $deleted ? 'Book deleted successfully.' : 'Book could not be deleted. It may have borrowing records or may not exist.';
        }
        if ($action !== 'edit') {
            $action = 'list';
        }
    }

    // AUTHORS
    if ($page === 'authors' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $author = new Author($conn);
        if ($action === 'save') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim((string)($_POST['author_name'] ?? ''));
            $bio = trim((string)($_POST['biography'] ?? '')) ?: null;
            if ($name === '') {
                throw new InvalidArgumentException('Author name is required.');
            }
            if ($id > 0) {
                $author->update($id, $name, $bio);
                $message = 'Author updated successfully.';
            } else {
                $author->addAuthor($name, $bio);
                $message = 'Author added successfully.';
            }
        } elseif ($action === 'delete') {
            $deleted = $author->delete((int)($_POST['id'] ?? 0));
            $message = $deleted ? 'Author deleted successfully.' : 'Author could not be deleted. It may be linked to a book.';
        }
        $action = 'list';
    }

    // GENRES
    if ($page === 'genres' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $genre = new Genre($conn);
        if ($action === 'save') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim((string)($_POST['genre_name'] ?? ''));
            $parentInput = trim((string)($_POST['parent_genre_id'] ?? ''));
            if ($name === '') {
                throw new InvalidArgumentException('Genre name is required.');
            }
            if ($parentInput !== '' && !ctype_digit($parentInput)) {
                throw new InvalidArgumentException('Select a valid parent genre.');
            }
            $parentId = $parentInput === '' ? null : (int)$parentInput;
            if ($id > 0) {
                $genre->update($id, $name, $parentId);
                $message = 'Genre updated successfully.';
            } else {
                $genre->add($name, $parentId);
                $message = 'Genre added successfully.';
            }
        } elseif ($action === 'delete') {
            $deleted = $genre->delete((int)($_POST['id'] ?? 0));
            $message = $deleted ? 'Genre deleted successfully.' : 'Genre could not be deleted.';
        }
        $action = 'list';
    }

    // MEMBERS
    if ($page === 'members' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $memberManager = new Member($conn);
        if ($action === 'save') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim((string)($_POST['name'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $phone = trim((string)($_POST['phone'] ?? ''));
            $category = trim((string)($_POST['category'] ?? ''));
            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Enter a name and a valid email address.');
            }
            if ($id > 0) {
                $memberManager->updateMembers($id, $name, $email, $phone, $category);
                $message = 'Member updated successfully.';
            } else {
                $memberManager->addMember($name, $email, $phone, $category);
                $message = 'Member added successfully.';
            }
        } elseif ($action === 'status') {
            $active = (int)($_POST['active'] ?? 0);
            if (!in_array($active, [0, 1], true)) {
                throw new InvalidArgumentException('Invalid member status.');
            }
            $changed = $memberManager->setStatus((int)($_POST['id'] ?? 0), $active === 1);
            $message = $changed ? 'Member status updated.' : 'Member status was not changed.';
        } elseif ($action === 'delete') {
            $deleted = $memberManager->deleteMember((int)($_POST['id'] ?? 0));
            $message = $deleted ? 'Member deleted successfully.' : 'Member could not be deleted.';
        }
        $action = 'list';
    }

    // BORROW / RETURN
    if ($page === 'borrowings' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'borrow') {
            $memberId = (int)($_POST['member_id'] ?? 0);
            $bookId = (int)($_POST['book_id'] ?? 0);
            if ($memberId < 1 || $bookId < 1) {
                throw new InvalidArgumentException('Select a valid member and book.');
            }

            $conn->beginTransaction();
            $memberQuery = $conn->prepare(
                'SELECT m.member_id, m.active, mf.max_books, mf.loan_days
                 FROM members m
                 JOIN member_fee mf ON mf.category_id = m.category_id
                 WHERE m.member_id = ? AND m.active = 1
                 FOR UPDATE'
            );
            $memberQuery->execute([$memberId]);
            $member = $memberQuery->fetch(PDO::FETCH_ASSOC);
            if (!$member) {
                throw new RuntimeException('Member not found or inactive.');
            }

            $countQuery = $conn->prepare('SELECT COUNT(*) FROM borrowings WHERE member_id = ? AND return_date IS NULL');
            $countQuery->execute([$memberId]);
            if ((int)$countQuery->fetchColumn() >= (int)$member['max_books']) {
                throw new RuntimeException('Member has reached the borrowing limit.');
            }

            $copyQuery = $conn->prepare("SELECT copy_id FROM book_copies WHERE book_id = ? AND status = 'Available' ORDER BY copy_id LIMIT 1 FOR UPDATE");
            $copyQuery->execute([$bookId]);
            $copyId = $copyQuery->fetchColumn();
            if (!$copyId) {
                throw new RuntimeException('No available copy for this book.');
            }

            $days = max(1, (int)$member['loan_days']);
            $insert = $conn->prepare('INSERT INTO borrowings (member_id, book_id, copy_id, borrow_date, due_date, return_date, late_fee, lost_fee) VALUES (?, ?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL ? DAY), NULL, 0, 0)');
            $insert->execute([$memberId, $bookId, $copyId, $days]);
            $conn->prepare("UPDATE book_copies SET status = 'Borrowed' WHERE copy_id = ?")->execute([$copyId]);
            $conn->commit();
            $message = 'Book borrowed successfully. Copy ID: ' . $copyId;
        } elseif ($action === 'return') {
            $borrowingId = (int)($_POST['borrowing_id'] ?? 0);
            $query = $conn->prepare('SELECT b.*, m.category_id AS fee_id FROM borrowings b JOIN members m ON m.member_id = b.member_id WHERE b.borrowing_id = ? AND b.return_date IS NULL');
            $query->execute([$borrowingId]);
            $borrowing = $query->fetch(PDO::FETCH_ASSOC);
            if (!$borrowing) {
                throw new RuntimeException('Active borrowing record not found.');
            }

            $heldQuery = $conn->prepare('SELECT GREATEST(DATEDIFF(CURDATE(), borrow_date), 0) FROM borrowings WHERE borrowing_id = ?');
            $heldQuery->execute([$borrowingId]);
            $heldDays = (int)$heldQuery->fetchColumn();

            $lateQuery = $conn->prepare('SELECT GREATEST(DATEDIFF(CURDATE(), ?), 0)');
            $lateQuery->execute([$borrowing['due_date']]);
            $lateDays = (int)$lateQuery->fetchColumn();

            $rate = 0.0;
            if (!empty($borrowing['fee_id'])) {
                $rateQuery = $conn->prepare('SELECT late_fee_per_day FROM member_fee WHERE category_id = ?');
                $rateQuery->execute([$borrowing['fee_id']]);
                $rate = (float)$rateQuery->fetchColumn();
            }
            $lateFee = $lateDays * $rate;

            $conn->beginTransaction();
            $update = $conn->prepare('UPDATE borrowings SET return_date = CURDATE(), late_fee = ? WHERE borrowing_id = ?');
            $update->execute([$lateFee, $borrowingId]);
            if (!empty($borrowing['copy_id'])) {
                $conn->prepare("UPDATE book_copies SET status = 'Available' WHERE copy_id = ?")->execute([$borrowing['copy_id']]);
            }
            if ($lateFee > 0) {
                $feeInsert = $conn->prepare("INSERT INTO fees (borrowing_id, fee_type, amount, fee_date, payment_status) VALUES (?, 'Late fee', ?, CURDATE(), 'pending')");
                $feeInsert->execute([$borrowingId, $lateFee]);
            }
            $conn->commit();
            $message = 'Book returned. Held for ' . $heldDays . ' days. Late days: ' . $lateDays . '. Late fee: Rs. ' . number_format($lateFee, 2);
        }
        $action = 'list';
    }

    // FEES
    if ($page === 'fees' && $_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'pay') {
        $paid = (new Fee($conn))->markPaid((int)($_POST['fee_id'] ?? 0));
        $message = $paid ? 'Payment recorded.' : 'Fee not found or already paid.';
        $action = 'list';
    }
} catch (Throwable $error) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    $message = 'Operation failed: ' . $error->getMessage();
    $action = $page === 'books' && $bookFormValues !== [] && (int)($_POST['id'] ?? 0) > 0
        ? 'edit'
        : 'list';
}

/* Load selected record for edit forms. */
$edit = null;
if ($action === 'edit' && (isset($_GET['id']) || isset($_POST['id']))) {
    $tableMap = [
        'books' => ['books', 'book_id'],
        'authors' => ['authors', 'author_id'],
        'genres' => ['genres', 'genre_id'],
        'members' => ['members', 'member_id']
    ];
    if (isset($tableMap[$page])) {
        [$table, $primaryKey] = $tableMap[$page];
        $query = $conn->prepare("SELECT * FROM `$table` WHERE `$primaryKey` = ?");
        $query->execute([(int)($_GET['id'] ?? $_POST['id'])]);
        $edit = $query->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Management System</title>
</head>
<body>

<h1><center>LIBRARY MANAGEMENT SYSTEM</center></h1>
<hr>
<?php if ($page !== 'dashboard'): ?>
    <p><a href="index.php">Main Menu</a></p>
<?php endif; ?>
<hr>

<?php if ($message !== ''): ?>
    <p><strong><?= e($message) ?></strong></p>
<?php endif; ?>

<?php if ($page === 'dashboard'): ?>
    <div align="center">
    <!--<h2>Main Menu</h2>-->
    <h2>Select a section to manage the library</h2>
    <table border="1" cellpadding="20" cellspacing="10" bgcolor="#ffffff">
        <tr>
            <td bgcolor="#0c2653"><a href="index.php?page=books"><font color="#ffffff">Book Management</font></a></td>
            <td bgcolor="#0c2653"><a href="index.php?page=authors"><font color="#ffffff">Author Management</font></a></td>
            <td bgcolor="#0c2653"><a href="index.php?page=genres"><font color="#ffffff">Genre Management</font></a></td>
            <td bgcolor="#0c2653"><a href="index.php?page=members"><font color="#ffffff">Member Management</font></a></td>
            <td bgcolor="#0c2653"><a href="index.php?page=borrowings"><font color="#ffffff">Borrowing Management</font></a></td>
            <td bgcolor="#0c2653"><a href="index.php?page=fees"><font color="#ffffff">Fine Management</font></a></td>
            <td bgcolor="#0c2653"><a href="index.php?page=inventory"><font color="#ffffff">Book History</font></a></td>
        </tr>
    </table>

    <hr>
    <h2>Library Overview</h2>
    <?php
    $books=$conn->query("SELECT COUNT(*)FROM books")->fetchColumn();
    $authors=$conn->query("SELECT  COUNT(*)FROM authors")->fetchColumn();
    $genres=$conn->query("SELECT COUNT(*)FROM genres")->fetchColumn();
    $members=$conn->query("SELECT COUNT(*)FROM members")->fetchColumn();
    $borrowings=$conn->query("SELECT COUNT(*)FROM borrowings")->fetchColumn();
    $fees=$conn->query("SELECT COUNT(*)FROM fees")->fetchColumn();
    $overdues=$conn->query(
        "SELECT COUNT(*) FROM borrowings
         WHERE return_date IS NULL AND due_date < CURDATE()"
    )->fetchColumn();
    ?>
    <table border="1" cellpadding="13" cellspacing="0" width="90%" height=150%>
        <tr>
                <th bgcolor="#0c2653"><font color="#ffffff">Books</font></th>
                <th bgcolor="#0c2653"><font color="#ffffff">Authors</font></th>
                <th bgcolor="#0c2653"><font color="#ffffff">Genres</font></th>
                <th bgcolor="#0c2653"><font color="#ffffff">Members</font></th>
                <th bgcolor="#0c2653"><font color="#ffffff">Borrowings</font></th>
                <th bgcolor="#0c2653"><font color="#ffffff">Fees</font></th>
                <th bgcolor="#0c2653"><font color="#ffffff">Overdues</font></th>
        </tr>
        <tr>
                <td bgcolor="#eef3f8" align="center"><?= $books?></td>
                <td bgcolor="#eef3f8" align="center"><?= $authors ?></td>
                <td bgcolor="#eef3f8" align="center"><?= $genres ?></td>
                <td bgcolor="#eef3f8" align="center"><?= $members ?></td>
                <td bgcolor="#eef3f8" align="center"><?= $borrowings ?></td>
                <td bgcolor="#eef3f8" align="center"><?= $fees ?></td>
                <td bgcolor="#eef3f8" align="center"><?= $overdues ?></td>
        </tr>
   

    </table>
    </div>

<?php elseif ($page === 'books'): ?>
    <h2>Book Management</h2>
    <?php
    $genres = rows($conn, 'SELECT genre_id, genre_name FROM genres ORDER BY genre_name');
    $authors = rows($conn, 'SELECT author_id, author_name FROM authors ORDER BY author_name');
    $selectedAuthorIds = [];
    if ($bookFormValues !== []) {
        $postedAuthorIds = $bookFormValues['author_ids'] ?? [];
        if (is_array($postedAuthorIds)) {
            $selectedAuthorIds = array_values(array_unique(array_map('intval', $postedAuthorIds)));
        }
    } elseif ($edit && isset($edit['book_id'])) {
        $selectedAuthorIds = array_map('intval', rows($conn, 'SELECT author_id FROM book_authors WHERE book_id = ?', [(int)$edit['book_id']]));
        $selectedAuthorIds = array_column($selectedAuthorIds, 'author_id');
    }
    if ($newAuthorId > 0) {
        $selectedAuthorIds[] = $newAuthorId;
    }
    ?>
    <h3><?= $edit ? 'Update Book' : 'Add New Book' ?></h3>
    <form method="post" action="index.php?page=books">
        <input type="hidden" name="id" value="<?= e($edit['book_id'] ?? '') ?>">
        <?php field('Book Title', 'title', $bookFormValues['title'] ?? $edit['title'] ?? '', 'text', true); ?>
        <?php field('Publication Year', 'publication_year', $bookFormValues['publication_year'] ?? $edit['publication_year'] ?? '', 'number'); ?>
        <?php field('ISBN', 'isbn', $bookFormValues['isbn'] ?? $edit['isbn'] ?? ''); ?>
        <p><label>Physical Format:
            <select name="physical_format" required>
                <option value="">Select Format</option>
                <?php foreach (['Book', 'Manga', 'Magazine', 'Comic', 'Graphic Novel', 'Textbook', 'Reference Book', 'Journal'] as $format): ?>
                    <option value="<?= e($format) ?>" <?= (($bookFormValues['physical_format'] ?? $edit['physical_format'] ?? '') === $format) ? 'selected' : '' ?>><?= e($format) ?></option>
                <?php endforeach; ?>
            </select>
        </label></p>
        <p>Description:<br><textarea name="description" rows="4" cols="40"><?= e($bookFormValues['description'] ?? $edit['description'] ?? '') ?></textarea></p>
        <?php field('Price', 'price', $bookFormValues['price'] ?? $edit['price'] ?? '0', 'number', true); ?>
        <p><label>Genre:
            <select name="genre_id" required>
                <option value="">Select Genre</option>
                <?php foreach ($genres as $genreRow): ?>
                    <option value="<?= e($genreRow['genre_id']) ?>" <?= ((string)($bookFormValues['genre_id'] ?? $edit['genre_id'] ?? '') === (string)$genreRow['genre_id']) ? 'selected' : '' ?>><?= e($genreRow['genre_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label></p>

        <fieldset>
    <legend>Select Authors</legend>

    <label for="author_ids">Choose Authors:</label><br>

    <select name="author_ids[]" id="author_ids" multiple size="5">

        <?php foreach ($authors as $authorRow): ?>

            <option
                value="<?= e($authorRow['author_id']) ?>"
                <?= in_array(
                    (int)$authorRow['author_id'],
                    $selectedAuthorIds,
                    true
                ) ? 'selected' : '' ?>
            >
                <?= e($authorRow['author_name']) ?>
            </option>

        <?php endforeach; ?>

    </select>
    <p>Hold Ctrl to select multiple authors.</p>
    <button type="button" onclick="document.getElementById('new-author-form').hidden = !document.getElementById('new-author-form').hidden">Add a new author</button>
    <div id="new-author-form" <?= $bookFormValues === [] ? 'hidden' : '' ?>>
        <p><label>Author name: <input type="text" name="new_author_name" maxlength="20" value="<?= e($newAuthorId === 0 ? ($bookFormValues['new_author_name'] ?? '') : '') ?>"></label></p>
        <p><label>Biography:<br><textarea name="new_author_biography" rows="3" cols="40"><?= e($newAuthorId === 0 ? ($bookFormValues['new_author_biography'] ?? '') : '') ?></textarea></label></p>
        <button type="submit" name="action" value="add_author" formnovalidate>Add Author</button>
    </div>

</fieldset>

        <?php if (!$edit): ?>
            <?php field('Number of Copies', 'copies', $bookFormValues['copies'] ?? '1', 'number', true); ?>
        <?php else: ?>
            <input type="hidden" name="copies" value="1">
        <?php endif; ?>
        <button type="submit" name="action" value="save">Save Book</button>
        <?php if ($edit): ?> <a href="?page=books">Cancel Edit</a><?php endif; ?>
    </form>

    <h3>Search Books</h3>
    <form method="get" action="index.php">
        <input type="hidden" name="page" value="books">
        <label>Title or ISBN: <input type="search" name="keyword" value="<?= e($_GET['keyword'] ?? '') ?>"></label>
        <button type="submit">Search</button>
        <a href="index.php?page=books">Clear</a>
    </form>
    <h3>All Books</h3>
    <?php
    $keyword = trim((string)($_GET['keyword'] ?? ''));
    $sql = "SELECT b.book_id, b.title, b.publication_year, b.isbn, b.physical_format, b.price,
                   g.genre_name,
                   GROUP_CONCAT(DISTINCT a.author_name ORDER BY a.author_name SEPARATOR ', ') AS author_names,
                   COUNT(DISTINCT bc.copy_id) AS total_copies,
                   COUNT(DISTINCT CASE WHEN bc.status = 'Available' THEN bc.copy_id END) AS available_copies
            FROM books b
            LEFT JOIN genres g ON g.genre_id = b.genre_id
            LEFT JOIN book_authors ba ON ba.book_id = b.book_id
            LEFT JOIN authors a ON a.author_id = ba.author_id
            LEFT JOIN book_copies bc ON bc.book_id = b.book_id";
    if ($keyword !== '') {
        $sql .= ' WHERE b.title LIKE :keyword OR b.isbn LIKE :keyword OR a.author_name LIKE :keyword';
    }
    $sql .= ' GROUP BY b.book_id ORDER BY b.book_id DESC';
    $stmt = $conn->prepare($sql);
    if ($keyword !== '') {
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
    } else {
        $stmt->execute();
    }
    $bookRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <?php if (!$bookRows): ?>
        <p>No books found.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr><th>ID</th><th>Title</th><th>Authors</th><th>Genre</th><th>Year</th><th>ISBN</th><th>Price</th><th>Total Copies</th><th>Available Copies</th><th>Actions</th></tr>
            <?php foreach ($bookRows as $bookRow): ?>
                <tr>
                    <td><?= e($bookRow['book_id']) ?></td><td><?= e($bookRow['title']) ?></td>
                    <td><?= e($bookRow['author_names'] ?? 'Not assigned') ?></td><td><?= e($bookRow['genre_name'] ?? 'Not assigned') ?></td>
                    <td><?= e($bookRow['publication_year']) ?></td><td><?= e($bookRow['isbn']) ?></td><td><?= e($bookRow['price']) ?></td>
                    <td><?= e($bookRow['total_copies']) ?></td><td><?= e($bookRow['available_copies']) ?></td>
                    <td>
                        <a href="?page=books&action=edit&id=<?= e($bookRow['book_id']) ?>">Edit</a>
                        <form method="post" action="index.php?page=books" style="display:inline">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= e($bookRow['book_id']) ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

<?php elseif ($page === 'authors'): ?>
    <h2>Author Management</h2>
    <h3><?= $edit ? 'Update Author' : 'Add Author' ?></h3>
    <form method="post" action="index.php?page=authors">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= e($edit['author_id'] ?? '') ?>">
        <?php field('Author Name', 'author_name', $edit['author_name'] ?? '', 'text', true); ?>
        <p>Biography:<br><textarea name="biography" rows="4" cols="40"><?= e($edit['biography'] ?? '') ?></textarea></p>
        <button type="submit">Save Author</button>
    </form>
    <h3>All Authors</h3>
    <?php $authorRows = rows($conn, 'SELECT author_id, author_name, biography FROM authors ORDER BY author_id DESC'); ?>
    <?php if (!$authorRows): ?>
        <p>No authors found.</p>
    <?php else: ?>
        <table border="1" cellpadding="6"><tr><th>ID</th><th>Name</th><th>Biography</th><th>Actions</th></tr>
        <?php foreach ($authorRows as $authorRow): ?>
            <tr><td><?= e($authorRow['author_id']) ?></td><td><?= e($authorRow['author_name']) ?></td><td><?= e($authorRow['biography']) ?></td><td>
                <a href="?page=authors&action=edit&id=<?= e($authorRow['author_id']) ?>">Edit</a>
                <form method="post" action="index.php?page=authors" style="display:inline"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($authorRow['author_id']) ?>"><button type="submit">Delete</button></form>
            </td></tr>
        <?php endforeach; ?>
        </table>
    <?php endif; ?>

<?php elseif ($page === 'genres'): ?>
    <?php (new Genre($conn))->renderPage($edit, $_GET['keyword'] ?? null); ?>

<?php elseif ($page === 'members'): ?>
    <?php (new Member($conn))->renderPage($edit, $_GET['keyword'] ?? null); ?>

<?php elseif ($page === 'borrowings'): ?>
    <?php (new Borrowing($conn))->renderPage(); ?>

<?php elseif ($page === 'fees'): ?>
    <?php (new Fee($conn))->renderPage(); ?>

<?php elseif ($page === 'inventory'): ?>
    <?php (new BookInventory($conn))->renderPage(); ?>

<?php else: ?>
    <h2>Page not found</h2>
<?php endif; ?>

<?php if ($page !== 'dashboard'): ?>
    <hr>
    <p><a href="index.php">Return to Main Menu</a></p>
<?php endif; ?>
</body>
</html>
