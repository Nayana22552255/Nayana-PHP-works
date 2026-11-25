
<?php
function billingIndexEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= billingIndexEscape($title) ?></title>

    <style>
         * {
            box-sizing: border-box;
        }
        body {
           
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f6f9;
            overflow:hidden;
        }
         .navbar {
            background: #263b5e;
            color: white;
            padding: 18px 30px;
           
        }

        .container {
            max-width: 90%;
            margin: 35px auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom:25px;
           
            
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            background: #263b5e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .search {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }

        input {
            padding: 10px;
            width: 280px;
            max-width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 16px;
            background: #263b5e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .reset-btn {
    display: inline-block;
    padding: 10px 16px;
    background: #64748b;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
    transition: 0.3s ease;
}

.reset-btn:hover {
    background: #475569;
}

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #263b5e;
            color: white;
        }

        .status {
            font-weight: bold;
        }

        .pending {
            color: #dc2626;
        }

        .partial {
            color: #d97706;
        }

        .paid {
            color: #15803d;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        @media(max-width: 700px) {
            .container {
                width:96%
            
            }
             table {
                min-width: 650px;
            }
        }
    </style>
</head>

<body>



    <div class="navbar">

        <h3>Younseii Hospital System</h3>

    </div>
    <div class="container">

    <div class="header">

        <h2>Billing Management</h2>

        <div>
            <a href="/" class="btn">Dashboard</a>
            <a href="/billing/create" class="btn">Create New Bill</a>
        </div>
    </div>

    <form method="GET" action="/billing" class="search">

        <input
            type="text"
            name="search"
            placeholder="Search bill or patient..."
            value="<?= billingIndexEscape($keyword) ?>"
        >

        <button type="submit">Search</button>
          <a href="/billing" class="reset-btn">
            Reset
        </a>

    </form>

    <div class="table-wrapper">

        <table>

            <thead>
                <tr>
                    <th>Bill Number</th>
                    <th>Patient</th>
                    <th>Bill Date</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($bills)): ?>

                <?php foreach ($bills as $bill): ?>

                    <?php
                    $statusClass = 'pending';

                    if ($bill['payment_status'] === 'Paid') {
                        $statusClass = 'paid';
                    } elseif ($bill['payment_status'] === 'Partially Paid') {
                        $statusClass = 'partial';
                    }
                    ?>

                    <tr>

                        <td>
                            <?= billingIndexEscape($bill['bill_number']) ?>
                        </td>

                        <td>
                            <?= billingIndexEscape(
                                $bill['first_name'] . ' ' . $bill['last_name']
                            ) ?>
                        </td>

                        <td>
                            <?= billingIndexEscape($bill['bill_date']) ?>
                        </td>

                        <td>
                            ₹<?= number_format((float) $bill['total_amount'], 2) ?>
                        </td>

                        <td>
                            ₹<?= number_format((float) $bill['paid_amount'], 2) ?>
                        </td>

                        <td>
                            ₹<?= number_format((float) $bill['balance_amount'], 2) ?>
                        </td>

                        <td class="status <?= $statusClass ?>">
                            <?= billingIndexEscape($bill['payment_status']) ?>
                        </td>

                        <td>
                            <a href="/billing/show?id=<?= (int) $bill['id'] ?>">
                                View
                            </a>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8">No bills found.</td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>


</div>

</body>
</html>
