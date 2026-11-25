
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <?php require __DIR__ . '/../partials/navigation.php'; ?>

    <h2>Edit Doctor Details</h2>

    <a href="/doctors">Back to Doctor List</a>

    <br><br>

    <?php if (!empty($errors)): ?>

        <div class="error">

            <?php foreach ($errors as $error): ?>

                <p><?= htmlspecialchars($error) ?></p>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <form action="/doctors/update?id=<?= $doctor['doctor_id'] ?>"
          method="POST">

        <label>Doctor Name</label>
        <br>

        <input
            type="text"
            name="doctor_name"
            value="<?= htmlspecialchars($doctor['doctor_name']) ?>"
            required
        >

        <br><br>

        <label>Specialization</label>
        <br>

        <input
            type="text"
            name="specialization"
            maxlength="100"
            value="<?= htmlspecialchars($doctor['specialization']) ?>"
            required
        >

        <br><br>

        <label>Phone</label>
        <br>

        <input
            type="text"
            name="phone"
            value="<?= htmlspecialchars($doctor['phone'] ?? '') ?>"
        >

        <br><br>

        <label>Email</label>
        <br>

        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($doctor['email'] ?? '') ?>"
        >

        <br><br>

        <label>Department</label>
        <br>

        <select name="department_id" required>

            <option value="">Select Department</option>

            <?php foreach ($departments as $department): ?>

                <option
                    value="<?= $department['department_id'] ?>"
                    <?= ($doctor['department_id'] == $department['department_id']) ? 'selected' : '' ?>
                >

                    <?= htmlspecialchars($department['department_name']) ?>

                </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <button type="submit">Update Doctor</button>

    </form>

</body>
</html>
