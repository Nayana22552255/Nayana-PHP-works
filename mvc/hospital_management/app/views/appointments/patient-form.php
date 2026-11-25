
<?php
$escape = function ($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
};

$appointmentDate = $appointmentDate ?? '';
$appointmentTime = $appointmentTime ?? '';
$reason = $reason ?? '';
$error = $error ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $escape($title ?? 'Book Appointment') ?></title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        .navbar {
            background: #0f172a;
            color: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            font-size: 21px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
        }

        .container {
            max-width: 650px;
            margin: 45px auto;
            padding: 0 20px;
        }

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }

        h1 {
            font-size: 26px;
            margin-bottom: 8px;
            color: #0f172a;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .doctor-info {
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 17px;
            border-radius: 6px;
            margin-bottom: 25px;
            line-height: 1.7;
        }

        .doctor-info strong {
            color: #1d4ed8;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 15px;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37,99,235,0.12);
        }

        textarea {
            resize: vertical;
            min-height: 110px;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 13px;
            border: none;
            border-radius: 6px;
            text-align: center;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-btn {
            background: #2563eb;
            color: white;
        }

        .submit-btn:hover {
            background: #1d4ed8;
        }

        .cancel-btn {
            background: #e2e8f0;
            color: #334155;
        }

        .cancel-btn:hover {
            background: #cbd5e1;
        }

        @media (max-width: 500px) {
            .form-card {
                padding: 22px;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">
    <h2>Younseii Hospital</h2>
    <a href="/patient/dashboard">Back to Dashboard</a>
</nav>

<div class="container">

    <div class="form-card">

        <h1>Book an Appointment</h1>

        <p class="subtitle">
            Choose your preferred date and time to request an appointment.
        </p>

        <?php if (!empty($error)): ?>
            <div class="error">
                <?= $escape($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($doctor)): ?>

            <div class="doctor-info">
                <strong>Selected Doctor</strong><br>

                Dr. <?= $escape(
                    $doctor['first_name'] . ' ' . $doctor['last_name']
                ) ?>

                <br>

                <strong>Specialization:</strong>
                <?= $escape($doctor['specialization']) ?>

                <br>

                <strong>Department:</strong>
                <?= $escape($doctor['department_name']) ?>

                <?php if (!empty($doctor['qualification'])): ?>
                    <br>
                    <strong>Qualification:</strong>
                    <?= $escape($doctor['qualification']) ?>
                <?php endif; ?>
            </div>

            <form method="POST" action="/patient/appointments/store">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= $escape($csrfToken ?? '') ?>"
                >

                <input
                    type="hidden"
                    name="doctor_id"
                    value="<?= (int)$doctor['id'] ?>"
                >

                <div class="form-group">
                    <label for="appointment_date">
                        Appointment Date
                    </label>

                    <input
                        type="date"
                        id="appointment_date"
                        name="appointment_date"
                        min="<?= date('Y-m-d') ?>"
                        value="<?= $escape($appointmentDate) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="appointment_time">
                        Appointment Time
                    </label>

                    <input
                        type="time"
                        id="appointment_time"
                        name="appointment_time"
                        value="<?= $escape($appointmentTime) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="reason">
                        Reason for Appointment
                    </label>

                    <textarea
                        id="reason"
                        name="reason"
                        maxlength="5000"
                        placeholder="Briefly describe why you need this appointment..."
                    ><?= $escape($reason) ?></textarea>
                </div>

                <div class="actions">

                    <a
                        href="/patient/dashboard"
                        class="btn cancel-btn"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn submit-btn"
                    >
                        Confirm Appointment
                    </button>

                </div>

            </form>

        <?php else: ?>

            <p>Doctor information is unavailable.</p>

            <a href="/patient/dashboard">Return to Dashboard</a>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
