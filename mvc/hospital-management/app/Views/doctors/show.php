
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Details</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <?php require __DIR__ . '/../partials/navigation.php'; ?>

    <h2>Doctor Details</h2>

    <a href="/doctors">Back to Doctor List</a>

    <hr>

    <p>
        <strong>Doctor ID:</strong>
        <?= htmlspecialchars($doctor['doctor_id']) ?>
    </p>

    <p>
        <strong>Name:</strong>
        <?= htmlspecialchars($doctor['doctor_name']) ?>
    </p>

    <p>
        <strong>Specialization:</strong>
        <?= htmlspecialchars($doctor['specialization']) ?>
    </p>

    <p>
        <strong>Department:</strong>
        <?= htmlspecialchars($doctor['department_name'] ?? 'Not assigned') ?>
    </p>

    <p>
        <strong>Phone:</strong>
        <?= htmlspecialchars($doctor['phone'] ?? '') ?>
    </p>

    <p>
        <strong>Email:</strong>
        <?= htmlspecialchars($doctor['email'] ?? '') ?>
    </p>

    <br>

    <a href="/doctors/edit?id=<?= $doctor['doctor_id'] ?>">
        Edit Doctor
    </a>

</body>

</html>
