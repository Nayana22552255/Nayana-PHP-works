
<?php
function appointmentShowEscape($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$user = Auth::user();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= appointmentShowEscape($title) ?></title>

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            width: 65%;
            margin: 40px auto;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px #ddd;
        }

        h1 {
            color: #263b5e;
        }

        .detail {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .detail strong {
            display: inline-block;
            width: 200px;
        }

        .button {
            display: inline-block;
            padding: 10px 16px;
            margin-top: 20px;
            margin-right: 5px;
            background: #263b5e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .status-form {
            margin-top: 25px;
        }

        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 16px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="card">

        <h1>Appointment Details</h1>

        <div class="detail">
            <strong>Appointment Number:</strong>
            <?= appointmentShowEscape($appointment['appointment_number']) ?>
        </div>

        <div class="detail">
            <strong>Patient Number:</strong>
            <?= appointmentShowEscape($appointment['patient_number']) ?>
        </div>

        <div class="detail">
            <strong>Patient Name:</strong>
            <?= appointmentShowEscape(
                $appointment['patient_first_name'] . ' ' .
                $appointment['patient_last_name']
            ) ?>
        </div>

        <div class="detail">
            <strong>Doctor Name:</strong>
            <?= appointmentShowEscape(
                'Dr. ' . $appointment['doctor_first_name'] . ' ' .
                $appointment['doctor_last_name']
            ) ?>
        </div>

        <div class="detail">
            <strong>Department:</strong>
            <?= appointmentShowEscape($appointment['department_name'] ?? 'Not assigned') ?>
        </div>

        <div class="detail">
            <strong>Appointment Date:</strong>
            <?= appointmentShowEscape($appointment['appointment_date']) ?>
        </div>

        <div class="detail">
            <strong>Appointment Time:</strong>
            <?= appointmentShowEscape(date(
                'h:i A',
                strtotime($appointment['appointment_time'])
            )) ?>
        </div>

        <div class="detail">
            <strong>Duration:</strong>
            <?= (int)$appointment['duration_minutes'] ?> Minutes
        </div>

        <div class="detail">
            <strong>Reason:</strong>
            <?= appointmentShowEscape($appointment['reason'] ?: 'Not provided') ?>
        </div>

        <div class="detail">
            <strong>Additional Notes:</strong>
            <?= appointmentShowEscape($appointment['notes'] ?: 'None') ?>
        </div>

        <div class="detail">
            <strong>Status:</strong>
            <?= appointmentShowEscape($appointment['status']) ?>
        </div>


        <a href="/appointments" class="button">
            Back to Appointments
        </a>


        <?php if (in_array($appointment['status'], ['Scheduled', 'Confirmed'], true)): ?>

            <a
                href="/appointments/edit?id=<?= (int)$appointment['id'] ?>"
                class="button"
            >
                Edit Appointment
            </a>

        <?php endif; ?>


        <?php if (in_array($appointment['status'], ['Scheduled', 'Confirmed'], true)): ?>

            <form
                action="/appointments/update-status"
                method="POST"
                class="status-form"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= appointmentShowEscape($csrfToken) ?>"
                >

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int)$appointment['id'] ?>"
                >

                <label><strong>Change Appointment Status:</strong></label>

                <select name="status" required>

                    <?php if ($appointment['status'] === 'Scheduled'): ?>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Confirmed">Confirmed</option>
                    <?php endif; ?>

                    <?php if ($appointment['status'] === 'Confirmed'): ?>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Scheduled">Scheduled</option>
                    <?php endif; ?>

                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="No-show">No-show</option>

                </select>

                <button type="submit">
                    Update Status
                </button>

            </form>

        <?php endif; ?>

    </div>

</div>

</body>

</html>
