
<?php
$patients = $patients ?? [];

function doctorPatientEscape($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Patients</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 0;
        }

        .navbar {
            width: 100%;
            background: #263b5e;
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h3 {
            margin: 0;
            text-align: left;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 90%;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h2 {
            color: #263b55;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 13px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #eaf1f8;
        }

        .view-btn {
            background: #1769aa;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 10px;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #777;
        }
        .dashboard-link {
            padding: 13px;
            text-align: center;
            
        }
    </style>
</head>
<body>

<div class="navbar">
    <h3>Younseii Hospital</h3>
  
</div>

<div class="container">

    <h2>My Patients</h2>
    <p>Patients who have appointments with you.</p>

    <table>
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Patient Name</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Last Appointment</th>
                <th>Total Appointments</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php if (!empty($patients)): ?>

            <?php foreach ($patients as $patient): ?>
                <tr>
                    <td>
                        <?= doctorPatientEscape($patient['patient_number'] ?? '') ?>
                    </td>

                    <td>
                        <?= doctorPatientEscape(
                            ($patient['first_name'] ?? '') . ' ' .
                            ($patient['last_name'] ?? '')
                        ) ?>
                    </td>

                    <td>
                        <?= doctorPatientEscape($patient['gender'] ?? '') ?>
                    </td>

                    <td>
                        <?= doctorPatientEscape($patient['phone'] ?? '') ?>
                    </td>

                    <td>
                        <?= doctorPatientEscape($patient['last_appointment'] ?? 'N/A') ?>
                    </td>

                    <td>
                        <?= (int)($patient['total_appointments'] ?? 0) ?>
                    </td>

                    <td>
                        <a class="view-btn"
                           href="/doctor/patients/show?id=<?= (int)$patient['id'] ?>">
                            View Details
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="7" class="empty">
                    No patients found.
                </td>
            </tr>

        <?php endif; ?>
        </tbody>
    </table>

    <div class="dashboard-link">
          <a href="/doctor/dashboard">Back to Dashboard</a>
    </div>

</div>

</body>
</html>
