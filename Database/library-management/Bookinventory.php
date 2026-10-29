
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
                    ORDER BY b.book_id ";

            $stmt = $this->conn->query($sql);
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    // 2. Show every copy belonging to a particular book
    public function showBookCopies(): void
    {
        $bookId = $this->readBookId();

        if ($bookId === null) {
            return;
        }

        try {
            $sql = "SELECT
                    b.book_id,
                    b.title,
                    bc.copy_id,
                    bc.status,
                    bc.inventory_date
                FROM books b
                INNER JOIN book_copies bc
                ON b.book_id = bc.book_id
                WHERE b.book_id = ?
                ORDER BY bc.copy_id
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$bookId]);
            $copies = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    // 3. Show books that are currently borrowed and not returned
    public function showBorrowedBooks(): void
    {
        try {
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

            $stmt = $this->conn->query($sql);
            $borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    // 4. Show members who have borrowed a particular book,
    // including previous borrowing records
    public function showMembersByBook(): void
    {
        $bookId = $this->readBookId();

        if ($bookId === null) {
            return;
        }

        try {
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
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    // 5. Show fines associated with a particular book
    public function showFinesByBook(): void
    {
        $bookId = $this->readBookId();

        if ($bookId === null) {
            return;
        }

        try {
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
            $fines = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    // 6. Show all lost copies with their most recent borrowing details
    public function showLostBooks(): void
    {
        try {
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

            $stmt = $this->conn->query($sql);
            $lostBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    // Helper method to validate a book ID
    private function readBookId(): ?int
    {
        $input = trim(readline("Enter book ID: "));

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
