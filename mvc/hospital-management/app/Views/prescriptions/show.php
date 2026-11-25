
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Details</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navigation.php'; ?>

<div class="container">

    <h2>Prescription Details</h2>

    <h3>General Information</h3>

    <p>
        <strong>Prescription ID:</strong>
        <?= $prescription['prescription_id'] ?>
    </p>

    <p>
        <strong>Patient:</strong>
        <?= htmlspecialchars($prescription['patient_name']) ?>
    </p>

    <p>
        <strong>Doctor:</strong>
        <?= htmlspecialchars($prescription['doctor_name']) ?>
    </p>

    <p>
        <strong>Appointment Date:</strong>
        <?= htmlspecialchars($prescription['appointment_date']) ?>
    </p>

    <p>
        <strong>Appointment Time:</strong>
        <?= htmlspecialchars($prescription['appointment_time']) ?>
    </p>

    <p>
        <strong>Notes:</strong>
        <?= nl2br(htmlspecialchars($prescription['notes'] ?? '')) ?>
    </p>

    <h3>Prescribed Medicines</h3>

    <table>

        <thead>
            <tr>
                <th>Medicine</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Duration</th>
                <th>Instructions</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!empty($items)): ?>

            <?php foreach ($items as $item): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($item['medicine_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($item['dosage'] ?? '') ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($item['frequency'] ?? '') ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($item['duration'] ?? '') ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($item['instructions'] ?? '') ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="5">No medicines found.</td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

    <br>

    <a href="/prescriptions/edit?id=<?= $prescription['prescription_id'] ?>">
        Edit Prescription
    </a>

    <a href="/prescriptions">
        Back to Prescriptions
    </a>

</div>

</body>
</html>
