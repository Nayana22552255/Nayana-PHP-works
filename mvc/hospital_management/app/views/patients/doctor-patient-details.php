<?php
$patient = $patient ?? [];

function doctorPatientDetailEscape($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7fb; margin: 0; padding: 30px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 25px; border-radius: 10px; }
        h2 { color: #263b55; }
        .details { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-top: 20px; }
        .detail { border-bottom: 1px solid #eee; padding: 10px; }
        .detail strong { display: block; color: #555; margin-bottom: 6px; }
        .back { display: inline-block; margin-top: 25px; background: #2563eb; color: white; padding: 10px 16px; border-radius: 5px; text-decoration: none; }
        @media (max-width: 600px) { .details { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">
    <h2>Patient Details</h2>
    <div class="details">
        <div class="detail"><strong>Patient ID</strong><?= doctorPatientDetailEscape($patient['patient_number'] ?? '') ?></div>
        <div class="detail"><strong>Name</strong><?= doctorPatientDetailEscape(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? '')) ?></div>
        <div class="detail"><strong>Gender</strong><?= doctorPatientDetailEscape($patient['gender'] ?? '') ?></div>
        <div class="detail"><strong>Date of Birth</strong><?= doctorPatientDetailEscape($patient['date_of_birth'] ?? '') ?></div>
        <div class="detail"><strong>Blood Group</strong><?= doctorPatientDetailEscape($patient['blood_group'] ?? '') ?></div>
        <div class="detail"><strong>Phone</strong><?= doctorPatientDetailEscape($patient['phone'] ?? '') ?></div>
        <div class="detail"><strong>Email</strong><?= doctorPatientDetailEscape($patient['email'] ?? 'Not provided') ?></div>
        <div class="detail"><strong>Address</strong><?= nl2br(doctorPatientDetailEscape($patient['address'] ?? 'Not provided')) ?></div>
        <div class="detail"><strong>Emergency Contact</strong><?= doctorPatientDetailEscape($patient['emergency_contact_name'] ?? 'Not provided') ?></div>
        <div class="detail"><strong>Emergency Phone</strong><?= doctorPatientDetailEscape($patient['emergency_contact_phone'] ?? 'Not provided') ?></div>
    </div>
    <a class="back" href="/doctor/patients">Back to My Patients</a>
</div>
</body>
</html>