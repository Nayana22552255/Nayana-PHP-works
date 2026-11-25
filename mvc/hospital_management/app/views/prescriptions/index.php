
<?php
function prescriptionIndexEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= prescriptionIndexEscape($title) ?></title>

    <style>
         * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
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
            gap: 15px;
            flex-wrap: wrap;
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

        .table-wrapper {
            overflow-x: auto;
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

        .active {
            color: #15803d;
        }

        .completed {
            color: #2563eb;
        }

        .cancelled {
            color: #dc2626;
        }
    </style>
</head>

<body>
   <div class="navbar">

        <h3>Younseii Hospital System</h3>

    </div>

<div class="container">

    <div class="header">
        <h2>Prescription Management</h2>

        <div>
            <a href="/" class="btn">Dashboard</a>
            <a href="/prescriptions/create" class="btn">
                Create Prescription
            </a>
        </div>
    </div>

    <form method="GET" action="/prescriptions" class="search">

        <input
            type="text"
            name="search"
            placeholder="Search prescription or patient..."
            value="<?= prescriptionIndexEscape($keyword) ?>"
        >

        <button type="submit">Search</button>

    </form>

    <div class="table-wrapper">

        <table>

            <thead>
                <tr>
                    <th>Prescription Number</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($prescriptions)): ?>

                <?php foreach ($prescriptions as $prescription): ?>

                    <?php
                    $statusClass = strtolower($prescription['status']);
                    ?>

                    <tr>

                        <td>
                            <?= prescriptionIndexEscape(
                                $prescription['prescription_number']
                            ) ?>
                        </td>

                        <td>
                            <?= prescriptionIndexEscape(
                                $prescription['patient_first_name'] . ' ' .
                                $prescription['patient_last_name']
                            ) ?>
                        </td>

                        <td>
                            <?= prescriptionIndexEscape(
                                $prescription['doctor_first_name'] . ' ' .
                                $prescription['doctor_last_name']
                            ) ?>
                        </td>

                        <td>
                            <?= prescriptionIndexEscape(
                                $prescription['prescription_date']
                            ) ?>
                        </td>

                        <td class="status <?= prescriptionIndexEscape($statusClass) ?>">
                            <?= prescriptionIndexEscape(
                                $prescription['status']
                            ) ?>
                        </td>

                        <td>
                            <a href="/prescriptions/show?id=<?= (int) $prescription['id'] ?>">
                                View
                            </a>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6">No prescriptions found.</td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>
