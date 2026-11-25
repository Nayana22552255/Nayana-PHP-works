
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Appointment</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navigation.php'; ?>

<div class="container">

    <h2>Book New Appointment</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="/appointments/store" method="POST">

        <label>Patient</label>
        <select name="patient_id" required>
            <option value="">Select Patient</option>

            <?php foreach ($patients as $patient): ?>
                <option
                    value="<?= $patient['patient_id'] ?>"
                    <?= (($old['patient_id'] ?? '') == $patient['patient_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($patient['patient_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Doctor</label>
        <select name="doctor_id" required>
            <option value="">Select Doctor</option>

            <?php foreach ($doctors as $doctor): ?>
                <option
                    value="<?= $doctor['doctor_id'] ?>"
                    <?= (($old['doctor_id'] ?? '') == $doctor['doctor_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($doctor['doctor_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Appointment Date</label>
        <input type="date"
               name="appointment_date"
               value="<?= htmlspecialchars($old['appointment_date'] ?? '') ?>"
               required>

        <label>Appointment Time</label>
        <input type="time"
               name="appointment_time"
               value="<?= htmlspecialchars($old['appointment_time'] ?? '') ?>"
               required>

        <label>Reason</label>
        <textarea name="reason"><?= htmlspecialchars($old['reason'] ?? '') ?></textarea>

        <label>Status</label>
        <select name="status">
            <option value="Scheduled">Scheduled</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
        </select>

        <button type="submit">Book Appointment</button>

    </form>

    <a href="/appointments">Back to Appointments</a>

</div>

</body>
</html>
