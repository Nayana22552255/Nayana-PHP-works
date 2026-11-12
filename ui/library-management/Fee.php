<?php
require_once "member.php";
require_once __DIR__ . '/Borrowing.php';

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

   /* public function processOverdueBorrowings(): void
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
    }*/

            
    public function processOverdueBorrowings(): void
    {
        try {
            $this->conn->beginTransaction();

            // Find all overdue books that have not been returned
            $sql = "SELECT
                    br.borrowing_id,
                    br.due_date,
                    mf.member_category AS category
                    FROM borrowings br
                    INNER JOIN members m
                    ON br.member_id = m.member_id
                    INNER JOIN member_fee mf
                    ON mf.category_id = m.category_id
                    WHERE br.return_date IS NULL
                    AND br.due_date < CURDATE()
                    FOR UPDATE
            ";

            $stmt = $this->conn->query($sql);
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($records as $record) {

                $borrowingId = (int) $record['borrowing_id'];

                // Get the member's daily late-fee rate
                $dailyRate = self::lateFeeRateForCategory(
                    $record['category']
                );

                // Find existing unpaid late-fee records
                $pendingQuery = $this->conn->prepare(
                    "SELECT fee_id, fee_date
                    FROM fees
                    WHERE borrowing_id = ?
                    AND fee_type = 'Late fee'
                    AND payment_status = 'pending'
                    ORDER BY fee_date, fee_id
                    FOR UPDATE"
                );

                $pendingQuery->execute([$borrowingId]);
                $pendingFees = $pendingQuery->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($pendingFees)) {

                    // Keep the earliest pending record
                    $feeId = (int) $pendingFees[0]['fee_id'];
                    $startDate = $pendingFees[0]['fee_date'];

                    // Remove additional pending daily records
                    if (count($pendingFees) > 1) {

                        $extraIds = array_map(
                            'intval',
                            array_column(
                                array_slice($pendingFees, 1),
                                'fee_id'
                            )
                        );

                        $placeholders = implode(
                            ',',
                            array_fill(0, count($extraIds), '?')
                        );

                        $delete = $this->conn->prepare(
                            "DELETE FROM fees
                            WHERE fee_id IN ($placeholders)"
                        );

                        $delete->execute($extraIds);
                    }

                } else {

                    // Find the last payment date, if any
                    $paidQuery = $this->conn->prepare(
                        "SELECT MAX(COALESCE(payment_date, fee_date))
                        FROM fees
                        WHERE borrowing_id = ?
                        AND fee_type = 'Late fee'
                        AND payment_status = 'paid'"
                    );

                    $paidQuery->execute([$borrowingId]);
                    $lastPaidDate = $paidQuery->fetchColumn();

                    // First overdue day is the day after the due date
                    $startDate = date(
                        'Y-m-d',
                        strtotime($record['due_date'] . ' +1 day')
                    );

                    // If a previous fee was paid, start the next period
                    // on the day after that payment.
                    if ($lastPaidDate !== null) {
                        $nextStartDate = date(
                            'Y-m-d',
                            strtotime($lastPaidDate . ' +1 day')
                        );

                        if ($nextStartDate > $startDate) {
                            $startDate = $nextStartDate;
                        }
                    }

                    // No overdue days remain unpaid yet
                    if ($startDate > date('Y-m-d')) {
                        continue;
                    }

                    // Create one pending fee record
                    $insert = $this->conn->prepare(
                        "INSERT INTO fees
                            (borrowing_id, fee_type, amount,
                            fee_date, payment_status)
                        VALUES (?, 'Late fee', 0, ?, 'pending')"
                    );

                    $insert->execute([
                        $borrowingId,
                        $startDate
                    ]);

                    $feeId = (int) $this->conn->lastInsertId();
                }

                // Calculate cumulative fee from the starting date
                $start= new DateTimeImmutable($startDate);
                $today=new DateTimeImmutable(date('Y-m-d'));
                $days=(int) $start->diff($today)->days + 1;

                $totalAmount = $days * $dailyRate;

                // Update the same pending row
                $updateFee = $this->conn->prepare(
                    "UPDATE fees
                    SET amount = ?
                    WHERE fee_id = ?
                    AND payment_status = 'pending'"
                );

                $updateFee->execute([
                    $totalAmount,
                    $feeId
                ]);
            }

            // Update the cumulative late fee in borrowings
            $updateBorrowing = $this->conn->prepare(
                "UPDATE borrowings br
                SET br.late_fee = (
                    SELECT COALESCE(SUM(f.amount), 0)
                    FROM fees f
                    WHERE f.borrowing_id = br.borrowing_id
                    AND f.fee_type = 'Late fee'
                )
                WHERE br.return_date IS NULL"
            );

            $updateBorrowing->execute();

            $this->conn->commit();

        } catch (Throwable $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }

            
        

    private function getFeeRecords(?string $keyword = null): array
    {
        $sql = "SELECT
                    f.fee_id,
                    f.borrowing_id,
                    m.member_id,
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
                    mf.member_category AS category,
                    mf.late_fee_per_day
                FROM fees f
                INNER JOIN borrowings br ON f.borrowing_id = br.borrowing_id
                INNER JOIN members m ON br.member_id = m.member_id
                LEFT JOIN member_fee mf ON mf.category_id = m.category_id
                INNER JOIN books b ON br.book_id = b.book_id";

        if ($keyword !== null) {
            $sql .= " WHERE m.name LIKE :name
                      OR f.payment_status LIKE :status
                      OR f.fee_type LIKE :type";
        }

        $sql .= " ORDER BY f.fee_id";
        $stmt = $this->conn->prepare($sql);

        if ($keyword !== null) {
            $like = '%' . $keyword . '%';
            $stmt->execute([
                ':name' => $like,
                ':status' => $like,
                ':type' => $like,
            ]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getFeeHistoryRecords(): array
    {
        $sql = "SELECT
                    f.fee_id,
                    f.borrowing_id,
                    m.member_id,
                    b.title AS book_title,
                    m.name AS member_name,
                    br.borrow_date,
                    f.fee_type,
                    f.amount,
                    f.fee_date,
                    f.payment_status,
                    f.payment_date
                FROM fees f
                INNER JOIN borrowings br ON f.borrowing_id = br.borrowing_id
                INNER JOIN books b ON br.book_id = b.book_id
                INNER JOIN members m ON br.member_id = m.member_id
                ORDER BY f.fee_date DESC, f.fee_id DESC";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function renderFeeRecordsTable(array $fees, bool $history = false): void
    {
        if ($fees === []) {
            echo '<p>No fee records found.</p>';
            return;
        }

        $columns = $history ? [
            'fee_id' => 'Fee ID',
            'borrowing_id' => 'Borrowing ID',
            'member_id' => 'Member ID',
            'book_title' => 'Book',
            'member_name' => 'Member',
            'borrow_date' => 'Borrowed On',
            'fee_type' => 'Fee Type',
            'amount' => 'Amount',
            'fee_date' => 'Fee Date',
            'payment_status' => 'Payment Status',
            'payment_date' => 'Payment Date',
        ] : [
            'fee_id' => 'Fee ID',
            'borrowing_id' => 'Borrowing ID',
            'member_id' => 'Member ID',
            'member_name' => 'Member',
            'book_title' => 'Book',
            'fee_type' => 'Fee Type',
            'amount' => 'Amount',
            'fee_date' => 'Fee Date',
            'payment_status' => 'Payment Status',
            'payment_date' => 'Payment Date',
            'due_date' => 'Due Date',
            'return_date' => 'Return Date',
            'overdue_days' => 'Overdue Days',
            'daily_rate' => 'Rate per Day',
            'period_total' => 'Total Late Fee for Period',
        ];

        echo '<div style="overflow-x:auto"><table border="1" cellpadding="6" cellspacing="0"><thead><tr>';
        foreach ($columns as $heading) {
            echo '<th>' . $this->h($heading) . '</th>';
        }
        echo '</tr></thead><tbody>';

        foreach ($fees as $fee) {
            if ($history) {
                $paymentStatus = strtolower((string) $fee['payment_status']);
                $values = [
                    'fee_id' => $fee['fee_id'],
                    'borrowing_id' => $fee['borrowing_id'],
                    'member_id' => $fee['member_id'],
                    'book_title' => $fee['book_title'],
                    'member_name' => $fee['member_name'],
                    'borrow_date' => $fee['borrow_date'],
                    'fee_type' => $fee['fee_type'],
                    'amount' => 'Rs. ' . number_format((float) $fee['amount'], 2),
                    'fee_date' => $fee['fee_date'],
                    'payment_status' => $paymentStatus === 'pending'
                        ? 'Unpaid'
                        : ucfirst($paymentStatus),
                    'payment_date' => $fee['payment_date'] ?? 'Not paid',
                ];

                echo '<tr>';
                foreach (array_keys($columns) as $key) {
                    echo '<td>' . $this->h($values[$key]) . '</td>';
                }
                echo '</tr>';
                continue;
            }

            $isLateFee = $fee['fee_type'] === 'Late fee';
            $dailyRate = $isLateFee && $fee['late_fee_per_day'] !== null
                ? (float) $fee['late_fee_per_day']
                : null;
            $values = [
                'fee_id' => $fee['fee_id'],
                'borrowing_id' => $fee['borrowing_id'],
                'member_id' => $fee['member_id'],
                'member_name' => $fee['member_name'],
                'book_title' => $fee['book_title'],
                'fee_type' => $fee['fee_type'],
                'amount' => 'Rs. ' . number_format((float) $fee['amount'], 2),
                'fee_date' => $fee['fee_date'],
                'payment_status' => $fee['payment_status'],
                'payment_date' => $fee['payment_date'] ?? 'Not paid',
                'due_date' => $fee['due_date'],
                'return_date' => $fee['return_date']
                    ?? 'Not returned (through ' . $fee['fee_through_date'] . ')',
                'overdue_days' => $isLateFee ? $fee['overdue_days'] : '-',
                'daily_rate' => $dailyRate === null
                    ? '-'
                    : 'Rs. ' . number_format($dailyRate, 2),
                'period_total' => $dailyRate === null
                    ? '-'
                    : 'Rs. ' . number_format(
                        (int) $fee['overdue_days'] * $dailyRate,
                        2
                    ),
            ];

            echo '<tr>';
            foreach (array_keys($columns) as $key) {
                echo '<td>' . $this->h($values[$key]) . '</td>';
            }
            echo '</tr>';
        }

        echo '</tbody></table></div>';
    }

    public function showAll(): void
    {
        try {
            $fees = $this->getFeeRecords();

            if (empty($fees)) {
                echo "No fee records found.\n";
                return;
            }

            echo "\n========== FEE RECORDS ==========\n";

            foreach ($fees as $fee) {
                echo "Fee ID: " . $fee['fee_id'] . "\n";
                echo "Borrowing ID: " . $fee['borrowing_id'] . "\n";
                echo "Member ID: " . $fee['member_id'] . "\n";
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
            $fees = $this->getFeeRecords($keyword);

            if (empty($fees)) {
                echo "No matching fee records found.\n";
                return;
            }

            foreach ($fees as $fee) {
                echo "Fee ID: " . $fee['fee_id'] . "\n";
                echo "Member ID: " . $fee['member_id'] . "\n";
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
                        mf.member_category AS category, b.title AS book_title
                 FROM borrowings br
                 INNER JOIN members m ON br.member_id = m.member_id
                 LEFT JOIN member_fee mf ON mf.category_id = m.category_id
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
        (new Borrowing($this->conn))->returnBook($borrowingId, true);
    }

    
    public function markPaid(int $feeId): bool
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
                return true;
            } else {
                echo "Fee not found or it has already been paid or waived.\n";
                return false;
            }

        } catch (PDOException $e) {
            echo "Error updating payment: " . $e->getMessage() . "\n";
            return false;
        }
    }

    
        
    public function viewAllFines(): void
    {
    do {
        echo "\n========== FINES AND REVENUE MENU ==========\n";
        echo "1. Fine History\n";
        echo "2. Total Fines\n";
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
                    m.member_id,
                    b.title AS book_title,
                    m.name AS member_name,
                    br.borrow_date,
                    f.fee_type,
                    f.amount,
                    f.fee_date,
                    f.payment_status,
                    f.payment_date
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
            $paymentStatus = strtolower((string) $fee['payment_status']);
            if ($paymentStatus === 'pending') {
                $paymentStatus = 'Unpaid';
            } else {
                $paymentStatus = ucfirst($paymentStatus);
            }

            echo "\nFee ID: " . $fee['fee_id'] . "\n";
            echo "Borrowing ID: " . $fee['borrowing_id'] . "\n";
            echo "Member ID: " . $fee['member_id'] . "\n";
            echo "Book: " . $fee['book_title'] . "\n";
            echo "Member: " . $fee['member_name'] . "\n";
            echo "Borrowed On: " . $fee['borrow_date'] . "\n";
            echo "Fee Type: " . $fee['fee_type'] . "\n";
            echo "Amount: Rs. "
                . number_format((float) $fee['amount'], 2) . "\n";
            echo "Fee Date: " . $fee['fee_date'] . "\n";
            echo "Payment Status: " . $paymentStatus . "\n";
            echo "Payment Date: " . ($fee['payment_date'] ?? 'Not paid') . "\n";
            echo "---------------------------------\n";
        }
    } catch (Throwable $e) {
        echo "Error displaying fee history: "
            . $e->getMessage() . "\n";
    }
    }


 
    private function getTotalFeeSummary(): array
    {
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
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function renderTotalFeesTable(array $totals): void
    {
        $rows = [
            'Total Fees (Late + Lost)' => $totals['total_fees'],
            'Total Late Fees' => $totals['total_late_fees'],
            'Total Lost Fees' => $totals['total_lost_fees'],
            'Total Revenue Collected' => $totals['total_revenue'],
            'Total Pending Fees' => $totals['pending_fees'],
        ];

        echo '<table border="1" cellpadding="6" cellspacing="0">';
        echo '<thead><tr><th>Fee Summary</th><th>Amount</th></tr></thead><tbody>';
        foreach ($rows as $label => $amount) {
            echo '<tr><th scope="row">' . $this->h($label) . '</th><td>Rs. '
                . number_format((float) $amount, 2) . '</td></tr>';
        }
        echo '</tbody></table>';
    }

    private function showTotalFees(): void
    {
    try {
        $totals = $this->getTotalFeeSummary();

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



    private function getRevenuePerBook(): array
    {
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function renderRevenuePerBookTable(array $books): void
    {
        if ($books === []) {
            echo '<p>No book revenue records found.</p>';
            return;
        }

        echo '<div style="overflow-x:auto"><table border="1" cellpadding="6" cellspacing="0">';
        echo '<thead><tr><th>Book ID</th><th>Book</th><th>Late Fee Revenue</th>'
            . '<th>Lost Fee Revenue</th><th>Total Revenue</th></tr></thead><tbody>';
        foreach ($books as $book) {
            echo '<tr>';
            echo '<td>' . $this->h($book['book_id']) . '</td>';
            echo '<td>' . $this->h($book['book_title']) . '</td>';
            echo '<td>Rs. ' . number_format((float) $book['late_fee_revenue'], 2) . '</td>';
            echo '<td>Rs. ' . number_format((float) $book['lost_fee_revenue'], 2) . '</td>';
            echo '<td>Rs. ' . number_format((float) $book['total_revenue'], 2) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    }

    private function showRevenuePerBook(): void
    {
        try {
            $books = $this->getRevenuePerBook();
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

    private function h(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    private function captureOutput(callable $callback): string
    {
        ob_start();
        try {
            $callback();
            return trim((string) ob_get_clean());
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }

    private function getActiveBorrowings(): array
    {
        $sql = "SELECT br.borrowing_id, m.name AS member_name,
                       b.title AS book_title
                FROM borrowings br
                JOIN members m ON m.member_id = br.member_id
                JOIN books b ON b.book_id = br.book_id
                WHERE br.return_date IS NULL
                ORDER BY br.borrowing_id DESC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function renderPage(): void
    {
        $result = '';
        $feeTable = null;
        $feeTableIsHistory = false;
        $totalFeeSummary = null;
        $revenueByBook = null;
        $error = '';

        try {
            $this->processOverdueBorrowings();
        } catch (Throwable $e) {
            $error = 'Unable to refresh overdue fees: ' . $e->getMessage();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $action = (string)($_POST['fee_action'] ?? '');
                if ($action === 'search') {
                    $feeTable = $this->getFeeRecords(
                        trim((string)($_POST['keyword'] ?? ''))
                    );
                } elseif ($action === 'lost') {
                    $borrowingId = filter_var(
                        $_POST['borrowing_id'] ?? null,
                        FILTER_VALIDATE_INT
                    );
                    if (!$borrowingId || $borrowingId < 1) {
                        throw new InvalidArgumentException('Select a borrowing record.');
                    }
                    $result = $this->captureOutput(
                        fn() => $this->addLostFee((int)$borrowingId)
                    );
                } elseif ($action === 'paid') {
                    $feeId = filter_var($_POST['fee_id'] ?? null, FILTER_VALIDATE_INT);
                    if (!$feeId || $feeId < 1) {
                        throw new InvalidArgumentException('Enter a valid Fee ID.');
                    }
                    $result = $this->captureOutput(
                        fn() => $this->markPaid((int)$feeId)
                    );
                } elseif ($action === 'all') {
                    $feeTable = $this->getFeeRecords();
                } elseif ($action === 'report') {
                    $report = (string)($_POST['report_type'] ?? '');
                    if ($report === 'history') {
                        $feeTable = $this->getFeeHistoryRecords();
                        $feeTableIsHistory = true;
                    } elseif ($report === 'totals') {
                        $totalFeeSummary = $this->getTotalFeeSummary();
                    } elseif ($report === 'revenue') {
                        $revenueByBook = $this->getRevenuePerBook();
                    } else {
                        throw new InvalidArgumentException('Select a valid report.');
                    }
                }
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }

        try {
            $borrowings = $this->getActiveBorrowings();
        } catch (Throwable $e) {
            $borrowings = [];
            $error = $error !== '' ? $error : $e->getMessage();
        }

        echo '<h2>Fee Management</h2>';
        if ($error !== '') {
            echo '<p><strong>Error:</strong> ' . $this->h($error) . '</p>';
        }
        if ($feeTable !== null) {
            echo '<section><h3>' . ($feeTableIsHistory ? 'Fee History' : 'Fee Records') . '</h3>';
            $this->renderFeeRecordsTable($feeTable, $feeTableIsHistory);
            echo '</section>';
        } elseif ($totalFeeSummary !== null) {
            echo '<section><h3>Total Fees Summary</h3>';
            $this->renderTotalFeesTable($totalFeeSummary);
            echo '</section>';
        } elseif ($revenueByBook !== null) {
            echo '<section><h3>Revenue per Book</h3>';
            $this->renderRevenuePerBookTable($revenueByBook);
            echo '</section>';
        } elseif ($result !== '') {
            echo '<section><h3>Result</h3><pre>' . $this->h($result) . '</pre></section>';
        }

        echo '<section><h3>Search Fees</h3>';
        echo '<form method="post">';
        echo '<input type="hidden" name="fee_action" value="search">';
        echo '<label>Member name, fee type, or payment status: ';
        echo '<input type="search" name="keyword" required></label> ';
        echo '<button type="submit">Search</button></form></section>';

        echo '<section><h3>Report a Lost Book</h3>';
        echo '<form method="post">';
        echo '<input type="hidden" name="fee_action" value="lost">';
        echo '<label>Borrowing record: <select name="borrowing_id" required>';
        echo '<option value="">Select a borrowed book</option>';
        foreach ($borrowings as $borrowing) {
            $label = '#' . $borrowing['borrowing_id'] . ' - '
                . $borrowing['member_name'] . ' - ' . $borrowing['book_title'];
            echo '<option value="' . $this->h($borrowing['borrowing_id']) . '">'
                . $this->h($label) . '</option>';
        }
        echo '</select></label> <button type="submit">Add Lost-Book Fee</button>';
        echo '</form></section>';

        echo '<section><h3>Mark a Fee as Paid</h3>';
        echo '<form method="post">';
        echo '<input type="hidden" name="fee_action" value="paid">';
        echo '<label>Fee ID: <input type="number" name="fee_id" min="1" required></label> ';
        echo '<button type="submit">Mark as Paid</button></form></section>';

        echo '<section><h3>Fee Records and Reports</h3>';
        echo '<form method="post">';
        echo '<input type="hidden" name="fee_action" value="all">';
        echo '<button type="submit">Show All Fee Records</button>';
        echo '</form>';
        echo '<form method="post">';
        echo '<input type="hidden" name="fee_action" value="report">';
        echo '<label>Report: <select name="report_type" required>';
        echo '<option value="">Select a report</option>';
        echo '<option value="history">Fee History</option>';
        echo '<option value="totals">Total Fees Summary</option>';
        echo '<option value="revenue">Revenue per Book</option>';
        echo '</select></label> <button type="submit">View Report</button>';
        echo '</form></section>';
    }

        //$stmt = $this->conn->query($sql);
        //$fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

     
}

?>
