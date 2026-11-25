<?php
$prescription = $prescription ?? [];

function doctorPrescriptionDetailEscape($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$items = $prescription['items'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Details</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f7fb; }
        .navbar { background: #263b5e; color: white; padding: 18px 30px; }
        .container { max-width: 950px; margin: 30px auto; padding: 25px; background: white; border-radius: 8px; }
        h2, h3 { color: #263b5e; }
        .details { line-height: 1.9; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 11px; text-align: left; }
        th { background: #263b5e; color: white; }
        .back { display: inline-block; margin-top: 20px; background: #263b5e; color: white; padding: 10px 16px; border-radius: 5px; text-decoration: none; }
        @media (max-width: 700px) { .container { margin: 10px; padding: 12px; } table { font-size: 13px; } }
    </style>
</head>
<body>
    <div class="navbar"><h3>Younseii Hospital System</h3></div>
    <div class="container">
        <h2>Prescription Details</h2>
        <div class="details">
            <p><strong>Prescription Number:</strong> <?= doctorPrescriptionDetailEscape($prescription['prescription_number'] ?? '') ?></p>
            <p><strong>Prescription Date:</strong> <?= doctorPrescriptionDetailEscape($prescription['prescription_date'] ?? '') ?></p>
            <p><strong>Patient:</strong> <?= doctorPrescriptionDetailEscape(($prescription['patient_first_name'] ?? '') . ' ' . ($prescription['patient_last_name'] ?? '')) ?></p>
            <p><strong>Patient Number:</strong> <?= doctorPrescriptionDetailEscape($prescription['patient_number'] ?? '') ?></p>
            <p><strong>Gender:</strong> <?= doctorPrescriptionDetailEscape($prescription['gender'] ?? '') ?></p>
            <p><strong>Phone:</strong> <?= doctorPrescriptionDetailEscape($prescription['phone'] ?? '') ?></p>
            <p><strong>Doctor:</strong> <?= doctorPrescriptionDetailEscape(($prescription['doctor_first_name'] ?? '') . ' ' . ($prescription['doctor_last_name'] ?? '')) ?></p>
            <p><strong>Qualification:</strong> <?= doctorPrescriptionDetailEscape($prescription['qualification'] ?? '') ?></p>
            <p><strong>Specialization:</strong> <?= doctorPrescriptionDetailEscape($prescription['specialization'] ?? '') ?></p>
            <p><strong>Appointment:</strong> <?= doctorPrescriptionDetailEscape($prescription['appointment_number'] ?? 'Not linked') ?></p>
            <p><strong>Diagnosis:</strong><br><?= nl2br(doctorPrescriptionDetailEscape($prescription['diagnosis'] ?? '')) ?></p>
            <p><strong>Notes:</strong><br><?= nl2br(doctorPrescriptionDetailEscape($prescription['notes'] ?? '')) ?></p>
            <p><strong>Status:</strong> <?= doctorPrescriptionDetailEscape($prescription['status'] ?? '') ?></p>
        </div>

        <h3>Prescribed Medicines</h3>
        <table>
            <thead>
                <tr>
                    <th>Medicine</th>
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
                        <td><?= doctorPrescriptionDetailEscape($item['medicine_name'] ?? '') ?></td>
                        <td><?= doctorPrescriptionDetailEscape($item['dosage'] ?? '') ?></td>
                        <td><?= doctorPrescriptionDetailEscape($item['frequency'] ?? '') ?></td>
                        <td><?= doctorPrescriptionDetailEscape($item['duration'] ?? '') ?></td>
                        <td><?= (int)($item['quantity'] ?? 0) ?></td>
                        <td><?= doctorPrescriptionDetailEscape($item['instructions'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No medicines found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <a class="back" href="/doctor/prescriptions">Back to Prescriptions</a>
    </div>
</body>
</html>
