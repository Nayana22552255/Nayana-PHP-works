
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

    
    private function selectOrCreateGenre(): int
        {
            while (true) {
                echo "\n========== GENRE OPTIONS ==========\n";
                echo "1. Select Existing Genre\n";
                echo "2. Add New Genre\n";
                echo "0. Cancel\n";

                $choice = trim(readline("Choose an option: "));

                if ($choice === "0") {
                    throw new RuntimeException("Book addition cancelled.");
                }

                if ($choice === "1") {
                    $this->showGenres();

                    $input = trim(readline("Enter Genre ID: "));

                    if ($input === "" || !ctype_digit($input)) {
                        echo "Please enter a valid numeric Genre ID.\n";
                        continue;
                    }

                    $genreId = (int) $input;

                    $stmt = $this->conn->prepare(
                        "SELECT genre_id FROM genres WHERE genre_id = ?"
                    );
                    $stmt->execute([$genreId]);

                    if ($stmt->fetchColumn() !== false) {
                        return $genreId;
                    }

                    echo "Genre ID not found. Try again.\n";
                } elseif ($choice === "2") {
                    $name = trim(readline("Enter new genre name: "));

                    if ($name === "") {
                        echo "Genre name cannot be empty.\n";
                        continue;
                    }

                    // Check whether this genre already exists.
                    $stmt = $this->conn->prepare(
                        "SELECT genre_id
                        FROM genres
                        WHERE LOWER(TRIM(genre_name)) = LOWER(TRIM(?))"
                    );
                    $stmt->execute([$name]);

                    $existingId = $stmt->fetchColumn();

                    if ($existingId !== false) {
                        echo "This genre already exists. Genre ID: "
                            . $existingId . "\n";

                        return (int) $existingId;
                    }

                    // Choose a parent genre, if needed.
                    $this->showGenres();

                    $parentInput = trim(
                        readline("Enter parent Genre ID, or 0 for a main genre: ")
                    );

                    if ($parentInput === "" || !ctype_digit($parentInput)) {
                        echo "Invalid parent Genre ID.\n";
                        continue;
                    }

                    $parentId = (int) $parentInput;

                    if ($parentId !== 0) {
                        $stmt = $this->conn->prepare(
                            "SELECT genre_id
                            FROM genres
                            WHERE genre_id = ?"
                        );
                        $stmt->execute([$parentId]);

                        if ($stmt->fetchColumn() === false) {
                            echo "Parent Genre ID not found.\n";
                            continue;
                        }
                    }

                    $stmt = $this->conn->prepare(
                        "INSERT INTO genres (genre_name, parent_genre_id)
                        VALUES (?, ?)"
                    );

                    $stmt->execute([
                        $name,
                        $parentId === 0 ? null : $parentId
                    ]);

                    $newGenreId = (int) $this->conn->lastInsertId();

                    echo "New genre added successfully!\n";
                    echo "Genre ID: " . $newGenreId . "\n";

                    return $newGenreId;
                } else {
                    echo "Invalid option. Please try again.\n";
                }
            }
    }


        
        private function physicalFormats(): array
        {
            return [
                1 => 'Book',
                2 => 'Manga',
                3 => 'Magazine',
                4 => 'Comic',
                5 => 'Graphic Novel',
                6 => 'Textbook',
                7 => 'Reference Book',
                8 => 'Journal'
            ];
        }

        private function physicalFormatName(string $storedFormat): string
        {
            $formats = $this->physicalFormats();

            if (ctype_digit($storedFormat)) {
                return $formats[(int) $storedFormat] ?? $storedFormat;
            }

            return $storedFormat;
        }

        private function selectPhysicalFormat(bool $allowKeep = false): ?string
        {
            $formats = $this->physicalFormats();

            echo "\n========== PHYSICAL FORMAT ==========\n";

            if ($allowKeep) {
                echo "0. Keep current format\n";
            }

            foreach ($formats as $id => $name) {
                echo "$id. $name\n";
            }

            while (true) {
                $input = trim(readline(
                    $allowKeep
                        ? "Select physical format ID (blank or 0 to keep): "
                        : "Select physical format ID: "
                ));

                if ($allowKeep && ($input === '' || $input === '0')) {
                    return null;
                }

                if ($input !== '' && ctype_digit($input)) {
                    $formatId = (int) $input;

                    if (isset($formats[$formatId])) {
                        echo "Selected format: "
                            . $formats[$formatId] . "\n";

                        return $formats[$formatId];
                    }
                }

                echo "Invalid choice. Please select a valid format ID.\n";
            }
        }




    // Add a new book using a genre ID selected from the available genres.
    public function addBook(
        string $title,
        ?int $year,
        ?string $isbn,
        ?string $description,
        float $price,
        int $copies
    ): void 
    {


        
    $genreId = $this->selectOrCreateGenre();
    $selectedFormat = $this->selectPhysicalFormat();
    if ($selectedFormat === null) {
        return;
    }
    $format = $selectedFormat;

    // Get the selected or newly created genre name.
    $check = $this->conn->prepare(
        "SELECT genre_name FROM genres WHERE genre_id = ?"
    );
    $check->execute([$genreId]);
    $genreName = $check->fetchColumn();

    if ($genreName === false) {
        echo "Genre not found. Book was not added.\n";
        return;
    }

        

        $author = new Author($this->conn);
        $author->showAll();
        $authorChoices = [];
        $selectedAuthorIds = [];

        while (true) {
            $authorInput = trim(readline(
                "Enter an author ID, N to add a new author, or press Enter when finished: "
            ));

            if ($authorInput === '') {
                break;
            }

            if (strcasecmp($authorInput, 'N') === 0) {
                $newAuthorName = trim(readline("New author name: "));
                if ($newAuthorName === '' || strlen($newAuthorName) > 20) {
                    echo "Author name must contain 1 to 20 characters.\n";
                    continue;
                }

                $biographyInput = trim(readline("New author biography (optional): "));
                $authorChoices[] = [
                    'author_id' => null,
                    'author_name' => $newAuthorName,
                    'biography' => $biographyInput === '' ? null : $biographyInput
                ];
                echo "New author added to this book selection.\n";
                continue;
            }

            if (!ctype_digit($authorInput)) {
                echo "Enter a listed author ID, N for a new author, or Enter to finish.\n";
                continue;
            }

            $authorId = (int) $authorInput;
            if (isset($selectedAuthorIds[$authorId])) {
                echo "That author is already selected.\n";
                continue;
            }

            $checkAuthor = $this->conn->prepare(
                "SELECT author_name FROM authors WHERE author_id = ?"
            );
            $checkAuthor->execute([$authorId]);
            $authorName = $checkAuthor->fetchColumn();
            if ($authorName === false) {
                echo "Author ID $authorId was not found. Choose an ID from the list.\n";
                continue;
            }

            $authorChoices[] = [
                'author_id' => $authorId,
                'author_name' => $authorName,
                'biography' => null
            ];
            $selectedAuthorIds[$authorId] = true;
        }

        $this->conn->beginTransaction();

        try {
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
            $link = $this->conn->prepare(
                "INSERT INTO book_authors (book_id, author_id) VALUES (?, ?)"
            );
            $addAuthor = $this->conn->prepare(
                "INSERT INTO authors (author_name, biography) VALUES (?, ?)"
            );
            foreach ($authorChoices as $authorChoice) {
                $authorId = $authorChoice['author_id'];
                if ($authorId === null) {
                    $addAuthor->execute([
                        $authorChoice['author_name'],
                        $authorChoice['biography']
                    ]);
                    $authorId = (int) $this->conn->lastInsertId();
                }

                $link->execute([$bookId, $authorId]);
            }

            if ($copies < 1) {
                throw new InvalidArgumentException(
                    "Number of copies must be at least 1."
                );
            }

            $copyStmt = $this->conn->prepare(
                "INSERT INTO book_copies (book_id, status,inventory_date)
                VALUES (?, 'Available',CURDATE())"
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
        $authorNames = array_column($authorChoices, 'author_name');
        echo "Author(s): " . (empty($authorNames) ? 'Not assigned' : implode(', ', $authorNames)) . "\n";
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
            echo "Format: "
                . $this->physicalFormatName((string) $book['physical_format']) . "\n";
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
                       b.publication_year, b.physical_format, b.price,
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
            echo "Format: "
                . $this->physicalFormatName((string) $book['physical_format']) . "\n";
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
        while (true) {
            $yearInput = trim(readline(
                "New publication year:  "
            ));
            break;

        }


            //echo "Invalid year. Enter a whole number from 1990 to 2155, or press Enter to keep the current year.\n" }\
        $year = $yearInput === '' ? $book['publication_year'] : (int) $yearInput;

        echo "Current ISBN: " . ($book['isbn'] ?? '') . "\n";
        $isbnInput = trim(readline("New ISBN: "));
        $isbn = $isbnInput === '' ? $book['isbn'] : ($isbnInput === '-' ? null : $isbnInput);

        
        echo "Current physical format: "
            . $this->physicalFormatName((string) $book['physical_format']) . "\n";
        $formatInput = $this->selectPhysicalFormat(true);
        $format = $formatInput === null
            ? $this->physicalFormatName((string) $book['physical_format'])
            : $formatInput;


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
        echo "Press Enter or enter K to keep current authors; enter X to remove all authors, or add IDs/new authors to replace the author list.\n";
        (new Author($this->conn))->showAll();
        $authorChoices = [];
        $selectedAuthorIds = [];
        $replaceAuthors = false;
        $authorInput = trim(readline(
            "Author ID, N for a new author, K/Enter to keep, or X to remove all: "
        ));

        if ($authorInput !== '' && strcasecmp($authorInput, 'K') !== 0) {
            $replaceAuthors = true;

            while ($authorInput !== '' && strcasecmp($authorInput, 'X') !== 0) {
                if (strcasecmp($authorInput, 'N') === 0) {
                    $newAuthorName = trim(readline("New author name: "));
                    if ($newAuthorName === '' || strlen($newAuthorName) > 20) {
                        echo "Author name must contain 1 to 20 characters.\n";
                    } else {
                        $biographyInput = trim(readline("New author biography (optional): "));
                        $authorChoices[] = [
                            'author_id' => null,
                            'author_name' => $newAuthorName,
                            'biography' => $biographyInput === '' ? null : $biographyInput
                        ];
                        echo "New author added to the selection.\n";
                    }
                } elseif (ctype_digit($authorInput)) {
                    $authorId = (int) $authorInput;
                    if (isset($selectedAuthorIds[$authorId])) {
                        echo "That author is already selected.\n";
                    } else {
                        $check = $this->conn->prepare(
                            "SELECT author_name FROM authors WHERE author_id = ?"
                        );
                        $check->execute([$authorId]);
                        $authorName = $check->fetchColumn();
                        if ($authorName === false) {
                            echo "Author ID $authorId was not found. Choose an ID from the list.\n";
                        } else {
                            $authorChoices[] = [
                                'author_id' => $authorId,
                                'author_name' => $authorName,
                                'biography' => null
                            ];
                            $selectedAuthorIds[$authorId] = true;
                        }
                    }
                } else {
                    echo "Enter a listed author ID, N for a new author, X to remove all, or Enter to finish.\n";
                }

                $authorInput = trim(readline(
                    "Add another author ID or N for a new author (Enter to finish): "
                ));
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

            if ($replaceAuthors) {
                $deleteAuthors = $this->conn->prepare(
                    "DELETE FROM book_authors WHERE book_id = ?"
                );
                $deleteAuthors->execute([$book_id]);

                $addNewAuthor = $this->conn->prepare(
                    "INSERT INTO authors (author_name, biography) VALUES (?, ?)"
                );
                $addAuthorLink = $this->conn->prepare(
                    "INSERT INTO book_authors (book_id, author_id) VALUES (?, ?)"
                );

                foreach ($authorChoices as $authorChoice) {
                    $authorId = $authorChoice['author_id'];
                    if ($authorId === null) {
                        $addNewAuthor->execute([
                            $authorChoice['author_name'],
                            $authorChoice['biography']
                        ]);
                        $authorId = (int) $this->conn->lastInsertId();
                    }

                    $addAuthorLink->execute([$book_id, $authorId]);
                }
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

            $stmt = $this->conn->prepare(
                "DELETE FROM book_copies WHERE book_id = ?"
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
