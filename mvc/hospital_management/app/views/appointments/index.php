
<?php
function appointmentEscape($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= appointmentEscape($title) ?></title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            width: 90%;
            margin: 40px auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px #ddd;
        }

        h1 {
            color: #263b5e;
        }

        .top-links {
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            background: #263b5e;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 5px;
        }

        .search-form {
            margin-bottom: 20px;
        }

        input {
            padding: 10px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 15px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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

        tr:nth-child(even) {
            background: #f8fafc;
        }

        .status {
            font-weight: bold;
        }

        .Scheduled {
            color: #2563eb;
        }

        .Confirmed {
            color: #059669;
        }

        .Completed {
            color: #15803d;
        }

        .Cancelled {
            color: #dc2626;
        }

        .No-show {
            color: #d97706;
        }

        .action-link {
            color: #2563eb;
            text-decoration: none;
            margin-right: 8px;
        }

        .empty {
            text-align: center;
            padding: 25px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <h1>Appointment Management</h1>

        <div class="top-links">

            <a href="/" class="button">
                Dashboard
            </a>

            <a href="/appointments/create" class="button">
                Book New Appointment
            </a>

        </div>

        <form action="/appointments" method="GET" class="search-form">

            <input
                type="text"
                name="search"
                placeholder="Search appointment, patient or doctor"
                value="<?= appointmentEscape($keyword ?? '') ?>"
            >

            <button type="submit">Search</button>

            <a href="/appointments" class="action-link">
                Clear
            </a>

        </form>

        <h3>Total Appointments: <?= count($appointments) ?></h3>

        <table>

            <thead>
                <tr>
                    <th>Appointment No.</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($appointments)): ?>

                <?php foreach ($appointments as $appointment): ?>

                    <tr>

                        <td>
                            <?= appointmentEscape($appointment['appointment_number']) ?>
                        </td>

                        <td>
                            <?= appointmentEscape(
                                $appointment['patient_first_name'] . ' ' .
                                $appointment['patient_last_name']
                            ) ?>
                        </td>

                        <td>
                            <?= appointmentEscape(
                                $appointment['doctor_first_name'] . ' ' .
                                $appointment['doctor_last_name']
                            ) ?>
                        </td>

                        <td>
                            <?= appointmentEscape($appointment['department_name'] ?? 'Not assigned') ?>
                        </td>

                        <td>
                            <?= appointmentEscape($appointment['appointment_date']) ?>
                        </td>

                        <td>
                            <?= appointmentEscape(date(
                                'h:i A',
                                strtotime($appointment['appointment_time'])
                            )) ?>
                        </td>

                        <td class="status <?= appointmentEscape(str_replace('-', '', $appointment['status'])) ?>">
                            <?= appointmentEscape($appointment['status']) ?>
                        </td>

                        <td>
                            <a
                                href="/appointments/show?id=<?= (int)$appointment['id'] ?>"
                                class="action-link"
                            >
                                View
                            </a>

                            <?php if (in_array($appointment['status'], ['Scheduled', 'Confirmed'], true)): ?>

                                <a
                                    href="/appointments/edit?id=<?= (int)$appointment['id'] ?>"
                                    class="action-link"
                                >
                                    Edit
                                </a>

                            <?php endif; ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8" class="empty">
                        No appointments found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>
