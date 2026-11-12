
<?php

class Borrowing
{
    private PDO $conn;

    
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    
    public function updateOverdueLateFees(): void
    {
        try {
            $sql = "UPDATE borrowings br
                    INNER JOIN members m
                    ON br.member_id = m.member_id
                    INNER JOIN member_fee mf
                    ON m.category_id = mf.category_id
                    SET br.late_fee =
                    DATEDIFF(CURDATE(), br.due_date)
                    * mf.late_fee_per_day
                    WHERE br.return_date IS NULL
                    AND br.due_date < CURDATE()";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

        } catch (PDOException $e) {
            echo "Error updating overdue fees: "
                . $e->getMessage() . "\n";
        }
    }


    public function showAll(): void
    {
        try {
            
            $this->updateOverdueLateFees();
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
        $sql = "SELECT m.member_id, mf.max_books, mf.loan_days, m.active
                FROM members m
                JOIN member_fee mf ON mf.category_id = m.category_id
                WHERE m.member_id = :id
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

    
    public function returnBook(int $borrowingId, bool $reportedLost = false): void
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT
                        br.borrowing_id,
                        br.member_id,
                        br.book_id,
                        br.copy_id,
                        br.due_date,
                        br.lost_fee AS recorded_lost_fee,
                        mf.member_category AS category,
                        b.title AS book_title,
                        b.price AS book_price,
                        bc.status AS copy_status
                    FROM borrowings br
                    JOIN members m ON br.member_id = m.member_id
                    JOIN member_fee mf ON mf.category_id = m.category_id
                    JOIN books b ON br.book_id = b.book_id
                    LEFT JOIN book_copies bc ON br.copy_id = bc.copy_id
                    WHERE br.borrowing_id = :id
                      AND br.return_date IS NULL
                    FOR UPDATE";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $borrowingId]);

            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$record) {
                $this->conn->rollBack();
                echo "Borrowing record not found or book already returned.\n";
                return;
            }

            $existingLostFeeQuery = $this->conn->prepare(
                "SELECT COUNT(*), COALESCE(SUM(amount), 0)
                 FROM fees
                 WHERE borrowing_id = :id
                   AND fee_type = 'Lost Fee'"
            );
            $existingLostFeeQuery->execute([':id' => $borrowingId]);
            $existingLostFee = $existingLostFeeQuery->fetch(PDO::FETCH_NUM);
            $existingLostFeeCount = (int) $existingLostFee[0];
            $lostFee = (float) $existingLostFee[1];
            $isLost = $reportedLost
                || $record['copy_status'] === 'Lost'
                || $existingLostFeeCount > 0
                || (float) $record['recorded_lost_fee'] > 0;

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
                   AND fee_type = 'Late fee'
                   AND payment_status = 'paid'"
            );
            $lateFeeCheck->execute([':id' => $borrowingId]);
            $paidLateFee = (float) $lateFeeCheck->fetchColumn();
            $unpaidLateFee = max(0, $expectedLateFee - $paidLateFee);
            $lateFee = $paidLateFee + $unpaidLateFee;

            if ($isLost && $existingLostFeeCount === 0) {
                $lostFee = (float) $record['recorded_lost_fee'];
                if ($lostFee <= 0) {
                    $lostFee = (float) $record['book_price'];
                }

                if ($lostFee <= 0) {
                    throw new RuntimeException(
                        "The book price must be greater than zero to add a lost-book fee."
                    );
                }

                $insertLostFee = $this->conn->prepare(
                    "INSERT INTO fees
                        (borrowing_id, fee_type, amount, fee_date, payment_status)
                     VALUES (:borrowing_id, 'Lost Fee', :amount, CURDATE(), 'pending')"
                );
                $insertLostFee->execute([
                    ':borrowing_id' => $borrowingId,
                    ':amount' => $lostFee
                   
                ]);
            }

            $updateBorrowing = $this->conn->prepare(
                "UPDATE borrowings
                 SET return_date = :return_date,
                     late_fee = :late_fee,
                     lost_fee = :lost_fee
                 WHERE borrowing_id = :id"
            );
            $updateBorrowing->execute([
                ':return_date' => $returnDate,
                ':late_fee' => $lateFee,
                ':lost_fee' => $lostFee,
                ':id' => $borrowingId
            ]);

            if ($record['copy_id'] !== null
                && $record['copy_status'] === 'Borrowed') {
                $copyStatus = $isLost ? 'Lost' : 'Available';
                $updateCopy = $this->conn->prepare(
                    "UPDATE book_copies
                     SET status = :status
                     WHERE copy_id = :copy_id
                       AND status = 'Borrowed'"
                );
                $updateCopy->execute([
                    ':status' => $copyStatus,
                    ':copy_id' => $record['copy_id']
                ]);

                if ($updateCopy->rowCount() !== 1) {
                    throw new RuntimeException(
                        "Could not update the status of copy "
                        . $record['copy_id'] . "."
                    );
                }
            } elseif ($record['copy_id'] !== null
                && ($record['copy_status'] !== 'Lost' || !$isLost)) {
                throw new RuntimeException(
                    "The borrowed copy is not marked as Borrowed or Lost."
                );
            }

           // Find an existing pending late-fee record
$checkFee = $this->conn->prepare(
    "SELECT fee_id
     FROM fees
     WHERE borrowing_id = :id
       AND fee_type = 'Late fee'
       AND payment_status = 'pending'
     ORDER BY fee_id
     LIMIT 1"
);

        $checkFee->execute([':id' => $borrowingId]);

        $feeId = $checkFee->fetchColumn();

        if ($feeId) {

            // Update the existing fee with the complete late fee
            $updateFee = $this->conn->prepare(
                "UPDATE fees
                SET amount = :amount
                WHERE fee_id = :fee_id"
            );

            $updateFee->execute([
                ':amount' => $unpaidLateFee,
                ':fee_id' => $feeId
            ]);

        } elseif ($unpaidLateFee > 0) {

            // Insert a new fee record if none exists
            $insertFee = $this->conn->prepare(
                "INSERT INTO fees
                    (borrowing_id, fee_type, amount, fee_date, payment_status)
                VALUES (:id, 'Late fee', :amount, CURDATE(), 'pending')"
            );

            $insertFee->execute([
                ':id' => $borrowingId,
                ':amount' => $unpaidLateFee
            ]);
        }

            $this->conn->commit();

            echo $isLost
                ? "Book marked as lost successfully.\n"
                : "Book returned successfully!\n";

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
                    mf.member_category AS category,
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
                JOIN member_fee mf
                    ON mf.category_id = m.category_id
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
                    mf.member_category
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


public function viewAllMemberFines(int $memberId, bool $asTable = false): void
{
    try {
        // Check whether the member exists
        $check = $this->conn->prepare(
            "SELECT m.name, mf.member_category AS category
             FROM members m
             JOIN member_fee mf ON mf.category_id = m.category_id
             WHERE m.member_id = :id"
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

        if ($asTable) {
            echo '<h3>Member Details</h3>';
            echo '<table border="1" cellpadding="6" cellspacing="0">';
            echo '<tr><th>Member ID</th><th>Member Name</th><th>Category</th></tr>';
            echo '<tr><td>' . $this->h($memberId) . '</td><td>' . $this->h($member['name']) . '</td><td>' . $this->h($member['category']) . '</td></tr>';
            echo '</table>';
        } else {
            echo "\n========== MEMBER FINE DETAILS ==========\n";
            echo "Member ID: " . $memberId . "\n";
            echo "Member Name: " . $member['name'] . "\n";
            echo "Category: " . $member['category'] . "\n";
        }

        if (empty($records)) {
            if ($asTable) {
                echo '<p>This member has no overdue books.</p>';
            } else {
                echo "This member has no overdue books.\n";
                echo "=========================================\n";
            }
            return;
        }

        $totalFine = 0.0;
        $totalPaid = 0.0;
        $totalPending = 0.0;

        $dailyRate = Fee::lateFeeRateForCategory(
            $member['category']
        );
        $fineRows = [];

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

            $fineRows[] = [
                $record['borrowing_id'],
                $record['book_id'],
                $record['book_title'],
                $record['borrow_date'],
                $record['due_date'],
                $record['return_date'] ?? 'Not returned',
                $overdueDays,
                'Rs. ' . number_format($dailyRate, 2),
                'Rs. ' . number_format($calculatedFine, 2),
                'Rs. ' . number_format($paid, 2),
                'Rs. ' . number_format($pending, 2)
            ];

            if (!$asTable) {
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
        }

        if ($asTable) {
            echo '<h3>Borrowed Details</h3>';
            echo '<table border="1" cellpadding="6" cellspacing="0">';
            echo '<tr><th>Borrowing ID</th><th>Book ID</th><th>Book Title</th><th>Borrow Date</th><th>Due Date</th><th>Return Date</th><th>Overdue Days</th><th>Late Fee Per Day</th><th>Total Fine</th><th>Paid Fine</th><th>Pending Fine</th></tr>';
            foreach ($fineRows as $fineRow) {
                echo '<tr>';
                foreach ($fineRow as $value) {
                    echo '<td>' . $this->h($value) . '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
            echo '<h3>Total Summary</h3>';
            echo '<table border="1" cellpadding="6" cellspacing="0">';
            echo '<tr><th>Total Fine</th><th>Total Paid</th><th>Total Pending</th></tr>';
            echo '<tr><td>Rs. ' . number_format($totalFine, 2) . '</td><td>Rs. ' . number_format($totalPaid, 2) . '</td><td>Rs. ' . number_format($totalPending, 2) . '</td></tr>';
            echo '</table>';
            return;
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
        if ($asTable) {
            echo '<p><strong>Error displaying member fines:</strong> '
                . $this->h($e->getMessage()) . '</p>';
        } else {
            echo "Error displaying member fines: "
                . $e->getMessage() . "\n";
        }
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
public function showBorrowingDays(): void
{
    $sql = "SELECT
                br.borrowing_id,
                m.name AS member_name,
                b.title AS book_title,
                br.borrow_date,
                br.return_date,
                DATEDIFF(
                    COALESCE(br.return_date, CURDATE()),
                    br.borrow_date
                ) AS days_held
            FROM borrowings br
            JOIN members m ON br.member_id = m.member_id
            JOIN books b ON br.book_id = b.book_id
            ORDER BY br.borrowing_id DESC";

    $stmt = $this->conn->query($sql);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($records)) {
        echo "No borrowing records found.\n";
        return;
    }

    foreach ($records as $record) {
        echo "-----------------------------\n";
        echo "Borrowing ID: " . $record['borrowing_id'] . "\n";
        echo "Member Name: " . $record['member_name'] . "\n";
        echo "Book Title: " . $record['book_title'] . "\n";
        echo "Borrow Date: " . $record['borrow_date'] . "\n";

        if ($record['return_date'] === null) {
            echo "Return Status: Not Returned\n";
        } else {
            echo "Return Date: " . $record['return_date'] . "\n";
            echo "Return Status: Returned\n";
        }

        echo "Days Held: " . $record['days_held'] . " days\n";
    }
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



    /* ================= WEB PAGE METHODS ================= */

    private function h(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    private function renderOutput(callable $callback): void
    {
        ob_start();
        $callback();
        $output = ob_get_clean();
        if (trim($output) !== '') {
            echo '<pre>' . $this->h($output) . '</pre>';
        }
    }

    private function renderBorrowOutput(callable $callback): void
    {
        ob_start();
        $callback();
        $output = trim((string) ob_get_clean());

        if ($output === '') {
            return;
        }

        if (strpos($output, 'Book borrowed successfully!') === false) {
            echo '<pre>' . $this->h($output) . '</pre>';
            return;
        }

        $details = [];
        foreach (preg_split('/\R/', $output) as $line) {
            if (strpos($line, ': ') !== false) {
                [$label, $value] = explode(': ', trim($line), 2);
                $details[$label] = $value;
            }
        }

        echo '<p>Book borrowed successfully!</p>';
        echo '<table border="1" cellpadding="6" cellspacing="0">';
        foreach ([
            'Book',
            'Copy ID',
            'Borrow Date',
            'Due Date',
            'Available copies remaining'
        ] as $label) {
            if (isset($details[$label])) {
                echo '<tr><th>' . $this->h($label) . '</th><td>'
                    . $this->h($details[$label]) . '</td></tr>';
            }
        }
        echo '</table>';
    }

    private function memberOptions(): array
    {
        return $this->conn->query(
            "SELECT member_id, name FROM members ORDER BY name"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    private function bookOptions(): array
    {
        return $this->conn->query(
            "SELECT book_id, title FROM books ORDER BY title"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    private function activeBorrowingOptions(): array
    {
        return $this->conn->query(
            "SELECT br.borrowing_id, m.name, b.title
             FROM borrowings br
             JOIN members m ON m.member_id = br.member_id
             JOIN books b ON b.book_id = br.book_id
             WHERE br.return_date IS NULL
             ORDER BY br.borrowing_id DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    private function renderBorrowingTable(): void
    {
        $this->updateOverdueLateFees();
        $sql = "SELECT br.borrowing_id, br.member_id, m.name AS member_name,
                       br.book_id, b.title AS book_title, br.copy_id,
                       br.borrow_date, br.due_date, br.return_date,
                       br.late_fee, br.lost_fee
                FROM borrowings br
                JOIN members m ON m.member_id = br.member_id
                JOIN books b ON b.book_id = br.book_id
                ORDER BY br.borrowing_id DESC";
        $records = $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        echo '<h3>All Borrowing Records</h3>';
        if (!$records) {
            echo '<p>No borrowing records found.</p>';
            return;
        }

        echo '<table border="1" cellpadding="6">';
        echo '<tr><th>ID</th><th>Member</th><th>Book</th><th>Copy ID</th><th>Borrow Date</th><th>Due Date</th><th>Return Date</th><th>Late Fee</th><th>Lost Fee</th><th>Status</th></tr>';
        foreach ($records as $r) {
            echo '<tr>';
            foreach (['borrowing_id','member_name','book_title','copy_id','borrow_date','due_date','return_date','late_fee','lost_fee'] as $key) {
                $value = $r[$key];
                if ($key === 'return_date' && !$value) $value = 'Not returned';
                echo '<td>' . $this->h($value === null ? '-' : $value) . '</td>';
            }
            echo '<td>' . ($r['return_date'] ? 'Returned' : 'Borrowed') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    private function renderBorrowingDaysTable(): void
    {
        $sql = "SELECT br.borrowing_id, m.name AS member_name,
                       b.title AS book_title, br.borrow_date, br.return_date,
                       GREATEST(DATEDIFF(COALESCE(br.return_date, CURDATE()),
                       br.borrow_date), 0) AS days_held
                FROM borrowings br
                JOIN members m ON m.member_id = br.member_id
                JOIN books b ON b.book_id = br.book_id
                ORDER BY br.borrowing_id DESC";
        $records = $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        echo '<h3>Borrowing Days</h3>';
        if (!$records) {
            echo '<p>No borrowing records found.</p>';
            return;
        }
        echo '<table border="1" cellpadding="6"><tr><th>Borrowing ID</th><th>Member</th><th>Book</th><th>Borrow Date</th><th>Return Date</th><th>Status</th><th>Days Held</th></tr>';
        foreach ($records as $r) {
            echo '<tr><td>' . $this->h($r['borrowing_id']) . '</td>';
            echo '<td>' . $this->h($r['member_name']) . '</td>';
            echo '<td>' . $this->h($r['book_title']) . '</td>';
            echo '<td>' . $this->h($r['borrow_date']) . '</td>';
            echo '<td>' . $this->h($r['return_date'] ?: 'Not returned') . '</td>';
            echo '<td>' . ($r['return_date'] ? 'Returned' : 'Borrowed') . '</td>';
            echo '<td>' . $this->h($r['days_held']) . ' days</td></tr>';
        }
        echo '</table>';
    }

    private function renderMemberReport(string $type, int $memberId): void
    {
        if ($type === 'fines') {
            $this->viewAllMemberFines($memberId, true);
        } else {
            ob_start();
            $this->showMemberBorrowingHistory($memberId);
            $output = trim((string) ob_get_clean());

            if ($output === '') {
                return;
            }

            if (strpos($output, 'MEMBER BORROWING HISTORY') === false) {
                echo '<pre>' . $this->h($output) . '</pre>';
                return;
            }

            $memberDetails = [];
            $records = [];
            $record = null;
            foreach (preg_split('/\R/', $output) as $line) {
                $line = trim($line);
                if ($line === '' || strpos($line, '=') === 0 || strpos($line, '-') === 0) {
                    continue;
                }

                if (strpos($line, 'Borrowing ID: ') === 0) {
                    if ($record !== null) {
                        $records[] = $record;
                    }
                    $record = ['Borrowing ID' => substr($line, strlen('Borrowing ID: '))];
                    continue;
                }

                if ($line === 'This member has not borrowed any books.') {
                    $memberDetails['notice'] = $line;
                    continue;
                }

                if (strpos($line, ': ') !== false) {
                    [$label, $value] = explode(': ', $line, 2);
                    if ($record !== null) {
                        $record[$label] = $value;
                    } else {
                        $memberDetails[$label] = $value;
                    }
                }
            }
            if ($record !== null) {
                $records[] = $record;
            }

            echo '<h3>Member Borrowing History</h3>';
            echo '<p><strong>Member ID:</strong> ' . $this->h($memberDetails['Member ID'] ?? '')
                . ' &nbsp; <strong>Member Name:</strong> '
                . $this->h($memberDetails['Member Name'] ?? '') . '</p>';

            if (!$records) {
                echo '<p>' . $this->h($memberDetails['notice'] ?? 'No borrowing records found.') . '</p>';
                return;
            }

            echo '<table border="1" cellpadding="6" cellspacing="0">';
            echo '<tr><th>Borrowing ID</th><th>Book Title</th><th>Copy ID</th><th>Borrow Date</th>'
                . '<th>Due Date</th><th>Return Date</th><th>Status</th><th>Late Fee</th><th>Lost Fee</th></tr>';
            foreach ($records as $item) {
                echo '<tr>';
                foreach ([
                    'Borrowing ID',
                    'Book Title',
                    'Copy ID',
                    'Borrow Date',
                    'Due Date',
                    'Return Date',
                    'Status',
                    'Late Fee',
                    'Lost Fee'
                ] as $label) {
                    echo '<td>' . $this->h($item[$label] ?? '') . '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
    }

    public function renderPage(): void
    {
        $message = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrowing_action'])) {
            try {
                $action = $_POST['borrowing_action'];
                if ($action === 'borrow') {
                    $memberId = filter_input(INPUT_POST, 'member_id', FILTER_VALIDATE_INT);
                    $bookId = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
                    if (!$memberId || !$bookId) throw new InvalidArgumentException('Select a valid member and book.');
                    $this->renderBorrowOutput(fn() => $this->borrow((int)$memberId, (int)$bookId));
                } elseif ($action === 'return') {
                    $borrowingId = filter_input(INPUT_POST, 'borrowing_id', FILTER_VALIDATE_INT);
                    if (!$borrowingId) throw new InvalidArgumentException('Select a valid borrowing record.');
                    $reportedLost = ($_POST['book_lost'] ?? 'no') === 'yes';
                    $this->renderOutput(fn() => $this->returnBook((int)$borrowingId, $reportedLost));
                } elseif ($action === 'fines' || $action === 'history') {
                    $memberId = filter_input(INPUT_POST, 'member_id', FILTER_VALIDATE_INT);
                    if (!$memberId) throw new InvalidArgumentException('Select a valid member.');
                    $this->renderMemberReport($action, (int)$memberId);
                }
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }

        echo '<h2>Borrowing Management</h2>';
        if ($error !== '') echo '<p><strong>Error:</strong> ' . $this->h($error) . '</p>';

        echo '<h3>Borrow a Book</h3>';
        echo '<form method="post">';
        echo '<input type="hidden" name="borrowing_action" value="borrow">';
        echo '<label>Member: <select name="member_id" required><option value="">Select Member</option>';
        foreach ($this->memberOptions() as $m) {
            echo '<option value="' . $this->h($m['member_id']) . '">' . $this->h($m['name']) . ' (ID: ' . $this->h($m['member_id']) . ')</option>';
        }
        echo '</select></label> ';
        echo '<label>Book: <select name="book_id" required><option value="">Select Book</option>';
        foreach ($this->bookOptions() as $b) {
            echo '<option value="' . $this->h($b['book_id']) . '">' . $this->h($b['title']) . ' (ID: ' . $this->h($b['book_id']) . ')</option>';
        }
        echo '</select></label> ';
        echo '<button type="submit">Borrow Book</button></form>';

        echo '<hr><h3>Return a Book</h3>';
        $active = $this->activeBorrowingOptions();
        if (!$active) {
            echo '<p>There are no books currently borrowed.</p>';
        } else {
            echo '<form method="post"><input type="hidden" name="borrowing_action" value="return">';
            echo '<label>Borrowing Record: <select name="borrowing_id" required><option value="">Select Borrowing</option>';
            foreach ($active as $r) {
                $label = '#' . $r['borrowing_id'] . ' - ' . $r['name'] . ' - ' . $r['title'];
                echo '<option value="' . $this->h($r['borrowing_id']) . '">' . $this->h($label) . '</option>';
            }
            echo '</select></label> ';
            echo '<label>Was the book lost? <select name="book_lost"><option value="no">No</option><option value="yes">Yes</option></select></label> ';
            echo '<button type="submit">Process Return</button></form>';
        }

        echo '<hr><h3>View Member Fines</h3>';
        echo '<form method="post"><input type="hidden" name="borrowing_action" value="fines">';
        echo '<label>Member: <select name="member_id" required><option value="">Select Member</option>';
        foreach ($this->memberOptions() as $m) {
            echo '<option value="' . $this->h($m['member_id']) . '">' . $this->h($m['name']) . ' (ID: ' . $this->h($m['member_id']) . ')</option>';
        }
        echo '</select></label> <button type="submit">View All Fines</button></form>';

        echo '<hr><h3>View Member Borrowing History</h3>';
        echo '<form method="post"><input type="hidden" name="borrowing_action" value="history">';
        echo '<label>Member: <select name="member_id" required><option value="">Select Member</option>';
        foreach ($this->memberOptions() as $m) {
            echo '<option value="' . $this->h($m['member_id']) . '">' . $this->h($m['name']) . ' (ID: ' . $this->h($m['member_id']) . ')</option>';
        }
        echo '</select></label> <button type="submit">View Borrowing History</button></form>';

        echo '<hr>';
        $this->renderBorrowingTable();
        echo '<hr>';
        $this->renderBorrowingDaysTable();
    }

}
?>
