
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Management</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navigation.php'; ?>

<div class="container">

    <h2>Appointment Management</h2>

    <a href="/appointments/create">+ Book New Appointment</a>

    <br><br>

    <form action="/appointments/search" method="GET">
        <input
            type="text"
            name="keyword"
            placeholder="Search patient or doctor"
            value="<?= htmlspecialchars($keyword ?? '') ?>">

        <button type="submit">Search</button>
        <a href="/appointments">Reset</a>
    </form>

    <br>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!empty($appointments)): ?>

            <?php foreach ($appointments as $appointment): ?>

                <tr>
                    <td><?= $appointment['appointment_id'] ?></td>

                    <td>
                        <?= htmlspecialchars($appointment['patient_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($appointment['doctor_name']) ?>
                    </td>

                    <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>

                    <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>

                    <td><?= htmlspecialchars($appointment['status']) ?></td>

                    <td>
                        <a href="/appointments/show?id=<?= $appointment['appointment_id'] ?>">
                            View
                        </a>

                        <a href="/appointments/edit?id=<?= $appointment['appointment_id'] ?>">
                            Edit
                        </a>

                        <form action="/appointments/delete?id=<?= $appointment['appointment_id'] ?>"
                              method="POST"
                              style="display:inline;"
                              onsubmit="return confirm('Delete this appointment?')">

                            <button type="submit">Delete</button>

                        </form>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="7">No appointments found.</td>
            </tr>

        <?php endif; ?>

        </tbody>
    </table>

</div>

</body>
</html>
