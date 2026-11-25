<?php
$prescriptions = $prescriptions ?? [];

function doctorPrescriptionContentEscape($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<h2>My Prescriptions</h2>
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr>
            <th style="border:1px solid #ddd; padding:10px;">Prescription Number</th>
            <th style="border:1px solid #ddd; padding:10px;">Patient</th>
            <th style="border:1px solid #ddd; padding:10px;">Date</th>
            <th style="border:1px solid #ddd; padding:10px;">Status</th>
            <th style="border:1px solid #ddd; padding:10px;">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($prescriptions)): ?>
        <?php foreach ($prescriptions as $prescription): ?>
            <tr>
                <td style="border:1px solid #ddd; padding:10px;">
                    <?= doctorPrescriptionContentEscape($prescription['prescription_number'] ?? '') ?>
                </td>
                <td style="border:1px solid #ddd; padding:10px;">
                    <?= doctorPrescriptionContentEscape(($prescription['patient_first_name'] ?? '') . ' ' . ($prescription['patient_last_name'] ?? '')) ?>
                </td>
                <td style="border:1px solid #ddd; padding:10px;">
                    <?= doctorPrescriptionContentEscape($prescription['prescription_date'] ?? '') ?>
                </td>
                <td style="border:1px solid #ddd; padding:10px;">
                    <?= doctorPrescriptionContentEscape($prescription['status'] ?? '') ?>
                </td>
                <td style="border:1px solid #ddd; padding:10px;">
                    <a href="/doctor/prescriptions/show?id=<?= (int)$prescription['id'] ?>">View Details</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" style="border:1px solid #ddd; padding:10px; text-align:center;">No prescriptions found.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
