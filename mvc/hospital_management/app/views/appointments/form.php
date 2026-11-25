
<?php
function appointmentFormEscape($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$isEdit = !empty($appointment['id']);

$formAction = $isEdit
    ? '/appointments/update'
    : '/appointments/store';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= appointmentFormEscape($title) ?></title>

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            width: 60%;
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

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 11px;
            margin-top: 7px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            resize: vertical;
        }

        button {
            margin-top: 20px;
            padding: 12px 20px;
            background: #263b5e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #2563eb;
            text-decoration: none;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="card">

        <h1><?= appointmentFormEscape($title) ?></h1>

        <?php if (!empty($error)): ?>

            <div class="error">
                <?= appointmentFormEscape($error) ?>
            </div>

        <?php endif; ?>


        <form action="<?= appointmentFormEscape($formAction) ?>" method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= appointmentFormEscape($csrfToken) ?>"
            >

            <?php if ($isEdit): ?>

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int)$appointment['id'] ?>"
                >

            <?php endif; ?>


            <!-- Select Patient -->

            <label>Select Patient *</label>

            <select name="patient_id" required>

                <option value="">-- Select Patient --</option>

                <?php foreach ($patients as $patient): ?>

                    <option
                        value="<?= (int)$patient['id'] ?>"
                        <?= (string)($appointment['patient_id'] ?? '') === (string)$patient['id'] ? 'selected' : '' ?>
                    >

                        <?= appointmentFormEscape(
                            $patient['patient_number'] . ' - ' .
                            $patient['first_name'] . ' ' .
                            $patient['last_name']
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <!-- Select Doctor -->

            <label>Select Doctor *</label>

            <select name="doctor_id" required>

                <option value="">-- Select Doctor --</option>

                <?php foreach ($doctors as $doctor): ?>

                    <option
                        value="<?= (int)$doctor['id'] ?>"
                        <?= (string)($appointment['doctor_id'] ?? '') === (string)$doctor['id'] ? 'selected' : '' ?>
                    >

                        <?= appointmentFormEscape(
                            'Dr. ' . $doctor['first_name'] . ' ' .
                            $doctor['last_name'] . ' - ' .
                            ($doctor['department_name'] ?? 'No department')
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <!-- Appointment Date -->

            <label>Appointment Date *</label>

            <input
                type="date"
                name="appointment_date"
                min="<?= date('Y-m-d') ?>"
                value="<?= appointmentFormEscape($appointment['appointment_date'] ?? '') ?>"
                required
            >


            <!-- Appointment Time -->

            <label>Appointment Time *</label>

            <input
                type="time"
                name="appointment_time"
                value="<?= appointmentFormEscape(isset($appointment['appointment_time']) ? substr($appointment['appointment_time'], 0, 5) : '') ?>"
                required
            >


            <!-- Duration -->

            <label>Duration (Minutes) *</label>

            <select name="duration_minutes" required>

                <?php
                $durations = [15, 30, 45, 60, 90, 120];
                $selectedDuration = (int)($appointment['duration_minutes'] ?? 30);
                ?>

                <?php foreach ($durations as $duration): ?>

                    <option
                        value="<?= $duration ?>"
                        <?= $selectedDuration === $duration ? 'selected' : '' ?>
                    >
                        <?= $duration ?> Minutes
                    </option>

                <?php endforeach; ?>

            </select>


            <!-- Reason -->

            <label>Reason for Visit</label>

            <textarea
                name="reason"
                rows="3"
                placeholder="Enter reason for appointment"
            ><?= appointmentFormEscape($appointment['reason'] ?? '') ?></textarea>


            <!-- Notes -->

            <label>Additional Notes</label>

            <textarea
                name="notes"
                rows="3"
                placeholder="Enter additional notes"
            ><?= appointmentFormEscape($appointment['notes'] ?? '') ?></textarea>


            <button type="submit">

                <?= $isEdit ? 'Update Appointment' : 'Book Appointment' ?>

            </button>

        </form>


        <a href="/appointments" class="back-link">
            Back to Appointments
        </a>

    </div>

</div>

</body>

</html>
