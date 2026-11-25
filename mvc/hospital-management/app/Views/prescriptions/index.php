
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Management</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navigation.php'; ?>

<div class="container">

    <h2>Prescription Management</h2>

    <a href="/prescriptions/create">+ Add New Prescription</a>

    <br><br>

    <form action="/prescriptions/search" method="GET">

        <input
            type="text"
            name="keyword"
            placeholder="Search patient or doctor"
            value="<?= htmlspecialchars($keyword ?? '') ?>">

        <button type="submit">Search</button>

        <a href="/prescriptions">Reset</a>

    </form>

    <br>

    <table>

        <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Appointment Date</th>
                <th>Created Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!empty($prescriptions)): ?>

            <?php foreach ($prescriptions as $prescription): ?>

                <tr>

                    <td>
                        <?= $prescription['prescription_id'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($prescription['patient_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($prescription['doctor_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($prescription['appointment_date']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($prescription['created_at']) ?>
                    </td>

                    <td>

                        <a href="/prescriptions/show?id=<?= $prescription['prescription_id'] ?>">
                            View
                        </a>

                        <a href="/prescriptions/edit?id=<?= $prescription['prescription_id'] ?>">
                            Edit
                        </a>

                        <form
                            action="/prescriptions/delete?id=<?= $prescription['prescription_id'] ?>"
                            method="POST"
                            style="display:inline;"
                            onsubmit="return confirm('Are you sure you want to delete this prescription?')">

                            <button type="submit">Delete</button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="6">No prescriptions found.</td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>

</body>
</html>
