
<?php

class BookInventory
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function menu(): void
    {
        while (true) {
            echo "\n========== BOOK INVENTORY ==========\n";
            echo "1. Show all books with inventory date\n";
            echo "2. Show all copies of a particular book\n";
            echo "3. Show all currently borrowed books\n";
            echo "4. Show all members who have taken a particular book\n";
            echo "5. Show all fines associated with a book\n";
            echo "6. Show all lost books\n";
            echo "0. Back to main menu\n";

            $choice = trim(readline("Enter your choice: "));

            switch ($choice) {
                case '1':
                            $this->showAllBooks();
                            break;

                case '2':
                            $this->showBookCopies();
                            break;

                case '3':
                            $this->showBorrowedBooks();
                            break;

                case '4':
                            $this->showMembersByBook();
                            break;

                case '5':
                            $this->showFinesByBook();
                            break;

                case '6':
                            $this->showLostBooks();
                            break;

                case '0':
                           return;

                default:
                    echo "Invalid choice. Please try again.\n";
            }
        }
    }

    // 1. Show all books with copy counts and inventory date
    public function showAllBooks(): void
    {
        try {
            $books = $this->getAllBooksInventory();

            if (!$books) {
                echo "No books found.\n";
                return;
            }

            echo "\n========== ALL BOOKS ==========\n";

            foreach ($books as $book) {
                echo "\nBook ID: " . $book['book_id'] . "\n";
                echo "Title: " . $book['title'] . "\n";
                echo "Publication Year: ". ($book['publication_year'] ?? 'N/A') . "\n";
                echo "ISBN: " . ($book['isbn'] ?? 'N/A') . "\n";
                echo "Physical Format: ". ($book['physical_format'] ?? 'N/A') . "\n";
                echo "Genre: " . ($book['genre_name'] ?? 'N/A') . "\n";
                echo "Total Copies: " . $book['total_copies'] . "\n";
                echo "Available Copies: ". $book['available_copies'] . "\n";
                echo "Borrowed Copies: ". $book['borrowed_copies'] . "\n";
                echo "Lost Copies: " . $book['lost_copies'] . "\n";
                echo "First Inventory Date: ". ($book['first_inventory_date'] ?? 'Not recorded'). "\n";
                echo "--------------------------------\n";
            }
        } catch (PDOException $e) {
            echo "Error showing books: " . $e->getMessage() . "\n";
        }
    }

    private function getAllBooksInventory(bool $availableOnly = false): array
    {
        $sql = "SELECT
                    b.book_id,
                    b.title,
                    b.publication_year,
                    b.isbn,
                    b.physical_format,
                    g.genre_name,
                    COALESCE(c.total_copies, 0) AS total_copies,
                    COALESCE(c.available_copies, 0) AS available_copies,
                    COALESCE(c.borrowed_copies, 0) AS borrowed_copies,
                    COALESCE(c.lost_copies, 0) AS lost_copies,
                    c.first_inventory_date
                    FROM books b
                    LEFT JOIN genres g
                    ON b.genre_id = g.genre_id
                    LEFT JOIN (
                    SELECT
                        book_id,
                        COUNT(*) AS total_copies,
                        SUM(status = 'Available') AS available_copies,
                        SUM(status = 'Borrowed') AS borrowed_copies,
                        SUM(status = 'Lost') AS lost_copies,
                        MIN(inventory_date) AS first_inventory_date
                    FROM book_copies
                    GROUP BY book_id)
                    c ON b.book_id = c.book_id
                    " . ($availableOnly ? "WHERE COALESCE(c.available_copies, 0) > 0 " : "") . "
                    ORDER BY b.book_id ";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function addCopiesToBook(int $bookId, int $quantity): void
    {
        if ($bookId < 1 || $quantity < 1) {
            throw new InvalidArgumentException('Select a book and enter a positive copy quantity.');
        }

        $check = $this->conn->prepare(
            "SELECT COUNT(*)
             FROM book_copies
             WHERE book_id = ? AND status = 'Available'"
        );
        $check->execute([$bookId]);
        if ((int) $check->fetchColumn() < 1) {
            throw new InvalidArgumentException(
                'Copies can only be added to a book currently listed as available.'
            );
        }

        try {
            $this->conn->beginTransaction();
            $insert = $this->conn->prepare(
                "INSERT INTO book_copies (book_id, status, inventory_date)
                 VALUES (?, 'Available', CURDATE())"
            );
            for ($i = 0; $i < $quantity; $i++) {
                $insert->execute([$bookId]);
            }
            $this->conn->commit();
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    // 2. Show every copy belonging to a particular book
    public function showBookCopies(?int $selectedBookId = null): void
    {
        $bookId = $this->readBookId($selectedBookId);

        if ($bookId === null) {
            return;
        }

        try {
            $copies = $this->getBookCopies($bookId);

            if (!$copies) {
                echo "No copies found for this book.\n";
                return;
            }

            echo "\n========== BOOK COPIES ==========\n";
            echo "Book: " . $copies[0]['title'] . "\n";
            echo "Book ID: " . $copies[0]['book_id'] . "\n";

            foreach ($copies as $copy) {
                echo "\nCopy ID: " . $copy['copy_id'] . "\n";
                echo "Status: " . $copy['status'] . "\n";
                echo "Inventory Date: ". ($copy['inventory_date'] ?? 'Not recorded'). "\n";
                echo "--------------------------------\n";
            }
        } catch (PDOException $e) {
            echo "Error showing book copies: "
                . $e->getMessage() . "\n";
        }
    }

    private function getBookCopies(int $bookId): array
    {
        $sql = "SELECT b.book_id, b.title, bc.copy_id, bc.status, bc.inventory_date
                FROM books b
                INNER JOIN book_copies bc ON b.book_id = bc.book_id
                WHERE b.book_id = ?
                ORDER BY bc.copy_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$bookId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Show books that are currently borrowed and not returned
    public function showBorrowedBooks(): void
    {
        try {
            $borrowings = $this->getBorrowedBooks();

            if (!$borrowings) {
                echo "No books are currently borrowed.\n";
                return;
            }

            echo "\n========== CURRENTLY BORROWED BOOKS ==========\n";

            foreach ($borrowings as $row) {
                echo "\nBorrowing ID: " . $row['borrowing_id'] . "\n";
                echo "Book ID: " . $row['book_id'] . "\n";
                echo "Book Title: " . $row['title'] . "\n";
                echo "Copy ID: " . ($row['copy_id'] ?? 'N/A') . "\n";
                echo "Member ID: " . $row['member_id'] . "\n";
                echo "Member Name: " . $row['member_name'] . "\n";
                echo "Borrow Date: " . $row['borrow_date'] . "\n";
                echo "Due Date: " . $row['due_date'] . "\n";
                echo "--------------------------------\n";
            }
        } catch (PDOException $e) {
            echo "Error showing borrowed books: "
                . $e->getMessage() . "\n";
        }
    }

    private function getBorrowedBooks(): array
    {
        $sql = "SELECT
                    br.borrowing_id,
                    b.book_id,
                    b.title,
                    bc.copy_id,
                    m.member_id,
                    m.name AS member_name,
                    br.borrow_date,
                    br.due_date
                FROM borrowings br
                INNER JOIN books b
                ON br.book_id = b.book_id
                INNER JOIN members m
                ON br.member_id = m.member_id
                LEFT JOIN book_copies bc
                ON br.copy_id = bc.copy_id
                WHERE br.return_date IS NULL
                ORDER BY br.due_date
            ";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Show members who have borrowed a particular book,
    // including previous borrowing records
    public function showMembersByBook(?int $selectedBookId = null): void
    {
        $bookId = $this->readBookId($selectedBookId);

        if ($bookId === null) {
            return;
        }

        try {
            $records = $this->getMembersByBook($bookId);

            if (!$records) {
                echo "No borrowing history found for this book.\n";
                return;
            }

            echo "\n========== MEMBERS BY BOOK ==========\n";
            echo "Book Title: " . $records[0]['title'] . "\n";

            foreach ($records as $row) {
                echo "\nBorrowing ID: " . $row['borrowing_id'] . "\n";
                echo "Member ID: " . $row['member_id'] . "\n";
                echo "Member Name: " . $row['member_name'] . "\n";
                echo "Email: " . ($row['email'] ?? 'N/A') . "\n";
                echo "Borrow Date: " . $row['borrow_date'] . "\n";
                echo "Due Date: " . $row['due_date'] . "\n";
                echo "Return Date: "
                    . ($row['return_date'] ?? 'Not returned') . "\n";
                echo "--------------------------------\n";
            }
        } catch (PDOException $e) {
            echo "Error showing members: " . $e->getMessage() . "\n";
        }
    }

    private function getMembersByBook(int $bookId): array
    {
        $sql = "SELECT
                    b.title,
                    br.borrowing_id,
                    m.member_id,
                    m.name AS member_name,
                    m.email,
                    br.borrow_date,
                    br.due_date,
                    br.return_date
                FROM borrowings br
                INNER JOIN books b
                ON br.book_id = b.book_id
                INNER JOIN members m
                ON br.member_id = m.member_id
                WHERE b.book_id = ?
                ORDER BY br.borrow_date DESC
            ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$bookId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. Show fines associated with a particular book
    public function showFinesByBook(?int $selectedBookId = null): void
    {
        $bookId = $this->readBookId($selectedBookId);

        if ($bookId === null) {
            return;
        }

        try {
            $fines = $this->getFinesByBook($bookId);

            if (!$fines) {
                echo "No fines found for this book.\n";
                return;
            }

            $totalFines = 0;
            $totalPending = 0;
            $totalPaid = 0;

            echo "\n========== FINES BY BOOK ==========\n";
            echo "Book Title: " . $fines[0]['title'] . "\n";

            foreach ($fines as $fine) {
                $amount = (float) $fine['amount'];
                $totalFines += $amount;

                if ($fine['payment_status'] === 'pending') {
                    $totalPending += $amount;
                } elseif ($fine['payment_status'] === 'paid') {
                    $totalPaid += $amount;
                }

                echo "\nFee ID: " . $fine['fee_id'] . "\n";
                echo "Borrowing ID: " . $fine['borrowing_id'] . "\n";
                echo "Member ID: " . $fine['member_id'] . "\n";
                echo "Member Name: " . $fine['member_name'] . "\n";
                echo "Fee Type: " . $fine['fee_type'] . "\n";
                echo "Amount: " . number_format($amount, 2) . "\n";
                echo "Fee Date: " . $fine['fee_date'] . "\n";
                echo "Payment Status: " . $fine['payment_status'] . "\n";
                echo "Payment Date: "
                    . ($fine['payment_date'] ?? 'N/A') . "\n";
                echo "--------------------------------\n";
            }

            echo "\n========== FINE SUMMARY ==========\n";
            echo "Total Fines: " . number_format($totalFines, 2) . "\n";
            echo "Pending Fines: " . number_format($totalPending, 2) . "\n";
            echo "Paid Fines: " . number_format($totalPaid, 2) . "\n";
        } catch (PDOException $e) {
            echo "Error showing fines: " . $e->getMessage() . "\n";
        }
    }

    private function getFinesByBook(int $bookId): array
    {
        $sql = "SELECT
                    b.title,
                    br.borrowing_id,
                    m.member_id,
                    m.name AS member_name,
                    f.fee_id,
                    f.fee_type,
                    f.amount,
                    f.fee_date,
                    f.payment_status,
                    f.payment_date
                FROM fees f
                INNER JOIN borrowings br
                ON f.borrowing_id = br.borrowing_id
                INNER JOIN books b
                ON br.book_id = b.book_id
                INNER JOIN members m
                ON br.member_id = m.member_id
                WHERE b.book_id = ?
                ORDER BY f.fee_date DESC
            ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$bookId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 6. Show all lost copies with their most recent borrowing details
    public function showLostBooks(): void
    {
        try {
            $lostBooks = $this->getLostBooks();

            if (!$lostBooks) {
                echo "No lost books found.\n";
                return;
            }

            echo "\n========== LOST BOOKS ==========\n";

            foreach ($lostBooks as $book) {
                echo "\nBook ID: " . $book['book_id'] . "\n";
                echo "Book Title: " . $book['title'] . "\n";
                echo "Copy ID: " . $book['copy_id'] . "\n";
                echo "Inventory Date: ". ($book['inventory_date'] ?? 'Not recorded') . "\n";
                echo "Borrowing ID: ". ($book['borrowing_id'] ?? 'N/A') . "\n";
                echo "Member ID: " . ($book['member_id'] ?? 'N/A') . "\n";
                echo "Member Name: ". ($book['member_name'] ?? 'N/A') . "\n";
                echo "Borrow Date: ". ($book['borrow_date'] ?? 'N/A') . "\n";
                echo "Due Date: ". ($book['due_date'] ?? 'N/A') . "\n";
                echo "Recorded Lost Fee: ". number_format((float) ($book['lost_fee'] ?? 0), 2). "\n";
                echo "--------------------------------\n";
            }
        } catch (PDOException $e) {
            echo "Error showing lost books: " . $e->getMessage() . "\n";
        }
    }

    private function getLostBooks(): array
    {
        $sql = "SELECT
                    b.book_id,
                    b.title,
                    bc.copy_id,
                    bc.inventory_date,
                    br.borrowing_id,
                    br.member_id,
                    m.name AS member_name,
                    br.borrow_date,
                    br.due_date,
                    br.lost_fee
                FROM book_copies bc
                INNER JOIN books b
                ON bc.book_id = b.book_id
                LEFT JOIN borrowings br
                ON br.borrowing_id = (
                SELECT MAX(br2.borrowing_id)
                FROM borrowings br2
                WHERE br2.copy_id = bc.copy_id)
                LEFT JOIN members m
                ON br.member_id = m.member_id
                WHERE bc.status = 'Lost'
                ORDER BY b.title, bc.copy_id
            ";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }



    
    // Display the inventory features as a browser page with HTML forms.
    public function renderPage(): void
    {
        $result = '';
        $showAllBooksTable = false;
        $availableBooksTable = null;
        $availableBooksTableError = '';
        $availableBooksMessage = '';
        $copiesForTable = null;
        $copiesTableError = '';
        $borrowingsForTable = null;
        $borrowingsTableError = '';
        $membersForBookTable = null;
        $membersForBookError = '';
        $finesForBookTable = null;
        $finesForBookError = '';
        $lostBooksForTable = null;
        $lostBooksTableError = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['inventory_action'] ?? '';
            $bookId = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);

            if ($action === 'all_books') {
                $showAllBooksTable = true;
            } elseif ($action === 'available_books') {
                try {
                    $availableBooksTable = $this->getAllBooksInventory(true);
                } catch (PDOException $e) {
                    $availableBooksTableError = 'Error showing available books: ' . $e->getMessage();
                }
            } elseif ($action === 'add_copies') {
                $quantity = filter_var(
                    $_POST['copy_quantity'] ?? null,
                    FILTER_VALIDATE_INT
                );
                try {
                    if (!$bookId || $bookId < 1 || !$quantity || $quantity < 1) {
                        throw new InvalidArgumentException(
                            'Select a valid book and enter a positive number of copies.'
                        );
                    }
                    $this->addCopiesToBook($bookId, $quantity);
                    $availableBooksMessage = $quantity . ' copy/copies added successfully.';
                    $availableBooksTable = $this->getAllBooksInventory(true);
                } catch (Throwable $e) {
                    $availableBooksTableError = 'Unable to add copies: ' . $e->getMessage();
                }
            } elseif ($action === 'borrowed_books') {
                try {
                    $borrowingsForTable = $this->getBorrowedBooks();
                } catch (PDOException $e) {
                    $borrowingsTableError = 'Error showing borrowed books: ' . $e->getMessage();
                }
            } elseif ($action === 'book_copies') {
                if (!$bookId || $bookId < 1) {
                    $copiesTableError = 'Please select a valid book.';
                } else {
                    try {
                        $copiesForTable = $this->getBookCopies($bookId);
                    } catch (PDOException $e) {
                        $copiesTableError = 'Error showing book copies: ' . $e->getMessage();
                    }
                }
            } elseif ($action === 'members_by_book') {
                if (!$bookId || $bookId < 1) {
                    $membersForBookError = 'Please select a valid book.';
                } else {
                    try {
                        $membersForBookTable = $this->getMembersByBook($bookId);
                    } catch (PDOException $e) {
                        $membersForBookError = 'Error showing members: ' . $e->getMessage();
                    }
                }
            } elseif ($action === 'fines_by_book') {
                if (!$bookId || $bookId < 1) {
                    $finesForBookError = 'Please select a valid book.';
                } else {
                    try {
                        $finesForBookTable = $this->getFinesByBook($bookId);
                    } catch (PDOException $e) {
                        $finesForBookError = 'Error showing fines: ' . $e->getMessage();
                    }
                }
            } elseif ($action === 'lost_books') {
                try {
                    $lostBooksForTable = $this->getLostBooks();
                } catch (PDOException $e) {
                    $lostBooksTableError = 'Error showing lost books: ' . $e->getMessage();
                }
            } else {
                ob_start();

                switch ($action) {
                    default:
                        echo "Please select a valid inventory option.";
                }

                $result = ob_get_clean();
            }
        }

        $books = $this->conn->query(
            "SELECT book_id, title FROM books ORDER BY title"
        )->fetchAll(PDO::FETCH_ASSOC);

        echo '<h2>Book Inventory Management</h2>';

        echo '<h3>1. Show all books with inventory date</h3>';
        echo '<form method="post">';
        echo '<input type="hidden" name="inventory_action" value="all_books">';
        echo '<button type="submit">Show All Books</button>';
        echo '</form>';

        echo '<h3>2. View all available books</h3>';
        echo '<form method="post">';
        echo '<input type="hidden" name="inventory_action" value="available_books">';
        echo '<button type="submit">View Available Books</button>';
        echo '</form>';

        echo '<h3>3. Show all copies of a particular book</h3>';
        $this->renderBookSelectForm('book_copies', $books);

        echo '<h3>4. Show all currently borrowed books</h3>';
        echo '<form method="post">';
        echo '<input type="hidden" name="inventory_action" value="borrowed_books">';
        echo '<button type="submit">Show Borrowed Books</button>';
        echo '</form>';

        echo '<h3>5. Show all members who have taken a particular book</h3>';
        $this->renderBookSelectForm('members_by_book', $books);

        echo '<h3>6. Show all fines associated with a book</h3>';
        $this->renderBookSelectForm('fines_by_book', $books);

        echo '<h3>7. Show all lost books</h3>';
        echo '<form method="post">';
        echo '<input type="hidden" name="inventory_action" value="lost_books">';
        echo '<button type="submit">Show Lost Books</button>';
        echo '</form>';

        if ($availableBooksTable !== null) {
            echo '<hr><h3>Available Books</h3>';
            if ($availableBooksMessage !== '') {
                echo '<p>' . htmlspecialchars($availableBooksMessage, ENT_QUOTES, 'UTF-8') . '</p>';
            }
            if (!$availableBooksTable) {
                echo '<p>No books are currently available.</p>';
            } else {
                echo '<table border="1" cellpadding="6" cellspacing="0">';
                echo '<tr><th>Book ID</th><th>Title</th><th>Publication Year</th><th>ISBN</th><th>Physical Format</th><th>Genre</th><th>Total Copies</th><th>Available Copies</th><th>Add Copies</th></tr>';
                foreach ($availableBooksTable as $book) {
                    echo '<tr>';
                    foreach ([
                        $book['book_id'],
                        $book['title'],
                        $book['publication_year'] ?? 'N/A',
                        $book['isbn'] ?? 'N/A',
                        $book['physical_format'] ?? 'N/A',
                        $book['genre_name'] ?? 'N/A',
                        $book['total_copies'],
                        $book['available_copies']
                    ] as $value) {
                        echo '<td>' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '</td>';
                    }
                    echo '<td><form method="post" style="margin:0">';
                    echo '<input type="hidden" name="inventory_action" value="add_copies">';
                    echo '<input type="hidden" name="book_id" value="' . (int)$book['book_id'] . '">';
                    echo '<input type="number" name="copy_quantity" min="1" value="1" required>';
                    echo ' <button type="submit">Add</button></form></td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        } elseif ($availableBooksTableError !== '') {
            echo '<hr><h3>Inventory Results</h3>';
            echo '<p>' . htmlspecialchars($availableBooksTableError, ENT_QUOTES, 'UTF-8') . '</p>';
        } elseif ($borrowingsForTable !== null) {
            echo '<hr><h3>Inventory Results</h3>';
            if (!$borrowingsForTable) {
                echo '<p>No books are currently borrowed.</p>';
            } else {
                echo '<table border="1" cellpadding="6" cellspacing="0">';
                echo '<tr><th>Borrowing ID</th><th>Book ID</th><th>Book Title</th><th>Copy ID</th><th>Member ID</th><th>Member Name</th><th>Borrow Date</th><th>Due Date</th></tr>';
                foreach ($borrowingsForTable as $row) {
                    echo '<tr>';
                    foreach ([
                        $row['borrowing_id'],
                        $row['book_id'],
                        $row['title'],
                        $row['copy_id'] ?? 'N/A',
                        $row['member_id'],
                        $row['member_name'],
                        $row['borrow_date'],
                        $row['due_date']
                    ] as $value) {
                        echo '<td>' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</table>';
            }
        } elseif ($borrowingsTableError !== '') {
            echo '<hr><h3>Inventory Results</h3>';
            echo '<p>' . htmlspecialchars($borrowingsTableError, ENT_QUOTES, 'UTF-8') . '</p>';
        } elseif ($copiesForTable !== null) {
            echo '<hr><h3>Inventory Results</h3>';
            if (!$copiesForTable) {
                echo '<p>No copies found for this book.</p>';
            } else {
                echo '<p>Book: ' . htmlspecialchars((string)$copiesForTable[0]['title'], ENT_QUOTES, 'UTF-8') . '</p>';
                echo '<p>Book ID: ' . (int)$copiesForTable[0]['book_id'] . '</p>';
                echo '<table border="1" cellpadding="6" cellspacing="0">';
                echo '<tr><th>Copy ID</th><th>Status</th><th>Inventory Date</th></tr>';
                foreach ($copiesForTable as $copy) {
                    echo '<tr>';
                    echo '<td>' . (int)$copy['copy_id'] . '</td>';
                    echo '<td>' . htmlspecialchars((string)$copy['status'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars((string)($copy['inventory_date'] ?? 'Not recorded'), ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        } elseif ($copiesTableError !== '') {
            echo '<hr><h3>Inventory Results</h3>';
            echo '<p>' . htmlspecialchars($copiesTableError, ENT_QUOTES, 'UTF-8') . '</p>';
        } elseif ($membersForBookTable !== null) {
            echo '<hr><h3>Inventory Results</h3>';
            if (!$membersForBookTable) {
                echo '<p>No borrowing history found for this book.</p>';
            } else {
                echo '<p>Book Title: ' . htmlspecialchars((string)$membersForBookTable[0]['title'], ENT_QUOTES, 'UTF-8') . '</p>';
                echo '<table border="1" cellpadding="6" cellspacing="0">';
                echo '<tr><th>Borrowing ID</th><th>Member ID</th><th>Member Name</th><th>Email</th><th>Borrow Date</th><th>Due Date</th><th>Return Date</th></tr>';
                foreach ($membersForBookTable as $row) {
                    echo '<tr>';
                    foreach ([
                        $row['borrowing_id'],
                        $row['member_id'],
                        $row['member_name'],
                        $row['email'] ?? 'N/A',
                        $row['borrow_date'],
                        $row['due_date'],
                        $row['return_date'] ?? 'Not returned'
                    ] as $value) {
                        echo '<td>' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</table>';
            }
        } elseif ($membersForBookError !== '') {
            echo '<hr><h3>Inventory Results</h3>';
            echo '<p>' . htmlspecialchars($membersForBookError, ENT_QUOTES, 'UTF-8') . '</p>';
        } elseif ($finesForBookTable !== null) {
            echo '<hr><h3>Inventory Results</h3>';
            if (!$finesForBookTable) {
                echo '<p>No fines found for this book.</p>';
            } else {
                $totalFines = 0.0;
                $totalPending = 0.0;
                $totalPaid = 0.0;
                echo '<p>Book Title: ' . htmlspecialchars((string)$finesForBookTable[0]['title'], ENT_QUOTES, 'UTF-8') . '</p>';
                echo '<table border="1" cellpadding="6" cellspacing="0">';
                echo '<tr><th>Fee ID</th><th>Borrowing ID</th><th>Member ID</th><th>Member Name</th><th>Fee Type</th><th>Amount</th><th>Fee Date</th><th>Payment Status</th><th>Payment Date</th></tr>';
                foreach ($finesForBookTable as $fine) {
                    $amount = (float)$fine['amount'];
                    $totalFines += $amount;
                    if ($fine['payment_status'] === 'pending') {
                        $totalPending += $amount;
                    } elseif ($fine['payment_status'] === 'paid') {
                        $totalPaid += $amount;
                    }

                    echo '<tr>';
                    foreach ([
                        $fine['fee_id'],
                        $fine['borrowing_id'],
                        $fine['member_id'],
                        $fine['member_name'],
                        $fine['fee_type'],
                        number_format($amount, 2),
                        $fine['fee_date'],
                        $fine['payment_status'],
                        $fine['payment_date'] ?? 'N/A'
                    ] as $value) {
                        echo '<td>' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</table>';
                echo '<h4>Fine Summary</h4>';
                echo '<table border="1" cellpadding="6" cellspacing="0">';
                echo '<tr><th>Total Fines</th><th>Pending Fines</th><th>Paid Fines</th></tr>';
                echo '<tr><td>' . number_format($totalFines, 2) . '</td><td>' . number_format($totalPending, 2) . '</td><td>' . number_format($totalPaid, 2) . '</td></tr>';
                echo '</table>';
            }
        } elseif ($finesForBookError !== '') {
            echo '<hr><h3>Inventory Results</h3>';
            echo '<p>' . htmlspecialchars($finesForBookError, ENT_QUOTES, 'UTF-8') . '</p>';
        } elseif ($lostBooksForTable !== null) {
            echo '<hr><h3>Inventory Results</h3>';
            if (!$lostBooksForTable) {
                echo '<p>No lost books found.</p>';
            } else {
                echo '<table border="1" cellpadding="6" cellspacing="0">';
                echo '<tr><th>Book ID</th><th>Book Title</th><th>Copy ID</th><th>Inventory Date</th><th>Borrowing ID</th><th>Member ID</th><th>Member Name</th><th>Borrow Date</th><th>Due Date</th><th>Recorded Lost Fee</th></tr>';
                foreach ($lostBooksForTable as $book) {
                    echo '<tr>';
                    foreach ([
                        $book['book_id'],
                        $book['title'],
                        $book['copy_id'],
                        $book['inventory_date'] ?? 'Not recorded',
                        $book['borrowing_id'] ?? 'N/A',
                        $book['member_id'] ?? 'N/A',
                        $book['member_name'] ?? 'N/A',
                        $book['borrow_date'] ?? 'N/A',
                        $book['due_date'] ?? 'N/A',
                        number_format((float)($book['lost_fee'] ?? 0), 2)
                    ] as $value) {
                        echo '<td>' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</table>';
            }
        } elseif ($lostBooksTableError !== '') {
            echo '<hr><h3>Inventory Results</h3>';
            echo '<p>' . htmlspecialchars($lostBooksTableError, ENT_QUOTES, 'UTF-8') . '</p>';
        } elseif ($showAllBooksTable) {
            echo '<hr><h3>Inventory Results</h3>';
            try {
                $books = $this->getAllBooksInventory();
                if (!$books) {
                    echo '<p>No books found.</p>';
                } else {
                    echo '<table border="1" cellpadding="6" cellspacing="0">';
                    echo '<tr><th>Book ID</th><th>Title</th><th>Publication Year</th><th>ISBN</th><th>Physical Format</th><th>Genre</th><th>Total Copies</th><th>Available Copies</th><th>Borrowed Copies</th><th>Lost Copies</th><th>First Inventory Date</th></tr>';
                    foreach ($books as $book) {
                        echo '<tr>';
                        foreach ([
                            $book['book_id'],
                            $book['title'],
                            $book['publication_year'] ?? 'N/A',
                            $book['isbn'] ?? 'N/A',
                            $book['physical_format'] ?? 'N/A',
                            $book['genre_name'] ?? 'N/A',
                            $book['total_copies'],
                            $book['available_copies'],
                            $book['borrowed_copies'],
                            $book['lost_copies'],
                            $book['first_inventory_date'] ?? 'Not recorded'
                        ] as $value) {
                            echo '<td>' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '</td>';
                        }
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            } catch (PDOException $e) {
                echo '<p>Error showing books: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
            }
        } elseif ($result !== '') {
            echo '<hr><h3>Inventory Results</h3>';
            echo '<pre>' . htmlspecialchars($result, ENT_QUOTES, 'UTF-8') . '</pre>';
        }
    }

    private function renderBookSelectForm(string $action, array $books): void
    {
        echo '<form method="post">';
        echo '<input type="hidden" name="inventory_action" value="' .
            htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . '">';
        echo '<label>Select Book: </label>';
        echo '<select name="book_id" required>';
        echo '<option value="">-- Select a book --</option>';

        foreach ($books as $book) {
            echo '<option value="' . (int) $book['book_id'] . '">' .
                htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') .
                ' (ID: ' . (int) $book['book_id'] . ')</option>';
        }

        echo '</select> ';
        echo '<button type="submit">Show Details</button>';
        echo '</form>';
    }

    // Helper method to validate a book ID
    private function readBookId(?int $selectedBookId = null): ?int
    {
        if ($selectedBookId !== null) {
            $input = (string) $selectedBookId;
        } else {
            $input = trim(readline("Enter book ID: "));
        }

        if ($input === '' || !ctype_digit($input) || (int) $input < 1) {
            echo "Invalid book ID.\n";
            return null;
        }

        $bookId = (int) $input;

        $stmt = $this->conn->prepare(
            "SELECT book_id FROM books WHERE book_id = ?"
        );
        $stmt->execute([$bookId]);

        if (!$stmt->fetchColumn()) {
            echo "Book ID not found.\n";
            return null;
        }

        return $bookId;
    }
}
