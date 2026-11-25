<?php
$prescriptions = $prescriptions ?? [];

function doctorPrescriptionListEscape($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Prescriptions</title>
    <style>
        body 
        { margin: 0; 
        font-family: Arial, sans-serif;
         background: #f4f7fb; 
        }
        .navbar { 
            background: #263b5e; 
            color: white; 
            padding: 18px 30px; 
        }
        .container { 
            max-width: 1100px; 
            margin: 35px auto; 
            padding: 20px; 
        }
        h2 { 
            color: #263b5e; 
        }
        .toolbar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .back { 
            background: #263b5e; 
            color: white; 
            padding: 10px 16px; 
            border-radius: 5px; 
            text-decoration: none; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            background: white; 
        }
        th, td { 
            border: 1px solid #ddd;
             padding: 12px;
              text-align: left; 
            }
        th {
             background: #263b5e; 
             color: white; 
            }
        .view { 
            color: #2563eb; 
            text-decoration: none; 
            font-weight: bold; 
        }
        .empty {
             text-align: center;
              color: #64748b; 
            }
            .create-prescription {
                background: #16a34a;
                color: white;
                padding: 10px 16px;
                border-radius: 5px;
                text-decoration: none;
                font-weight: bold;
                margin-right: 10px;
            }

            .create-prescription:hover {
                background: #15803d;
            }

            .toolbar-actions {
                display: flex;
                align-items: center;
                gap: 10px;
            }
        @media (max-width: 700px) { 
            .container {
                
            } table { font-size: 13px; } 
        }
    </style>
</head>
<body>
    <div class="navbar"><h3>Younseii Hospital System</h3></div>
    <div class="container">
        <div class="toolbar">

    <h2>My Prescriptions</h2>

    <div class="toolbar-actions">

        <a class="create-prescription"
           href="/prescriptions/create">
            + Create New Prescription
        </a>

        <a class="back" href="/doctor/dashboard">
            Dashboard
        </a>

    </div>

</div>
        <table>
            <thead>
                <tr>
                    <th>Prescription Number</th>
                    <th>Patient</th>
                    <th>Patient ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($prescriptions)): ?>
                <?php foreach ($prescriptions as $prescription): ?>
                    <tr>
                        <td><?= doctorPrescriptionListEscape($prescription['prescription_number'] ?? '') ?></td>
                        <td><?= doctorPrescriptionListEscape(($prescription['patient_first_name'] ?? '') . ' ' . ($prescription['patient_last_name'] ?? '')) ?></td>
                        <td><?= doctorPrescriptionListEscape($prescription['patient_number'] ?? '') ?></td>
                        <td><?= doctorPrescriptionListEscape($prescription['prescription_date'] ?? '') ?></td>
                        <td><?= doctorPrescriptionListEscape($prescription['status'] ?? '') ?></td>
                        <td>
                            <a class="view" href="/doctor/prescriptions/show?id=<?= (int)$prescription['id'] ?>">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="empty">No prescriptions found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
