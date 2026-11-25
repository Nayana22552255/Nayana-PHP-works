<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Patient List</title>
        <link rel="stylesheet" href="<?= htmlspecialchars(APP_BASE_URL) ?>/assets/css/style.css">
    </head>

    <body>
        <?php require __DIR__ . '/../partials/navigation.php'; ?>
        <h2>Patient Management</h2>
        <a href="<?= htmlspecialchars(APP_BASE_URL) ?>/patients/create">
            Add New Patient
        </a>
        <br><br>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Email</th>
            </tr>

            <?php
            /** @var array<int, array<string, mixed>> $patients */
            foreach ($patients as $patient):
            ?>
                <tr>
                    <td>
                        <?= htmlspecialchars((string) $patient['patient_id']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($patient['patient_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($patient['gender']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($patient['phone']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($patient['email']) ?>
                    </td>

                </tr>
                <?php endforeach; ?>

        </table>
    </body>
</html>