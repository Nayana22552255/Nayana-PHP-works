
<?php
function prescriptionShowEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= prescriptionShowEscape($title) ?></title>

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

        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .status {
            font-weight: bold;
        }

        @media(max-width: 600px) {
            .container {
                padding: 12px;
            }

            table {
                font-size: 13px;
            }
        }

        @media print {
            body {
                background: white;
            }

            .container {
                margin: 0;
                max-width: none;
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Patient Prescription</h2>

    <div class="details">

        <p>
            <strong>Prescription Number:</strong>
            <?= prescriptionShowEscape(
                $prescription['prescription_number']
            ) ?>
        </p>

        <p>
            <strong>Prescription Date:</strong>
            <?= prescriptionShowEscape(
                $prescription['prescription_date']
            ) ?>
        </p>

        <p>
            <strong>Patient Name:</strong>
            <?= prescriptionShowEscape(
                $prescription['patient_first_name'] . ' ' .
                $prescription['patient_last_name']
            ) ?>
        </p>

        <p>
            <strong>Patient Number:</strong>
            <?= prescriptionShowEscape(
                $prescription['patient_number']
            ) ?>
        </p>

        <p>
            <strong>Gender:</strong>
            <?= prescriptionShowEscape($prescription['gender']) ?>
        </p>

        <p>
            <strong>Phone:</strong>
            <?= prescriptionShowEscape($prescription['phone']) ?>
        </p>

        <p>
            <strong>Doctor:</strong>
            <?= prescriptionShowEscape(
                $prescription['doctor_first_name'] . ' ' .
                $prescription['doctor_last_name']
            ) ?>
        </p>

        <p>
            <strong>Qualification:</strong>
            <?= prescriptionShowEscape(
                $prescription['qualification']
            ) ?>
        </p>

        <p>
            <strong>Specialization:</strong>
            <?= prescriptionShowEscape(
                $prescription['specialization']
            ) ?>
        </p>

        <p>
            <strong>Diagnosis:</strong>
            <?= nl2br(prescriptionShowEscape(
                $prescription['diagnosis'] ?? ''
            )) ?>
        </p>

        <p>
            <strong>Status:</strong>
            <span class="status">
                <?= prescriptionShowEscape($prescription['status']) ?>
            </span>
        </p>

    </div>

    <h3>Prescribed Medicines</h3>

    <table>

        <thead>
            <tr>
                <th>Medicine Name</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Duration</th>
                <th>Quantity</th>
                <th>Instructions</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!empty($items)): ?>

            <?php foreach ($items as $item): ?>

                <tr>

                    <td>
                        <?= prescriptionShowEscape(
                            $item['medicine_name']
                        ) ?>
                    </td>

                    <td>
                        <?= prescriptionShowEscape(
                            $item['dosage']
                        ) ?>
                    </td>

                    <td>
                        <?= prescriptionShowEscape(
                            $item['frequency']
                        ) ?>
                    </td>

                    <td>
                        <?= prescriptionShowEscape(
                            $item['duration']
                        ) ?>
                    </td>

                    <td>
                        <?= (int) $item['quantity'] ?>
                    </td>

                    <td>
                        <?= prescriptionShowEscape(
                            $item['instructions'] ?? ''
                        ) ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="6">No medicines found.</td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

    <h3>Additional Notes</h3>

    <p>
        <?= nl2br(prescriptionShowEscape(
            $prescription['notes'] ?? ''
        )) ?>
    </p>

    <div class="no-print">

        <?php if ($prescription['status'] !== 'Cancelled'): ?>

            <h3>Update Prescription Status</h3>

            <form method="POST" action="/prescriptions/update-status">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= prescriptionShowEscape($csrfToken) ?>"
                >

                <input
                    type="hidden"
                    name="prescription_id"
                    value="<?= (int) $prescription['id'] ?>"
                >

                <select name="status" required>

                    <option value="">-- Select Status --</option>

                    <option value="Active"
                        <?= $prescription['status'] === 'Active' ? 'selected' : '' ?>>
                        Active
                    </option>

                    <option value="Completed"
                        <?= $prescription['status'] === 'Completed' ? 'selected' : '' ?>>
                        Completed
                    </option>

                    <option value="Cancelled">
                        Cancelled
                    </option>

                </select>

                <button type="submit" class="btn">
                    Update Status
                </button>

            </form>

        <?php endif; ?>

        <button onclick="window.print()" class="btn">
            Print Prescription
        </button>

        <a href="/prescriptions" class="btn">
            Back to Prescriptions
        </a>

    </div>

</div>

</body>
</html>
