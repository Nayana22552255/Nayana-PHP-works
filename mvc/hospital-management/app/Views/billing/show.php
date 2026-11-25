
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Details</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navigation.php'; ?>

<div class="container">

    <h2>Bill Details</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h3>Patient Information</h3>

    <p>
        <strong>Bill ID:</strong>
        <?= $bill['bill_id'] ?>
    </p>

    <p>
        <strong>Patient:</strong>
        <?= htmlspecialchars($bill['patient_name']) ?>
    </p>

    <p>
        <strong>Phone:</strong>
        <?= htmlspecialchars($bill['phone'] ?? '') ?>
    </p>

    <p>
        <strong>Doctor:</strong>
        <?= htmlspecialchars($bill['doctor_name']) ?>
    </p>

    <p>
        <strong>Appointment Date:</strong>
        <?= htmlspecialchars($bill['appointment_date']) ?>
    </p>

    <p>
        <strong>Bill Date:</strong>
        <?= htmlspecialchars($bill['bill_date']) ?>
    </p>

    <h3>Bill Items</h3>

    <table>

        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Amount</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($items as $item): ?>

            <tr>
                <td><?= htmlspecialchars($item['description']) ?></td>

                <td><?= $item['quantity'] ?></td>

                <td>₹<?= number_format($item['unit_price'], 2) ?></td>

                <td>
                    ₹<?= number_format(
                        $item['quantity'] * $item['unit_price'],
                        2
                    ) ?>
                </td>
            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <h3>Payment Summary</h3>

    <p>
        <strong>Total Amount:</strong>
        ₹<?= number_format($bill['total_amount'], 2) ?>
    </p>

    <p>
        <strong>Paid Amount:</strong>
        ₹<?= number_format($bill['paid_amount'], 2) ?>
    </p>

    <p>
        <strong>Outstanding Balance:</strong>
        ₹<?= number_format($balance, 2) ?>
    </p>

    <p>
        <strong>Payment Status:</strong>
        <?= htmlspecialchars($bill['payment_status']) ?>
    </p>

    <h3>Record Payment</h3>

    <?php if ($balance > 0): ?>

        <form
            action="/bills/payment?id=<?= $bill['bill_id'] ?>"
            method="POST">

            <label>Payment Amount (₹)</label>

            <input
                type="number"
                name="amount"
                min="0.01"
                max="<?= htmlspecialchars((string)$balance) ?>"
                step="0.01"
                required>

            <label>Payment Method</label>

            <select name="payment_method" required>
                <option value="">Select Payment Method</option>
                <option value="Cash">Cash</option>
                <option value="Card">Card</option>
                <option value="UPI">UPI</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>

            <button type="submit">Record Payment</button>

        </form>

    <?php else: ?>

        <p>Full payment has been received.</p>

    <?php endif; ?>

    <h3>Payment History</h3>

    <table>

        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Payment Date</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!empty($payments)): ?>

            <?php foreach ($payments as $payment): ?>

                <tr>
                    <td><?= $payment['payment_id'] ?></td>

                    <td>
                        ₹<?= number_format($payment['amount'], 2) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($payment['payment_method']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($payment['payment_date']) ?>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="4">No payments recorded.</td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

    <br>

    <a href="/bills/edit?id=<?= $bill['bill_id'] ?>">
        Edit Bill
    </a>

    <a href="/bills">
        Back to Bills
    </a>

</div>

</body>
</html>
