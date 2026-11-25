<?php
$prescription = $prescription ?? [];

function doctorPrescriptionDetailContentEscape($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$items = $prescription['items'] ?? [];
?>
<h2>Prescription Details</h2>
<p><strong>Prescription Number:</strong> <?= doctorPrescriptionDetailContentEscape($prescription['prescription_number'] ?? '') ?></p>
<p><strong>Prescription Date:</strong> <?= doctorPrescriptionDetailContentEscape($prescription['prescription_date'] ?? '') ?></p>
<p><strong>Patient:</strong> <?= doctorPrescriptionDetailContentEscape(($prescription['patient_first_name'] ?? '') . ' ' . ($prescription['patient_last_name'] ?? '')) ?></p>
<p><strong>Diagnosis:</strong> <?= nl2br(doctorPrescriptionDetailContentEscape($prescription['diagnosis'] ?? '')) ?></p>
<p><strong>Notes:</strong> <?= nl2br(doctorPrescriptionDetailContentEscape($prescription['notes'] ?? '')) ?></p>
<p><strong>Status:</strong> <?= doctorPrescriptionDetailContentEscape($prescription['status'] ?? '') ?></p>

<h3>Prescribed Medicines</h3>
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr>
            <th style="border:1px solid #ddd; padding:10px;">Medicine</th>
            <th style="border:1px solid #ddd; padding:10px;">Dosage</th>
            <th style="border:1px solid #ddd; padding:10px;">Frequency</th>
            <th style="border:1px solid #ddd; padding:10px;">Duration</th>
            <th style="border:1px solid #ddd; padding:10px;">Quantity</th>
            <th style="border:1px solid #ddd; padding:10px;">Instructions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
            <tr>
                <td style="border:1px solid #ddd; padding:10px;"><?= doctorPrescriptionDetailContentEscape($item['medicine_name'] ?? '') ?></td>
                <td style="border:1px solid #ddd; padding:10px;"><?= doctorPrescriptionDetailContentEscape($item['dosage'] ?? '') ?></td>
                <td style="border:1px solid #ddd; padding:10px;"><?= doctorPrescriptionDetailContentEscape($item['frequency'] ?? '') ?></td>
                <td style="border:1px solid #ddd; padding:10px;"><?= doctorPrescriptionDetailContentEscape($item['duration'] ?? '') ?></td>
                <td style="border:1px solid #ddd; padding:10px;"><?= (int)($item['quantity'] ?? 0) ?></td>
                <td style="border:1px solid #ddd; padding:10px;"><?= doctorPrescriptionDetailContentEscape($item['instructions'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6" style="border:1px solid #ddd; padding:10px; text-align:center;">No medicines found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
