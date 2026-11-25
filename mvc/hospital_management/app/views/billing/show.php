
<?php
function billingShowEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= billingShowEscape($title) ?></title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            max-width: 950px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 8px;
        }

        h2, h3 {
            color: #263b5e;
        }

        .details {
            line-height: 1.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 11px;
            text-align: left;
        }

        th {
            background: #263b5e;
            color: white;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 7px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            margin-top: 15px;
            background: #263b5e;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .summary {
            max-width: 350px;
            margin-left: auto;
            line-height: 2;
        }

        .status {
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .container {
                margin: 0;
                max-width: none;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Patient Bill</h2>

    <div class="details">

        <p>
            <strong>Bill Number:</strong>
            <?= billingShowEscape($bill['bill_number']) ?>
        </p>

        <p>
            <strong>Bill Date:</strong>
            <?= billingShowEscape($bill['bill_date']) ?>
        </p>

        <p>
            <strong>Patient:</strong>
            <?= billingShowEscape(
                $bill['first_name'] . ' ' . $bill['last_name']
            ) ?>
        </p>

        <p>
            <strong>Patient Number:</strong>
            <?= billingShowEscape($bill['patient_number']) ?>
        </p>

        <p>
            <strong>Phone:</strong>
            <?= billingShowEscape($bill['phone']) ?>
        </p>

    </div>

    <h3>Bill Items</h3>

    <table>

        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($items as $item): ?>

            <tr>
                <td>
                    <?= billingShowEscape($item['item_name']) ?>
                </td>

                <td>
                    <?= (int) $item['quantity'] ?>
                </td>

                <td>
                    ₹<?= number_format((float) $item['unit_price'], 2) ?>
                </td>

                <td>
                    ₹<?= number_format((float) $item['total_price'], 2) ?>
                </td>
            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <div class="summary">

        <p>
            Subtotal:
            ₹<?= number_format((float) $bill['subtotal'], 2) ?>
        </p>

        <p>
            Discount:
            ₹<?= number_format((float) $bill['discount'], 2) ?>
        </p>

        <p>
            Tax:
            ₹<?= number_format((float) $bill['tax'], 2) ?>
        </p>

        <h3>
            Total:
            ₹<?= number_format((float) $bill['total_amount'], 2) ?>
        </h3>

        <p>
            Paid:
            ₹<?= number_format((float) $bill['paid_amount'], 2) ?>
        </p>

        <p>
            Balance:
            ₹<?= number_format((float) $bill['balance_amount'], 2) ?>
        </p>

        <p>
            <strong>Payment Status:</strong>
            <span class="status">
                <?= billingShowEscape($bill['payment_status']) ?>
            </span>
        </p>

    </div>

    <h3>Payment History</h3>

    <table>

        <thead>
            <tr>
                <th>Payment Number</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Date</th>
                <th>Reference</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!empty($payments)): ?>

            <?php foreach ($payments as $payment): ?>

                <tr>
                    <td>
                        <?= billingShowEscape($payment['payment_number']) ?>
                    </td>

                    <td>
                        ₹<?= number_format((float) $payment['amount'], 2) ?>
                    </td>

                    <td>
                        <?= billingShowEscape($payment['payment_method']) ?>
                    </td>

                    <td>
                        <?= billingShowEscape($payment['payment_date']) ?>
                    </td>

                    <td>
                        <?= billingShowEscape(
                            $payment['transaction_reference'] ?? ''
                        ) ?>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="5">No payments recorded yet.</td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

    <?php if ((float) $bill['balance_amount'] > 0): ?>

        <div class="no-print">

            <h3>Record Payment</h3>

            <form method="POST" action="/billing/payment">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= billingShowEscape($csrfToken) ?>"
                >

                <input
                    type="hidden"
                    name="bill_id"
                    value="<?= (int) $bill['id'] ?>"
                >

                <label>Payment Amount (₹) *</label>

                <input
                    type="number"
                    name="amount"
                    min="0.01"
                    max="<?= billingShowEscape($bill['balance_amount']) ?>"
                    step="0.01"
                    required
                >

                <label>Payment Method *</label>

                <select name="payment_method" required>

                    <option value="">-- Select Method --</option>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="UPI">UPI</option>
                    <option value="Bank Transfer">Bank Transfer</option>

                </select>

                <label>Payment Date and Time *</label>

                <input
                    type="datetime-local"
                    name="payment_date"
                    value="<?= date('Y-m-d\TH:i') ?>"
                    required
                >

                <label>Transaction Reference</label>

                <input
                    type="text"
                    name="transaction_reference"
                    placeholder="Optional transaction reference"
                >

                <label>Notes</label>

                <textarea name="notes" rows="3"></textarea>

                <button type="submit" class="btn">
                    Record Payment
                </button>

            </form>

        </div>

    <?php endif; ?>

    <div class="no-print">

                <button type="button" onclick="printBill()" class="btn">
            Print Bill
        </button>

        <script>
        function printBill() {
            window.focus();
            window.print();
        }
        </script>

        <a href="/billing" class="btn">
            Back to Billing
        </a>

    </div>

</div>

</body>
</html>
