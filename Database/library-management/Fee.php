<?php
require_once "member.php";

class Fee
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

   public static function lateFeeRateForCategory(string $category): float
{
    switch (strtolower(trim($category))) {
        case 'staff':
            return 7.0;

        case 'research':
        case 'research student':
            return 5.0;

        case 'post graduate':
        case 'graduate student':
            return 3.0;

        case 'undergraduate':
        case 'undergraduate student':
            return 2.0;

        default:
            throw new UnexpectedValueException(
                "No late-fee rate is configured for member category: $category"
            );
    }
}

    public function processOverdueBorrowings(): void
    {
        $messages = [];

        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->query(
                "SELECT br.borrowing_id, br.return_date,
                        COALESCE(br.return_date, CURDATE()) AS fee_through_date,
                        DATEDIFF(CURDATE(), br.due_date) AS overdue_days,
                        m.category
                 FROM borrowings br
                 INNER JOIN members m ON br.member_id = m.member_id
                 WHERE br.return_date IS NULL
                   AND br.due_date < CURDATE()
                 FOR UPDATE"
            );
            $overdueBorrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($overdueBorrowings as $record) {
                $overdueDays = (int) $record['overdue_days'];
            
            }

            $this->conn->commit();
            foreach ($messages as $message) {
                echo $message . "\n";
            }
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }


   
    public function showAll(): void
    {
        try {
            $sql = "SELECT
                        f.fee_id,
                        f.borrowing_id,
                        m.name AS member_name,
                        b.title AS book_title,
                        f.fee_type,
                        f.amount,
                        f.fee_date,
                        f.payment_status,
                        f.payment_date,
                        br.due_date,
                        br.return_date,
                        COALESCE(br.return_date, CURDATE()) AS fee_through_date,
                        GREATEST(
                            DATEDIFF(COALESCE(br.return_date, CURDATE()), br.due_date),
                            0
                        ) AS overdue_days,
                        m.category
                    FROM fees f
                    INNER JOIN borrowings br
                    ON f.borrowing_id = br.borrowing_id
                    INNER JOIN members m
                    ON br.member_id = m.member_id
                    INNER JOIN books b
                    ON br.book_id = b.book_id
                    ORDER BY f.fee_id";

            $stmt = $this->conn->query($sql);
            $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($fees)) {
                echo "No fee records found.\n";
                return;
            }

            echo "\n========== FEE RECORDS ==========\n";

            foreach ($fees as $fee) {
                echo "Fee ID: " . $fee['fee_id'] . "\n";
                echo "Borrowing ID: " . $fee['borrowing_id'] . "\n";
                echo "Member: " . $fee['member_name'] . "\n";
                echo "Book: " . $fee['book_title'] . "\n";
                echo "Fee Type: " . $fee['fee_type'] . "\n";
                echo "Amount: Rs. " .number_format((float) $fee['amount'], 2) . "\n";
                echo "Fee Date: " . $fee['fee_date'] . "\n";
                echo "Payment Status: " . $fee['payment_status'] . "\n";
                echo "Payment Date: " .($fee['payment_date'] ?? 'Not paid') . "\n";
                $this->showLateFeePeriod($fee);
                echo "--------------------------------\n";
            }

        } catch (PDOException $e) {
            echo "Error displaying fees: " . $e->getMessage() . "\n";
        }
    }

    
    public function search(string $keyword): void
    {
        try {
            $sql = "SELECT
                        f.fee_id,
                        f.borrowing_id,
                        m.name AS member_name,
                        b.title AS book_title,
                        f.fee_type,
                        f.amount,
                        f.fee_date,
                        f.payment_status,
                        f.payment_date,
                        br.due_date,
                        br.return_date,
                        COALESCE(br.return_date, CURDATE()) AS fee_through_date,
                        GREATEST(
                            DATEDIFF(COALESCE(br.return_date, CURDATE()), br.due_date),
                            0
                        ) AS overdue_days,
                        m.category
                    FROM fees f
                    INNER JOIN borrowings br
                    ON f.borrowing_id = br.borrowing_id
                    INNER JOIN members m
                    ON br.member_id = m.member_id
                    INNER JOIN books b
                    ON br.book_id = b.book_id
                    WHERE m.name LIKE :name
                    OR f.payment_status LIKE :status
                    OR f.fee_type LIKE :type
                    ORDER BY f.fee_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                    ':name' => '%' . $keyword . '%',
                    ':status' => '%' . $keyword . '%',
                    ':type' => '%' . $keyword . '%'
            ]);


            $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($fees)) {
                echo "No matching fee records found.\n";
                return;
            }

            foreach ($fees as $fee) {
                echo "Fee ID: " . $fee['fee_id'] . "\n";
                echo "Member: " . $fee['member_name'] . "\n";
                echo "Book: " . $fee['book_title'] . "\n";
                echo "Fee Type: " . $fee['fee_type'] . "\n";
                echo "Amount: Rs. " .number_format((float) $fee['amount'], 2) . "\n";
                echo "Status: " . $fee['payment_status'] . "\n";
                $this->showLateFeePeriod($fee);
                echo "--------------------------------\n";
            }

        } catch (PDOException $e) {
            echo "Error searching fees: " . $e->getMessage() . "\n";
        }
    }

    private function showLateFeePeriod(array $fee): void
    {
        if ($fee['fee_type'] !== 'Late fee') {
            return;
        }

        $overdueDays = (int) $fee['overdue_days'];
        $dailyRate = self::lateFeeRateForCategory($fee['category']);

        echo "Due Date: " . $fee['due_date'] . "\n";
        echo "Return Date: " . ($fee['return_date']
            ?? 'Not returned (calculated through ' . $fee['fee_through_date'] . ')') . "\n";
        echo "Overdue Days: " . $overdueDays . "\n";
        echo "Rate per Day: Rs. " . number_format($dailyRate, 2) . "\n";
        echo "Total Late Fee for Period: Rs. "
            . number_format($overdueDays * $dailyRate, 2) . "\n";
    }

    
    public function addLateFee(int $borrowingId): void
    {
        try {
            $this->conn->beginTransaction();
            $check = $this->conn->prepare(
                "SELECT br.borrowing_id, br.due_date, br.return_date,
                        COALESCE(br.return_date, CURDATE()) AS fee_through_date,
                        DATEDIFF(CURDATE(), br.due_date) AS overdue_days,
                        m.category, b.title AS book_title
                 FROM borrowings br
                 INNER JOIN members m ON br.member_id = m.member_id
                 INNER JOIN books b ON br.book_id = b.book_id
                 WHERE br.borrowing_id = :id
                 FOR UPDATE"
            );
            $check->execute([':id' => $borrowingId]);
            $record = $check->fetch(PDO::FETCH_ASSOC);

            if (!$record) {
                $this->conn->rollBack();
                echo "Borrowing record not found.\n";
                return;
            }

            if ($record['return_date'] !== null) {
                $this->conn->rollBack();
                echo "This book has already been returned; no late fee can be added.\n";
                return;
            }

            $overdueDays = (int) $record['overdue_days'];
            if ($overdueDays <= 0) {
                $this->conn->rollBack();
                echo "This borrowing is not overdue; no late fee is due.\n";
                return;
            }

            $dailyRate = self::lateFeeRateForCategory($record['category']);
            $totalLateFee = $overdueDays * $dailyRate;


        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            echo "Error calculating late fee: " . $e->getMessage() . "\n";
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }


    public function addLostFee(int $borrowingId): void
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT br.borrowing_id, br.book_id, br.lost_fee,
                           b.price, b.title, br.copy_id, br.return_date
                    FROM borrowings br
                    INNER JOIN books b ON br.book_id = b.book_id
                    WHERE br.borrowing_id = :id
                    FOR UPDATE";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $borrowingId]);

            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$record) {
                $this->conn->rollBack();
                echo "Borrowing record not found.\n";
                return;
            }

            if ($record['return_date'] !== null) {
                $this->conn->rollBack();
                echo "This borrowing is already closed; a lost-book fee cannot be added.\n";
                return;
            }

            
            $check = $this->conn->prepare(
                "SELECT COUNT(*)
                 FROM fees
                 WHERE borrowing_id = :id
                 AND fee_type = 'Lost Fee'"
            );

            $check->execute([':id' => $borrowingId]);

            if ((int) $check->fetchColumn() > 0) {
                $this->conn->rollBack();
                echo "A lost-book fee already exists for this borrowing record.\n";
                return;
            }

            $amount = (float) $record['price'];

            if ($amount <= 0) {
                $this->conn->rollBack();
                echo "The book price must be greater than zero.\n";
                return;
            }

            $markCopyLost = $this->conn->prepare(
                "UPDATE book_copies
                 SET status = 'Lost'
                 WHERE copy_id = :copy_id
                   AND status = 'Borrowed'"
            );
            $markCopyLost->execute([':copy_id' => $record['copy_id']]);

            if ($markCopyLost->rowCount() !== 1) {
                throw new RuntimeException(
                    "The borrowed copy could not be marked as lost. "
                    . "Check that its current status is Borrowed."
                );
            }

            
            $sql = "INSERT INTO fees(borrowing_id, fee_type, amount, fee_date, payment_status)
                    VALUES(:borrowing_id, 'Lost Fee', :amount,CURDATE(), 'pending')";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':borrowing_id' => $borrowingId,
                ':amount' => $amount
            ]);

            $update = $this->conn->prepare(
                "UPDATE borrowings
                 SET lost_fee = :amount
                 WHERE borrowing_id = :id"
            );

            $update->execute([
                ':amount' => $amount,
                ':id' => $borrowingId
            ]);

            $this->conn->commit();

            echo "Lost-book fee added successfully!\n";
            echo "Book: " . $record['title'] . "\n";
            echo "Copy ID: " . $record['copy_id'] . "\n";
            echo "Copy status: Lost\n";
            echo "Lost Fee: Rs. " .number_format($amount, 2) . "\n";

        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            echo "Error adding lost-book fee: " . $e->getMessage() . "\n";
        }
    }

    
    public function markPaid(int $feeId): void
    {
        try {
            $sql = "UPDATE fees
                    SET payment_status = 'paid',payment_date = CURDATE()
                    WHERE fee_id = :id
                    AND payment_status = 'pending'";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $feeId]);

            if ($stmt->rowCount() > 0) {
                echo "Fee payment recorded successfully!\n";
            } else {
                echo "Fee not found or it has already been paid or waived.\n";
            }

        } catch (PDOException $e) {
            echo "Error updating payment: " . $e->getMessage() . "\n";
        }
    }

    
        
    public function viewAllFines(): void
    {
    do {
        echo "\n========== FINES AND REVENUE MENU ==========\n";
        echo "1. Fee History\n";
        echo "2. Total Fees\n";
        echo "3. Revenue per Book\n";
        echo "0. Back to Main Menu\n";
        echo "============================================\n";

        $choice = (int) readline("Enter your choice: ");

        switch ($choice) {
            case 1:
                $this->showFeeHistory();
                break;

            case 2:
                $this->showTotalFees();
                break;

            case 3:
                $this->showRevenuePerBook();
                break;

            case 0:
                echo "Returning to main menu...\n";
                break;

            default:
                echo "Invalid choice. Please try again.\n";
        }

        } while ($choice !== 0);
    }

    private function showFeeHistory(): void
    {
    try {
        $sql = "SELECT
                    f.fee_id,
                    f.borrowing_id,
                    b.title AS book_title,
                    m.name AS member_name,
                    f.fee_type,
                    f.amount,
                    f.fee_date,
                    f.payment_status
                FROM fees f
                JOIN borrowings br
                    ON f.borrowing_id = br.borrowing_id
                JOIN books b
                    ON br.book_id = b.book_id
                JOIN members m
                    ON br.member_id = m.member_id
                ORDER BY f.fee_date DESC, f.fee_id DESC";

        $stmt = $this->conn->query($sql);
        $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "\n========== FEE HISTORY ==========\n";

        if (empty($fees)) {
            echo "No fee records found.\n";
            return;
        }

        foreach ($fees as $fee) {
            echo "\nFee ID: " . $fee['fee_id'] . "\n";
            echo "Book: " . $fee['book_title'] . "\n";
            echo "Member: " . $fee['member_name'] . "\n";
            echo "Fee Type: " . $fee['fee_type'] . "\n";
            echo "Amount: Rs. "
                . number_format((float) $fee['amount'], 2) . "\n";
            echo "Fee Date: " . $fee['fee_date'] . "\n";
            echo "Payment Status: " . $fee['payment_status'] . "\n";
            echo "---------------------------------\n";
        }
    } catch (Throwable $e) {
        echo "Error displaying fee history: "
            . $e->getMessage() . "\n";
    }
    }


 
    private function showTotalFees(): void
    {
    try {
        $sql = "SELECT
                    COALESCE(SUM(amount), 0) AS total_fees,

                    COALESCE(SUM(
                        CASE
                            WHEN fee_type = 'Late fee'
                            THEN amount ELSE 0
                        END
                    ), 0) AS total_late_fees,

                    COALESCE(SUM(
                        CASE
                            WHEN fee_type = 'Lost fee'
                            THEN amount ELSE 0
                        END
                    ), 0) AS total_lost_fees,

                    COALESCE(SUM(
                        CASE
                            WHEN payment_status = 'paid'
                            THEN amount ELSE 0
                        END
                    ), 0) AS total_revenue,

                    COALESCE(SUM(
                        CASE
                            WHEN payment_status = 'pending'
                            THEN amount ELSE 0
                        END
                    ), 0) AS pending_fees

                FROM fees";

        $stmt = $this->conn->query($sql);
        $totals = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "\n========== TOTAL FEES SUMMARY ==========\n";

        echo "Total Fees (Late + Lost): Rs. "
            . number_format((float) $totals['total_fees'], 2) . "\n";

        echo "Total Late Fees: Rs. "
            . number_format((float) $totals['total_late_fees'], 2) . "\n";

        echo "Total Lost Fees: Rs. "
            . number_format((float) $totals['total_lost_fees'], 2) . "\n";

        echo "Total Revenue Collected: Rs. "
            . number_format((float) $totals['total_revenue'], 2) . "\n";

        echo "Total Pending Fees: Rs. "
            . number_format((float) $totals['pending_fees'], 2) . "\n";

        echo "========================================\n";

    } catch (Throwable $e) {
        echo "Error displaying total fees: "
            . $e->getMessage() . "\n";
    }
    }



    private function showRevenuePerBook(): void
    {
    try {
        $sql = "SELECT
                    b.book_id,
                    b.title AS book_title,

                    COALESCE(SUM(
                        CASE
                            WHEN f.fee_type = 'Late fee'
                             AND f.payment_status = 'paid'
                            THEN f.amount ELSE 0
                        END
                    ), 0) AS late_fee_revenue,

                    COALESCE(SUM(
                        CASE
                            WHEN f.fee_type = 'Lost fee'
                             AND f.payment_status = 'paid'
                            THEN f.amount ELSE 0
                        END
                    ), 0) AS lost_fee_revenue,

                    COALESCE(SUM(
                        CASE
                            WHEN f.payment_status = 'paid'
                            THEN f.amount ELSE 0
                        END
                    ), 0) AS total_revenue

                FROM books b
                LEFT JOIN borrowings br
                    ON b.book_id = br.book_id
                LEFT JOIN fees f
                    ON br.borrowing_id = f.borrowing_id

                GROUP BY b.book_id, b.title
                ORDER BY total_revenue DESC, b.title";

        $stmt = $this->conn->query($sql);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "\n========== REVENUE PER BOOK ==========\n";

        foreach ($books as $book) {
            echo "\nBook ID: " . $book['book_id'] . "\n";
            echo "Book: " . $book['book_title'] . "\n";
            echo "Late Fee Revenue: Rs. "
                . number_format((float) $book['late_fee_revenue'], 2) . "\n";
            echo "Lost Fee Revenue: Rs. "
                . number_format((float) $book['lost_fee_revenue'], 2) . "\n";
            echo "Total Revenue: Rs. "
                . number_format((float) $book['total_revenue'], 2) . "\n";
            echo "--------------------------------------\n";
        }
    } catch (Throwable $e) {
        echo "Error displaying revenue per book: "
            . $e->getMessage() . "\n";
    }
    }
        //$stmt = $this->conn->query($sql);
        //$fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

     
}

?>
