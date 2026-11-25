
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Doctor</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <?php require __DIR__ . '/../partials/navigation.php'; ?>

    <h2>Doctor Registration</h2>

    <a href="/doctors">Back to Doctor List</a>

    <br><br>

    <?php if (!empty($errors)): ?>

        <div class="error">

            <?php foreach ($errors as $error): ?>

                <p><?= htmlspecialchars($error) ?></p>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <form action="/doctors/store" method="POST">

        <label>Doctor Name</label>
        <br>

        <input
            type="text"
            name="doctor_name"
            value="<?= htmlspecialchars($data['doctor_name'] ?? '') ?>"
            required
        >

        <br><br>

        <label>Specialization</label>
        <br>

        <input
            type="text"
            name="specialization"
            maxlength="100"
            value="<?= htmlspecialchars($data['specialization'] ?? '') ?>"
            required
        >

        <br><br>

        <label>Phone Number</label>
        <br>

        <input
            type="text"
            name="phone"
            value="<?= htmlspecialchars($data['phone'] ?? '') ?>"
        >

        <br><br>

        <label>Email</label>
        <br>

        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($data['email'] ?? '') ?>"
        >

        <br><br>

        <label>Department</label>
        <br>

        <select name="department_id" required>

            <option value="">Select Department</option>

            <?php foreach ($departments as $department): ?>

                <option
                    value="<?= $department['department_id'] ?>"
                    <?= (($data['department_id'] ?? '') == $department['department_id']) ? 'selected' : '' ?>
                >

                    <?= htmlspecialchars($department['department_name']) ?>

                </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <button type="submit">Register Doctor</button>

    </form>

</body>
</html>
