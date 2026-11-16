<?php
/**
 * Book Renewal Management
 * Standalone page for the existing Library Management System.
 * Uses the existing Database.php connection ($conn) and existing tables.
 */
require_once __DIR__ . '/Database.php';

if (!isset($conn) || !$conn instanceof PDO) {
    die('Database connection is not available. Check Database.php.');
}
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// This additional table stores renewal history without changing existing tables.
$conn->exec("CREATE TABLE IF NOT EXISTS renewal_history (
    renewal_id INT AUTO_INCREMENT PRIMARY KEY,
    borrowing_id INT NOT NULL,
    renewal_no INT NOT NULL,
    old_due_date DATE NOT NULL,
    new_due_date DATE NOT NULL,
    renewal_date DATE NOT NULL,
    UNIQUE KEY uq_borrowing_renewal (borrowing_id, renewal_no),
    CONSTRAINT fk_renewal_borrowing
        FOREIGN KEY (borrowing_id) REFERENCES borrowings(borrowing_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

function h(mixed $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$message = '';
$messageType = 'info';
$maxRenewals = 2; // Change this value if your library allows a different limit.

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'renew') {
    $borrowingId = filter_input(INPUT_POST, 'borrowing_id', FILTER_VALIDATE_INT);

    if (!$borrowingId || $borrowingId < 1) {
        $message = 'Please select a valid active borrowing record.';
        $messageType = 'error';
    } else {
        try {
            $conn->beginTransaction();

            // Lock the active borrowing and read the member's loan period.
            $query = $conn->prepare("SELECT br.borrowing_id, br.member_id, br.book_id,
                                            br.due_date, br.return_date,
                                            m.name AS member_name,
                                            mf.loan_days,
                                            b.title AS book_title
                                     FROM borrowings br
                                     INNER JOIN members m ON m.member_id = br.member_id
                                     INNER JOIN member_fee mf ON mf.category_id = m.category_id
                                     INNER JOIN books b ON b.book_id = br.book_id
                                     WHERE br.borrowing_id = ?
                                       AND br.return_date IS NULL
                                     FOR UPDATE");
            $query->execute([$borrowingId]);
            $borrowing = $query->fetch(PDO::FETCH_ASSOC);

            if (!$borrowing) {
                throw new RuntimeException('Borrowing record not found, or the book has already been returned.');
            }

            // Renewals are allowed only on or before the current due date.
            if ($borrowing['due_date'] < date('Y-m-d')) {
                throw new RuntimeException('This book is overdue. Clear the overdue/late-fee issue before renewing it.');
            }

            // Do not allow renewal while an unpaid late fee exists for this borrowing.
            $feeCheck = $conn->prepare("SELECT COUNT(*) FROM fees
                                        WHERE borrowing_id = ?
                                          AND fee_type = 'Late fee'
                                          AND payment_status = 'pending'");
            $feeCheck->execute([$borrowingId]);
            if ((int)$feeCheck->fetchColumn() > 0) {
                throw new RuntimeException('This borrowing has a pending late fee and cannot be renewed.');
            }

            $countQuery = $conn->prepare('SELECT COUNT(*) FROM renewal_history WHERE borrowing_id = ?');
            $countQuery->execute([$borrowingId]);
            $renewalCount = (int)$countQuery->fetchColumn();

            if ($renewalCount >= $maxRenewals) {
                throw new RuntimeException('Maximum number of renewals (' . $maxRenewals . ') has been reached.');
            }

            $loanDays = max(1, (int)$borrowing['loan_days']);
            $oldDueDate = $borrowing['due_date'];

            // Extend from the existing due date, not from today's date.
            $dateQuery = $conn->prepare('SELECT DATE_ADD(?, INTERVAL ? DAY)');
            $dateQuery->execute([$oldDueDate, $loanDays]);
            $newDueDate = (string)$dateQuery->fetchColumn();

            $update = $conn->prepare("UPDATE borrowings
                                      SET due_date = ?
                                      WHERE borrowing_id = ? AND return_date IS NULL");
            $update->execute([$newDueDate, $borrowingId]);
            if ($update->rowCount() !== 1) {
                throw new RuntimeException('The due date could not be updated.');
            }

            $renewalNo = $renewalCount + 1;
            $history = $conn->prepare("INSERT INTO renewal_history
                                       (borrowing_id, renewal_no, old_due_date, new_due_date, renewal_date)
                                       VALUES (?, ?, ?, ?, CURDATE())");
            $history->execute([$borrowingId, $renewalNo, $oldDueDate, $newDueDate]);

            $conn->commit();
            $message = 'Book renewed successfully. New due date: ' . $newDueDate .
                       '. Renewal ' . $renewalNo . ' of ' . $maxRenewals . '.';
            $messageType = 'success';
        } catch (Throwable $error) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $message = $error->getMessage();
            $messageType = 'error';
        }
    }
}

$activeBorrowings = $conn->query("SELECT br.borrowing_id, br.member_id, m.name AS member_name,
                                         b.title AS book_title, br.copy_id,
                                         br.borrow_date, br.due_date,
                                         (SELECT COUNT(*) FROM renewal_history rh
                                          WHERE rh.borrowing_id = br.borrowing_id) AS renewals_used
                                  FROM borrowings br
                                  INNER JOIN members m ON m.member_id = br.member_id
                                  INNER JOIN books b ON b.book_id = br.book_id
                                  WHERE br.return_date IS NULL
                                  ORDER BY br.due_date, br.borrowing_id DESC")
                         ->fetchAll(PDO::FETCH_ASSOC);

$renewalHistory = $conn->query("SELECT rh.renewal_id, rh.borrowing_id, m.name AS member_name,
                                       b.title AS book_title, rh.renewal_no,
                                       rh.old_due_date, rh.new_due_date, rh.renewal_date
                                FROM renewal_history rh
                                INNER JOIN borrowings br ON br.borrowing_id = rh.borrowing_id
                                INNER JOIN members m ON m.member_id = br.member_id
                                INNER JOIN books b ON b.book_id = br.book_id
                                ORDER BY rh.renewal_id DESC")
                       ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Renewal Management</title>
</head>
<body>
    <h1>BOOK RENEWAL MANAGEMENT</h1>
    <p><a href="index.php">Main Menu</a> | <a href="index.php?page=borrowings">Borrowing Management</a></p>
    <hr>

    <?php if ($message !== ''): ?>
        <p><strong><?= h($message) ?></strong></p>
    <?php endif; ?>

    <h2>Renew a Book</h2>

    <?php if (!$activeBorrowings): ?>
        <p>There are no currently borrowed books to renew.</p>
    <?php else: ?>
        <form method="post" action="Renewal.php">
            <input type="hidden" name="action" value="renew">
            <label for="borrowing_id">Active borrowing:</label>
            <select name="borrowing_id" id="borrowing_id" required>
                <option value="">-- Select borrowing --</option>
                <?php foreach ($activeBorrowings as $item): ?>
                    <?php
                    $isOverdue = $item['due_date'] < date('Y-m-d');
                    $atLimit = (int)$item['renewals_used'] >= $maxRenewals;
                    ?>
                    <option value="<?= h($item['borrowing_id']) ?>"
                        <?= ($isOverdue || $atLimit) ? 'disabled' : '' ?>>
                        <?= h('#' . $item['borrowing_id'] . ' - ' . $item['member_name'] .
                            ' - ' . $item['book_title'] . ' - Copy ' . $item['copy_id'] .
                            ' - Due: ' . $item['due_date'] .
                            ' - Renewals: ' . $item['renewals_used'] . '/' . $maxRenewals .
                            ($isOverdue ? ' (Overdue)' : '') .
                            ($atLimit ? ' (Limit reached)' : '')) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Renew Book</button>
        </form>
    <?php endif; ?>

    <h2>Currently Borrowed Books</h2>
    <?php if (!$activeBorrowings): ?>
        <p>No active borrowing records found.</p>
    <?php else: ?>
        <table border="1" cellpadding="7" cellspacing="0">
            <tr>
                <th>Borrowing ID</th><th>Member</th><th>Book</th><th>Copy ID</th>
                <th>Borrow Date</th><th>Current Due Date</th><th>Renewals Used</th><th>Status</th>
            </tr>
            <?php foreach ($activeBorrowings as $item): ?>
                <tr>
                    <td><?= h($item['borrowing_id']) ?></td>
                    <td><?= h($item['member_name']) ?></td>
                    <td><?= h($item['book_title']) ?></td>
                    <td><?= h($item['copy_id']) ?></td>
                    <td><?= h($item['borrow_date']) ?></td>
                    <td><?= h($item['due_date']) ?></td>
                    <td><?= h($item['renewals_used']) ?> / <?= $maxRenewals ?></td>
                    <td><?= $item['due_date'] < date('Y-m-d') ? 'Overdue' : 'Active' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <h2>Renewal History</h2>
    <?php if (!$renewalHistory): ?>
        <p>No renewal history found.</p>
    <?php else: ?>
        <table border="1" cellpadding="7" cellspacing="0">
            <tr>
                <th>Renewal ID</th><th>Borrowing ID</th><th>Member</th><th>Book</th>
                <th>Renewal No.</th><th>Previous Due Date</th><th>New Due Date</th><th>Renewal Date</th>
            </tr>
            <?php foreach ($renewalHistory as $history): ?>
                <tr>
                    <td><?= h($history['renewal_id']) ?></td>
                    <td><?= h($history['borrowing_id']) ?></td>
                    <td><?= h($history['member_name']) ?></td>
                    <td><?= h($history['book_title']) ?></td>
                    <td><?= h($history['renewal_no']) ?></td>
                    <td><?= h($history['old_due_date']) ?></td>
                    <td><?= h($history['new_due_date']) ?></td>
                    <td><?= h($history['renewal_date']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
