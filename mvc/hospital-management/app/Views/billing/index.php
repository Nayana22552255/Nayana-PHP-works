
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Management</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navigation.php'; ?>

<div class="container">

    <h2>Bill Management</h2>

    <a href="/bills/create">+ Create New Bill</a>

    <br><br>

    <form action="/bills/search" method="GET">

        <input
            type="text"
            name="keyword"
            placeholder="Search patient or doctor"
            value="<?= htmlspecialchars($keyword ?? '') ?>">

        <button type="submit">Search</button>

        <a href="/bills">Reset</a>

    </form>

    <br>

    <table>

        <thead>
            <tr>
                <th>Bill ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Bill Date</th>
                <th>Total Amount</th>
                <th>Paid Amount</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!empty($bills)): ?>

            <?php foreach ($bills as $bill): ?>

                <?php
                $balance = max(
                    0,
                    (float)$bill['total_amount'] -
                    (float)$bill['paid_amount']
                );
                ?>

                <tr>

                    <td><?= $bill['bill_id'] ?></td>

                    <td><?= htmlspecialchars($bill['patient_name']) ?></td>

                    <td><?= htmlspecialchars($bill['doctor_name']) ?></td>

                    <td><?= htmlspecialchars($bill['bill_date']) ?></td>

                    <td>₹<?= number_format($bill['total_amount'], 2) ?></td>

                    <td>₹<?= number_format($bill['paid_amount'], 2) ?></td>

                    <td>₹<?= number_format($balance, 2) ?></td>

                    <td><?= htmlspecialchars($bill['payment_status']) ?></td>

                    <td>

                        <a href="/bills/show?id=<?= $bill['bill_id'] ?>">
                            View
                        </a>

                        <a href="/bills/edit?id=<?= $bill['bill_id'] ?>">
                            Edit
                        </a>

                        <form
                            action="/bills/delete?id=<?= $bill['bill_id'] ?>"
                            method="POST"
                            style="display:inline;"
                            onsubmit="return confirm('Delete this bill and its payment records?')">

                            <button type="submit">Delete</button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="9">No bills found.</td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>

</body>
</html>
