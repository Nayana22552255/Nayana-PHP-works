
<?php

class Borrowing
{
    private PDO $conn;

    
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function showAll(): void
    {
        try {
            $sql = "SELECT
                        br.borrowing_id,
                        m.name AS member_name,
                        b.title AS book_title,
                        br.borrow_date,
                        br.due_date,
                        br.return_date,
                        br.late_fee,
                        br.lost_fee
                    FROM borrowings br
                    INNER JOIN members m
                        ON br.member_id = m.member_id
                    INNER JOIN books b
                        ON br.book_id = b.book_id
                    ORDER BY br.borrowing_id";

            $stmt = $this->conn->query($sql);
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($records)) {
                echo "No borrowing records found.\n";
                return;
            }

            echo "\n========== BORROWING RECORDS ==========\n";

            foreach ($records as $record) {
                echo "Borrowing ID: " . $record['borrowing_id'] . "\n";
                echo "Member: " . $record['member_name'] . "\n";
                echo "Book: " . $record['book_title'] . "\n";
                echo "Borrow Date: " . $record['borrow_date'] . "\n";
                echo "Due Date: " . $record['due_date'] . "\n";
                echo "Return Date: "
                    . ($record['return_date'] ?: 'Not returned') . "\n";
                echo "Late Fee: " . $record['late_fee'] . "\n";
                echo "Lost Fee: " . $record['lost_fee'] . "\n";
                echo "--------------------------------------\n";
            }

        } catch (PDOException $e) {
            echo "Error displaying borrowing records: ". $e->getMessage() . "\n";
        }
    }

    
    
    public function borrow(int $memberId, int $bookId): void
    {
        try {
        $this->conn->beginTransaction();

        // 1. Check member
        $sql = "SELECT member_id, max_books, loan_days, active
                FROM members
                WHERE member_id = :id
                FOR UPDATE";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $memberId]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$member) {
            $this->conn->rollBack();
            echo "Member not found.\n";
            return;
        }

        // 2. Check member status
        if ((int) $member['active'] !== 1) {
            $this->conn->rollBack();
            echo "This member is inactive and cannot borrow books.\n";
            return;
        }

        // 3. Check borrowing limit
        $sql = "SELECT COUNT(*)
                FROM borrowings
                WHERE member_id = :id
                  AND return_date IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $memberId]);
        $borrowedCount = (int) $stmt->fetchColumn();

        if ($borrowedCount >= (int) $member['max_books']) {
            $this->conn->rollBack();
            echo "This member has reached the borrowing limit.\n";
            return;
        }

        // 4. Find an available copy
        $sql = "SELECT bc.copy_id, b.book_id, b.title
                FROM book_copies bc
                JOIN books b ON b.book_id = bc.book_id
                WHERE bc.book_id = :book_id
                  AND bc.status = 'Available'
                ORDER BY bc.copy_id
                LIMIT 1
                FOR UPDATE";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':book_id' => $bookId]);
        $copy = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$copy) {
            $this->conn->rollBack();
            echo "No available copies of this book.\n";
            return;
        }

        $copyId = (int) $copy['copy_id'];

        // 5. Calculate dates
        $borrowDate = date('Y-m-d');
        $dueDate = date(
            'Y-m-d',
            strtotime('+' . (int) $member['loan_days'] . ' days')
        );

        // 6. Insert borrowing record
        $sql = "INSERT INTO borrowings
                (member_id, book_id, copy_id, borrow_date,
                 due_date, return_date, late_fee, lost_fee)
                VALUES
                (:member_id, :book_id, :copy_id, :borrow_date,
                 :due_date, NULL, 0, 0)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':member_id' => $memberId,
            ':book_id' => $bookId,
            ':copy_id' => $copyId,
            ':borrow_date' => $borrowDate,
            ':due_date' => $dueDate
        ]);

        // 7. Mark this copy as borrowed
        $sql = "UPDATE book_copies
                SET status = 'Borrowed'
                WHERE copy_id = :copy_id
                  AND status = 'Available'";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':copy_id' => $copyId]);

        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException(
                "Could not update the copy status."
            );
        }

        // 8. Count available copies
        $availableCopies = $this->getAvailableCopyCount($bookId);

        // 9. Commit transaction
        $this->conn->commit();

        // 10. Display borrowing details
        echo "\n========== BORROWING DETAILS ==========\n";
        echo "Book borrowed successfully!\n";
        echo "Book: " . $copy['title'] . "\n";
        echo "Copy ID: " . $copyId . "\n";
        echo "Borrow Date: " . $borrowDate . "\n";
        echo "Due Date: " . $dueDate . "\n";
        echo "Available copies remaining: "
            . $availableCopies . "\n";
        echo "=======================================\n";

    } catch (Throwable $e) {
        if ($this->conn->inTransaction()) {
            $this->conn->rollBack();
        }

        echo "Error borrowing book: "
            . $e->getMessage() . "\n";
    }
    }

    
    public function returnBook(int $borrowingId): void
    {
        try {
            $this->conn->beginTransaction();

            
            $sql = "SELECT
            br.borrowing_id,
            br.member_id,
            br.book_id,
            br.copy_id,
            br.due_date,
            m.category
            FROM borrowings br
            JOIN members m
            ON br.member_id = m.member_id
            WHERE br.borrowing_id = :id
            AND br.return_date IS NULL
            FOR UPDATE";

            //$this->conn->commit();

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $borrowingId]);

            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$record) {
                $this->conn->rollBack();
                echo "Borrowing record not found or book already returned.\n";
                return;
            }

            $returnDate = date('Y-m-d');

            
            $dueDate = new DateTime($record['due_date']);
            $actualReturnDate = new DateTime($returnDate);

            $lateDays = 0;

            if ($actualReturnDate > $dueDate) {
                $lateDays = (int) $dueDate->diff($actualReturnDate)->days;
            }

            
            $expectedLateFee = $lateDays
                * Fee::lateFeeRateForCategory($record['category']);

            $lateFeeCheck = $this->conn->prepare(
                "SELECT COALESCE(SUM(amount), 0)
                 FROM fees
                 WHERE borrowing_id = :id
                   AND fee_type = 'Late fee'"
            );
            $lateFeeCheck->execute([':id' => $borrowingId]);
            $existingLateFee = (float) $lateFeeCheck->fetchColumn();
            $additionalLateFee = max(0, $expectedLateFee - $existingLateFee);
            $lateFee = $existingLateFee + $additionalLateFee;

            
            $sql = "UPDATE borrowings
                    SET return_date = :return_date,
                    late_fee = :late_fee
                    WHERE borrowing_id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':return_date' => $returnDate,
                ':late_fee' => $lateFee,
                ':id' => $borrowingId
            ]);

            $savedReturnDate = $this->conn->prepare(
                "SELECT return_date
                 FROM borrowings
                 WHERE borrowing_id = :id"
            );
            $savedReturnDate->execute([':id' => $borrowingId]);
            $returnDate = $savedReturnDate->fetchColumn();

            if ($returnDate === false || $returnDate === null) {
                throw new RuntimeException(
                    "The return date was not saved for borrowing ID $borrowingId."
                );
            }

            
            $sql = "UPDATE book_copies
                    SET status= 'Available'
                    WHERE copy_id=:copy_id
                    AND status='Borrowed'";
                   

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':copy_id' => $record['copy_id']]);

            
            if ($additionalLateFee > 0) {
                $sql = "INSERT INTO fees(borrowing_id, fee_type, amount, fee_date,payment_status)
                        VALUES(:borrowing_id, 'Late fee', :amount,:fee_date, 'pending')";

                $stmt = $this->conn->prepare($sql);

                $stmt->execute([
                    ':borrowing_id' => $borrowingId,
                    ':amount' => $additionalLateFee,
                    ':fee_date' => $returnDate
                ]);
            }

           

            $this->conn->commit();

            $dailyRate = Fee::lateFeeRateForCategory($record['category']);

            echo "\n========== BOOK RETURN DETAILS ==========\n";
            echo "Borrowing ID: " . $borrowingId . "\n";
            echo "Due Date: " . $record['due_date'] . "\n";
            echo "Return Date: " . $returnDate . "\n";
            echo "Overdue Days: " . $lateDays . "\n";
            echo "Late Fee Per Day: Rs. " . number_format($dailyRate, 2) . "\n";
            echo "Calculated Late Fee: Rs. " . number_format($expectedLateFee, 2) . "\n";
            echo "Total Late Fee Recorded: Rs. " . number_format($lateFee, 2) . "\n";
            echo "\nBook returned successfully!\n";
            //echo "Return Date: " . $returnDate . "\n";
            echo "Late Fee for This Book: Rs. "
                . number_format($lateFee, 2) . "\n";

            // Display the member's late fees for all borrowed books
            $this->showMemberLateFeeSummary(
                (int) $record['member_id']
            );
            echo "=========================================\n";


            } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            echo "Error returning book: " . $e->getMessage() . "\n";
        }
    }


    public function showMemberLateFeeSummary(int $memberId): void
    {
    try {
        $sql = "SELECT
                    br.borrowing_id,
                    br.book_id,
                    b.title AS book_title,
                    br.borrow_date,
                    br.due_date,
                    br.return_date,
                    m.name AS member_name,
                    m.category,
                    GREATEST(
                        DATEDIFF(
                            COALESCE(br.return_date, CURDATE()),
                            br.due_date
                        ),
                        0
                    ) AS overdue_days,
                    COALESCE(
                        SUM(
                            CASE
                                WHEN f.fee_type = 'Late fee'
                                     AND f.payment_status = 'paid'
                                THEN f.amount
                                ELSE 0
                            END
                        ), 0
                    ) AS paid_late_fee
                FROM borrowings br
                JOIN members m
                    ON br.member_id = m.member_id
                JOIN books b
                    ON br.book_id = b.book_id
                LEFT JOIN fees f
                    ON br.borrowing_id = f.borrowing_id
                WHERE br.member_id = :member_id
                GROUP BY
                    br.borrowing_id,
                    br.book_id,
                    b.title,
                    br.borrow_date,
                    br.due_date,
                    br.return_date,
                    m.name,
                    m.category
                ORDER BY br.borrowing_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':member_id' => $memberId]);

        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($records)) {
            echo "No borrowing records found for this member.\n";
            return;
        }

        $totalLateFee = 0.0;
        $totalPaid = 0.0;

        echo "\n========== MEMBER LATE FEE SUMMARY ==========\n";
        echo "Member: " . $records[0]['member_name'] . "\n";
        echo "Member ID: " . $memberId . "\n";

        foreach ($records as $record) {
            $overdueDays = (int) $record['overdue_days'];

            $dailyRate = Fee::lateFeeRateForCategory(
                $record['category']
            );

            $calculatedFee = $overdueDays * $dailyRate;
            $paidFee = min(
                $calculatedFee,
                (float) $record['paid_late_fee']
            );

            $pendingFee = max(0, $calculatedFee - $paidFee);

            $totalLateFee += $calculatedFee;
            $totalPaid += $paidFee;

            echo "\nBorrowing ID: " . $record['borrowing_id'] . "\n";
            echo "Book ID: " . $record['book_id'] . "\n";
            echo "Book Title: " . $record['book_title'] . "\n";
            echo "Borrow Date: " . $record['borrow_date'] . "\n";
            echo "Due Date: " . $record['due_date'] . "\n";

            echo "Return Date: "
                . ($record['return_date'] ?? 'Not returned') . "\n";

            echo "Overdue Days: " . $overdueDays . "\n";
            echo "Rate Per Day: Rs. "
                . number_format($dailyRate, 2) . "\n";

            echo "Total Late Fee: Rs. "
                . number_format($calculatedFee, 2) . "\n";

            echo "Paid Late Fee: Rs. "
                . number_format($paidFee, 2) . "\n";

            echo "Pending Late Fee: Rs. "
                . number_format($pendingFee, 2) . "\n";

            echo "---------------------------------------------\n";
        }

        echo "\n=============== FINAL TOTALS ===============\n";
        echo "Total Late Fees: Rs. "
            . number_format($totalLateFee, 2) . "\n";

        echo "Total Paid: Rs. "
            . number_format($totalPaid, 2) . "\n";

        echo "Total Pending: Rs. "
            . number_format(
                max(0, $totalLateFee - $totalPaid),
                2
            ) . "\n";

        echo "=============================================\n";

    } catch (Throwable $e) {
        echo "Error displaying member late-fee summary: "
            . $e->getMessage() . "\n";
    }
}


public function viewAllMemberFines(int $memberId): void
{
    try {
        // Check whether the member exists
        $check = $this->conn->prepare(
            "SELECT name, category
             FROM members
             WHERE member_id = :id"
        );

        $check->execute([':id' => $memberId]);
        $member = $check->fetch(PDO::FETCH_ASSOC);

        if (!$member) {
            echo "Member not found.\n";
            return;
        }

        // Get all overdue borrowings for this member.
        // For returned books, calculate through return_date.
        // For unreturned books, calculate through today's date.
        $sql = "SELECT
                    br.borrowing_id,
                    br.book_id,
                    b.title AS book_title,
                    br.borrow_date,
                    br.due_date,
                    br.return_date,
                    GREATEST(
                        DATEDIFF(
                            COALESCE(br.return_date, CURDATE()),
                            br.due_date
                        ),
                        0
                    ) AS overdue_days,

                    COALESCE(fees.paid_amount, 0) AS paid_amount

                FROM borrowings br

                INNER JOIN books b
                    ON br.book_id = b.book_id

                LEFT JOIN (
                    SELECT
                        borrowing_id,
                        SUM(
                            CASE
                                WHEN payment_status = 'paid'
                                THEN amount
                                ELSE 0
                            END
                        ) AS paid_amount
                    FROM fees
                    WHERE fee_type = 'Late fee'
                    GROUP BY borrowing_id
                ) fees
                    ON br.borrowing_id = fees.borrowing_id

                WHERE br.member_id = :member_id
                  AND COALESCE(br.return_date, CURDATE()) > br.due_date

                ORDER BY br.borrowing_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':member_id' => $memberId]);

        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "\n========== MEMBER FINE DETAILS ==========\n";
        echo "Member ID: " . $memberId . "\n";
        echo "Member Name: " . $member['name'] . "\n";
        echo "Category: " . $member['category'] . "\n";

        if (empty($records)) {
            echo "This member has no overdue books.\n";
            echo "=========================================\n";
            return;
        }

        $totalFine = 0.0;
        $totalPaid = 0.0;
        $totalPending = 0.0;

        $dailyRate = Fee::lateFeeRateForCategory(
            $member['category']
        );

        foreach ($records as $record) {
            $overdueDays = (int) $record['overdue_days'];

            // Calculate the total fine for this book
            $calculatedFine = $overdueDays * $dailyRate;

            // Amount already paid for this borrowing
            $paid = min(
                $calculatedFine,
                (float) $record['paid_amount']
            );

            $pending = max(0, $calculatedFine - $paid);

            $totalFine += $calculatedFine;
            $totalPaid += $paid;
            $totalPending += $pending;

            echo "\nBorrowing ID: "
                . $record['borrowing_id'] . "\n";

            echo "Book ID: " . $record['book_id'] . "\n";
            echo "Book Title: " . $record['book_title'] . "\n";
            echo "Borrow Date: " . $record['borrow_date'] . "\n";
            echo "Due Date: " . $record['due_date'] . "\n";

            echo "Return Date: "
                . ($record['return_date'] ?? 'Not returned')
                . "\n";

            echo "Overdue Days: " . $overdueDays . "\n";

            echo "Late Fee Per Day: Rs. "
                . number_format($dailyRate, 2) . "\n";

            echo "Total Fine: Rs. "
                . number_format($calculatedFine, 2) . "\n";

            echo "Paid Fine: Rs. "
                . number_format($paid, 2) . "\n";

            echo "Pending Fine: Rs. "
                . number_format($pending, 2) . "\n";

            echo "-----------------------------------------\n";
        }

        echo "\n=============== TOTAL SUMMARY ===============\n";

        echo "Total Fine: Rs. "
            . number_format($totalFine, 2) . "\n";

        echo "Total Paid: Rs. "
            . number_format($totalPaid, 2) . "\n";

        echo "Total Pending: Rs. "
            . number_format($totalPending, 2) . "\n";

        echo "==============================================\n";

    } catch (Throwable $e) {
        echo "Error displaying member fines: "
            . $e->getMessage() . "\n";
    }
}


public function showAvailableCopies(int $bookId): void
{
    $sql = "SELECT copy_id
            FROM book_copies
            WHERE book_id = ?
              AND status = 'Available'
            ORDER BY copy_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$bookId]);
    $copies = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "\nAvailable copy IDs: ";

    if (empty($copies)) {
        echo "None\n";
    } else {
        echo implode(', ', $copies) . "\n";
    }

    echo "Available copies: " . count($copies) . "\n";
}

private function getAvailableCopyCount(int $bookId): int
{
    $stmt = $this->conn->prepare(
        "SELECT COUNT(*)
         FROM book_copies
         WHERE book_id = ?
           AND status = 'Available'"
    );

    $stmt->execute([$bookId]);

    return (int) $stmt->fetchColumn();
}

public function showMemberBorrowingHistory(int $memberId):void
{
        try
        {
            $sql="SELECT name FROM  members WHERE member_id= :member_id";

            $stmt=$this->conn->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            $member=$stmt->fetch(PDO::FETCH_ASSOC);

            if(!$member)
                {
                    echo "Member not fount.\n";
                    return;
                }

                $sql="SELECT
                            br.borrowing_id,
                            b.title AS book_title,
                            br.copy_id,
                            br.borrow_date,
                            br.due_date,
                            br.return_date,
                            br.late_fee,
                            br.lost_fee
                            FROM borrowings br
                            JOIN books b
                            ON br.book_id=b.book_id
                            WHERE br.member_id=:member_id
                            ORDER BY br.borrow_date DESC, br.borrowing_id DESC";


                $stmt = $this->conn->prepare($sql);
                $stmt->execute([':member_id' => $memberId]);

                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo "\n========== MEMBER BORROWING HISTORY ==========\n";
                echo "Member ID: " . $memberId . "\n";
                echo "Member Name: " . $member['name'] . "\n";

                if (empty($records)) {
                    echo "This member has not borrowed any books.\n";
                    echo "==============================================\n";
                    return;
                }

                foreach ($records as $record) {
                    echo "\nBorrowing ID: "
                        . $record['borrowing_id'] . "\n";

                    echo "Book Title: "
                        . $record['book_title'] . "\n";

                    echo "Copy ID: "
                        . ($record['copy_id'] ?? 'Not assigned') . "\n";

                    echo "Borrow Date: "
                        . $record['borrow_date'] . "\n";

                    echo "Due Date: "
                        . $record['due_date'] . "\n";

                    echo "Return Date: "
                        . ($record['return_date'] ?? 'Not returned yet') . "\n";

                    echo "Status: "
                        . ($record['return_date'] === null
                            ? 'Currently Borrowed'
                            : 'Returned') . "\n";

                    echo "Late Fee: Rs. "
                        . number_format((float) $record['late_fee'], 2) . "\n";

                    echo "Lost Fee: Rs. "
                        . number_format((float) $record['lost_fee'], 2) . "\n";

                    echo "----------------------------------------------\n";
                }

                echo "==============================================\n";

                
        echo "==============================================\n";

    } catch (Throwable $e) {
        echo "Error displaying borrowing history: "
            . $e->getMessage() . "\n";
    }
}

}
?>
