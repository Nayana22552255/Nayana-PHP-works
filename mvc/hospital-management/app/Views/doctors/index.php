
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Management</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <?php require __DIR__ . '/../partials/navigation.php'; ?>

    <h2>Doctor Management</h2>

    <a href="/doctors/create">Add New Doctor</a>

    <br><br>

    <!-- Search Form -->

    <form action="/doctors/search" method="GET">

        <input
            type="text"
            name="keyword"
            placeholder="Search doctor..."
            value="<?= htmlspecialchars($keyword ?? '') ?>"
        >

        <button type="submit">Search</button>

        <a href="/doctors">Clear</a>

    </form>

    <br>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Doctor Name</th>
            <th>Specialization</th>
            <th>Department</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php if (!empty($doctors)): ?>

            <?php foreach ($doctors as $doctor): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($doctor['doctor_id']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($doctor['doctor_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($doctor['specialization']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($doctor['department_name'] ?? 'Not assigned') ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($doctor['phone'] ?? '') ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($doctor['email'] ?? '') ?>
                    </td>

                    <td>

                        <a href="/doctors/show?id=<?= $doctor['doctor_id'] ?>">
                            View
                        </a>

                        |

                        <a href="/doctors/edit?id=<?= $doctor['doctor_id'] ?>">
                            Edit
                        </a>

                        |

                        <form
                            action="/doctors/delete?id=<?= $doctor['doctor_id'] ?>"
                            method="POST"
                            style="display:inline;"
                            onsubmit="return confirm('Are you sure you want to delete this doctor?')"
                        >

                            <button type="submit">Delete</button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="7">No doctors found.</td>
            </tr>

        <?php endif; ?>

    </table>

</body>
</html>
