
<?php
require_once "genre.php";
require_once "author.php";

class Book
{
    private PDO $conn;


    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Display available genres
   public function showGenres(): void
{
    $sql = "SELECT
                g.genre_id,
                g.genre_name,
                p.genre_name AS parent_genre
                FROM genres g
                LEFT JOIN genres p
                ON g.parent_genre_id = p.genre_id
                ORDER BY g.genre_id";

    $stmt = $this->conn->query($sql);
    $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\n----------------Genre list----------------\n";

    if (empty($genres)) {
        echo "No genres found.\n";
        return;
    }

    foreach ($genres as $genre) {
        echo "Genre Id: " . $genre['genre_id'] . "\n";
        echo "Genre Name: " . $genre['genre_name'] . "\n";
        echo "Parent Genre: "
            . ($genre['parent_genre'] ?? 'None') . "\n";
    }
}

    // Add a new book using a genre ID selected from the available genres.
    public function addBook(
        string $title,
        ?int $year,
        ?string $isbn,
        string $format,
        ?string $description,
        float $price,
        int $copies
    ): void {
        $this->showGenres();

        $genreIdInput = trim(readline("Enter genre ID: "));
        if ($genreIdInput === "" || !ctype_digit($genreIdInput)) {
            echo "Invalid genre ID. Please enter a numeric ID from the list.\n";
            return;
        }
        $genreId = (int) $genreIdInput;

        $check = $this->conn->prepare(
            "SELECT genre_name FROM genres WHERE genre_id = ?"
        );
        $check->execute([$genreId]);
        $genreName = $check->fetchColumn();

        if ($genreName === false) {
            echo "Invalid genre ID. Please select an available genre.\n";
            $this->showGenres();
            return;
        }

        $author = new Author($this->conn);
        $author->showAll();

        $authorInput = trim(readline("Enter an existing author ID or a new author name (blank to keep): "));
        $newAuthorName = null;
        $newAuthorBiography = null;
        $authorId = null;
        $authorName = null;
        if ($authorInput !== '' && ctype_digit($authorInput)) {
            $authorId = (int) $authorInput;
            $checkAuthor = $this->conn->prepare(
                "SELECT author_name FROM authors WHERE author_id = ?"
            );
            $checkAuthor->execute([$authorId]);
            $authorName = $checkAuthor->fetchColumn();

            if ($authorName === false) {
                echo "Invalid author ID. Please select an author from the list.\n";
                $author->showAll();
                return;
            }
        } elseif ($authorInput !== '') {
            if (strlen($authorInput) > 20) {
                echo "Author name must be 20 characters or fewer.\n";
                return;
            }
            $newAuthorName = $authorInput;
            $authorName = $newAuthorName;
            $biographyInput = trim(readline("New author biography (optional): "));
            $newAuthorBiography = $biographyInput === '' ? null : $biographyInput;
        }

        $this->conn->beginTransaction();

        try {
            if ($newAuthorName !== null) {
                $addAuthor = $this->conn->prepare(
                    "INSERT INTO authors (author_name, biography) VALUES (?, ?)"
                );
                $addAuthor->execute([$newAuthorName, $newAuthorBiography]);
                $authorId = (int) $this->conn->lastInsertId();
            }

            $stmt = $this->conn->prepare(
                "INSERT INTO books
                    (title, publication_year, isbn,
                     physical_format, description, price, genre_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $title,
                $year,
                $isbn,
                $format,
                $description,
                $price,
                $genreId
            ]);

            $bookId = (int) $this->conn->lastInsertId();
            if ($authorId !== null) {
                $link = $this->conn->prepare(
                    "INSERT INTO book_authors (book_id, author_id) VALUES (?, ?)"
                );
                $link->execute([$bookId, $authorId]);
            }

        if ($copies < 1) {
        throw new InvalidArgumentException(
            "Number of copies must be at least 1."
        );
        }

        $copyStmt = $this->conn->prepare(
            "INSERT INTO book_copies (book_id, status)
            VALUES (?, 'Available')"
        );

        for ($i = 0; $i < $copies; $i++) {
            $copyStmt->execute([$bookId]);
        }

         $this->conn->commit();
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }

        echo "Book added successfully!\n";
        echo "Genre: $genreName\n";
        echo "Genre ID: $genreId\n";
        echo "Author: " . ($authorName ?? 'Not assigned') . "\n";
        if ($authorId !== null) {
            echo "Author ID: $authorId\n";
        }
        echo "Book ID: $bookId\n\n";
        echo "Total copies added: " . $copies . "\n";
        echo "Available copies: " . $copies . "\n";
        
    }

    // Display all books
    public function showAll(): void
    {
        $sql = "SELECT b.*, g.genre_name,
                GROUP_CONCAT(a.author_name ORDER BY a.author_name SEPARATOR ', ')
                AS author_names
                FROM books b
                LEFT JOIN genres g ON b.genre_id = g.genre_id
                LEFT JOIN book_authors ba ON b.book_id = ba.book_id
                LEFT JOIN authors a ON ba.author_id = a.author_id
                GROUP BY b.book_id
                ORDER BY b.book_id DESC";

        $stmt = $this->conn->query($sql);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($books)) {
            echo "No books found.\n";
            return;
        }

        foreach ($books as $book) {
            echo "-------------------------\n";
            echo "Book ID: " . $book['book_id'] . "\n";
            echo "Title: " . $book['title'] . "\n";
            echo "Publication Year: " . $book['publication_year'] . "\n";
            echo "ISBN: " . $book['isbn'] . "\n";
            echo "Format: " . $book['physical_format'] . "\n";
            echo "Description: " . $book['description'] . "\n";
            echo "Price: " . $book['price'] . "\n";
            echo "Genre ID: " . $book['genre_id'] . "\n";
            echo "Genre Name: "
                . ($book['genre_name'] ?? 'Not assigned') . "\n";
            echo "Authors: "
                . ($book['author_names'] ?? 'Not assigned') . "\n";
        }
    }

    // Search books by title
    public function search(string $keyword): void
    {
        $sql = "SELECT b.book_id, b.title,
                       b.publication_year, b.price,
                       b.genre_id, g.genre_name
                FROM books b
                LEFT JOIN genres g ON b.genre_id = g.genre_id
                WHERE b.title LIKE :keyword
                ORDER BY b.title";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':keyword' => '%' . $keyword . '%'
        ]);

        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($books)) {
            echo "No matching books found.\n";
            return;
        }

        foreach ($books as $book) {
            echo "-------------------------\n";
            echo "Book ID: " . $book['book_id'] . "\n";
            echo "Title: " . $book['title'] . "\n";
            echo "Year: " . $book['publication_year'] . "\n";
            echo "Price: " . $book['price'] . "\n";
            echo "Genre ID: " . $book['genre_id'] . "\n";
            echo "Genre Name: ". ($book['genre_name'] ?? 'Not assigned') . "\n";
        }
    }

    // Update a book's details, keeping any field whose prompt is left blank.
    public function updateBook(int $book_id): void
    {
        $stmt = $this->conn->prepare(
            "SELECT b.*,
            GROUP_CONCAT(a.author_name ORDER BY a.author_name SEPARATOR ', ')
            AS author_names
             FROM books b
             LEFT JOIN book_authors ba ON b.book_id = ba.book_id
             LEFT JOIN authors a ON ba.author_id = a.author_id
             WHERE b.book_id = ?
             GROUP BY b.book_id"
        );
        $stmt->execute([$book_id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($book === false) {
            echo "Book ID not found.\n";
            return;
        }

        echo "Leave a field blank to keep its current value. Enter - to clear ISBN or description.\n";
        echo "Current title: " . $book['title'] . "\n";
        $title = trim(readline("New title: "));
        $title = $title === '' ? $book['title'] : $title;

        echo "Current publication year: " . ($book['publication_year'] ?? '') . "\n";
        $yearInput = trim(readline("New publication year: "));
        if ($yearInput !== '' && !ctype_digit($yearInput)) {
            echo "Invalid publication year. Book was not updated.\n";
            return;
        }
        $year = $yearInput === '' ? $book['publication_year'] : (int) $yearInput;

        echo "Current ISBN: " . ($book['isbn'] ?? '') . "\n";
        $isbnInput = trim(readline("New ISBN: "));
        $isbn = $isbnInput === '' ? $book['isbn'] : ($isbnInput === '-' ? null : $isbnInput);

        echo "Current physical format: " . $book['physical_format'] . "\n";
        $format = trim(readline("New physical format: "));
        $format = $format === '' ? $book['physical_format'] : $format;

        echo "Current description: " . ($book['description'] ?? '') . "\n";
        $descriptionInput = trim(readline("New description: "));
        $description = $descriptionInput === ''
            ? $book['description']
            : ($descriptionInput === '-' ? null : $descriptionInput);

        echo "Current price: " . $book['price'] . "\n";
        $priceInput = trim(readline("New price: "));
        if ($priceInput !== '' && (!is_numeric($priceInput) || (float) $priceInput < 0)) {
            echo "Invalid price. Enter a non-negative number. Book was not updated.\n";
            return;
        }
        $price = $priceInput === '' ? $book['price'] : (float) $priceInput;

        echo "Current genre ID: " . ($book['genre_id'] ?? '') . "\n";
        $this->showGenres();
        $genreInput = trim(readline("New genre ID (blank to keep): "));
        $genreId = $book['genre_id'];
        if ($genreInput !== '') {
            if (!ctype_digit($genreInput)) {
                echo "Invalid genre ID. Book was not updated.\n";
                return;
            }

            $genreId = (int) $genreInput;
            $check = $this->conn->prepare(
                "SELECT COUNT(*) FROM genres WHERE genre_id = ?"
            );
            $check->execute([$genreId]);
            if ((int) $check->fetchColumn() === 0) {
                echo "Genre ID not found. Book was not updated.\n";
                return;
            }
        }

        echo "Current author(s): " . ($book['author_names'] ?? 'Not assigned') . "\n";
        (new Author($this->conn))->showAll();
        $authorInput = trim(readline("Enter an existing author ID or a new author name (blank to keep): "));
        $authorId = null;
        $newAuthorName = null;
        $newAuthorBiography = null;
        if ($authorInput !== '' && !ctype_digit($authorInput)) {
            if (strlen($authorInput) > 20) {
                echo "Author name must be 20 characters or fewer. Book was not updated.\n";
                return;
            }
            $newAuthorName = $authorInput;
            $biographyInput = trim(readline("New author biography (optional): "));
            $newAuthorBiography = $biographyInput === '' ? null : $biographyInput;
        } elseif ($authorInput !== '') {
            $authorId = (int) $authorInput;
            $check = $this->conn->prepare(
                "SELECT COUNT(*) FROM authors WHERE author_id = ?"
            );
            $check->execute([$authorId]);
            if ((int) $check->fetchColumn() === 0) {
                echo "Author ID not found. Book was not updated.\n";
                return;
            }
        }

        $this->conn->beginTransaction();
        try {
            $update = $this->conn->prepare(
                "UPDATE books
                 SET title = :title,
                     publication_year = :year,
                     isbn = :isbn,
                     physical_format = :format,
                     description = :description,
                     price = :price,
                     genre_id = :genre_id
                 WHERE book_id = :book_id"
            );
            $update->execute([
                ':title' => $title,
                ':year' => $year,
                ':isbn' => $isbn,
                ':format' => $format,
                ':description' => $description,
                ':price' => $price,
                ':genre_id' => $genreId,
                ':book_id' => $book_id
            ]);

            if ($newAuthorName !== null) {
                $addNewAuthor = $this->conn->prepare(
                    "INSERT INTO authors (author_name, biography) VALUES (?, ?)"
                );
                $addNewAuthor->execute([$newAuthorName, $newAuthorBiography]);
                $authorId = (int) $this->conn->lastInsertId();
            }

            if ($authorId !== null) {
                $deleteAuthors = $this->conn->prepare(
                    "DELETE FROM book_authors WHERE book_id = ?"
                );
                $deleteAuthors->execute([$book_id]);

                $addAuthor = $this->conn->prepare(
                    "INSERT INTO book_authors (book_id, author_id) VALUES (?, ?)"
                );
                $addAuthor->execute([$book_id, $authorId]);
            }

            $this->conn->commit();
            echo "Book updated successfully.\n";
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }

    // Delete a book
    public function delete(int $book_id): void
    {
        $check = $this->conn->prepare(
            "SELECT COUNT(*) FROM borrowings WHERE book_id = ?"
        );
        $check->execute([$book_id]);

        if ((int)$check->fetchColumn() > 0) {
            echo "Cannot delete this book because borrowing records exist.\n";
            return;
        }

        $this->conn->beginTransaction();

        try {
            $stmt = $this->conn->prepare(
                "DELETE FROM book_authors WHERE book_id = ?"
            );
            $stmt->execute([$book_id]);

            //$stmt = $this->conn->prepare(
               // "DELETE FROM book_genres WHERE book_id = ?"
            //);
            //$stmt->execute([$book_id]);

            $stmt = $this->conn->prepare(
                "DELETE FROM books WHERE book_id = ?"
            );
            $stmt->execute([$book_id]);

            $deleted = $stmt->rowCount();

            $this->conn->commit();

            if ($deleted > 0) {
                echo "Book deleted successfully.\n";
            } else {
                echo "Book ID not found.\n";
            }
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }
}
