
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Doctor Management</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f6f9;
        }

        .navbar {
            background: #263b5e;
            color: white;
            padding: 18px 30px;
        }

        .container {
            width: 94%;
            margin: 35px auto;
            overflow-x: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header h2 {
            margin-bottom:12px;
            color: #263b5e;
        }
        .back-link {
            display:inline-block;
            color: #2563eb;
            text-decoration:none;
            font-size:14px;
            font-weight:600;
        }
        .back-link:hover {
            color:#1d4ed8;
            text-decoration:underline;
        }


        .add-btn {
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            min-width: 1000px;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 10px #ddd;
        }

        th, td {
            padding: 13px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #e8edf5;
        }

        .active {
            color: green;
            font-weight: bold;
        }

        .inactive {
            color: red;
            font-weight: bold;
        }

        .edit-btn {
            background: #f59e0b;
            color: white;
            text-decoration: none;
            padding: 7px 12px;
            border-radius: 4px;
        }
    .reset-btn {
        background: #7c3aed;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
    }

    .reset-btn:hover {
        background: #6d28d9;
    }

        .status-btn {
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .deactivate {
            background: #dc2626;
        }

        .activate {
            background: #16a34a;
        }

        .action-form {
            display: inline;
        }

        .empty {
            text-align: center;
            padding: 25px;
        }
    .action-buttons {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .action-buttons a,
    .action-buttons button {
        min-width: 130px;
        text-align: center;
    }

    </style>

</head>

<body>

    <div class="navbar">

        <h3>Younseii Hospital System</h3>

    </div>


    <div class="container">

        <div class="header">

        <div>
            <h2>Doctor Management</h2>
            <a href="/admin/dashboard" class="back-link">
                ← Back to Dashboard
            </a>
        </div>

            <a href="/doctors/create" class="add-btn">
                + Add Doctor
            </a>

        </div>


        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Doctor Name</th>

                    <th>Username</th>

                    <th>Email</th>

                    <th>Department</th>

                    <th>Specialization</th>

                    <th>Experience</th>

                    <th>Status</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                <?php if (!empty($doctors)): ?>

                    <?php foreach ($doctors as $doctor): ?>

                        <tr>

                            <td>
                                <?= (int)$doctor['id'] ?>
                            </td>

                            <td>
                                Dr.
                                <?= htmlspecialchars(
                                    $doctor['first_name'] . ' ' .
                                    $doctor['last_name']
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($doctor['username']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($doctor['email']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($doctor['department_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($doctor['specialization']) ?>
                            </td>

                            <td>
                                <?= (int)$doctor['experience'] ?> years
                            </td>

                            <td>

                                <?php if ($doctor['status'] == 1): ?>

                                    <span class="active">
                                        Active
                                    </span>

                                <?php else: ?>

                                    <span class="inactive">
                                        Inactive
                                    </span>

                                <?php endif; ?>

                            </td>
                                                        
                            <td>

                                <div class="action-buttons">

                                    <!-- Edit Button -->
                                    <a
                                        href="/doctors/edit?id=<?= (int)$doctor['id'] ?>"
                                        class="edit-btn"
                                    >
                                        Edit
                                    </a>

                                    <!-- Activate / Deactivate Button -->
                                    <form
                                        action="/doctors/toggle-status"
                                        method="POST"
                                        class="action-form"
                                        onsubmit="return confirm('Are you sure you want to change this doctor account status?');"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars($csrfToken) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int)$doctor['id'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="<?= $doctor['status'] == 1 ? 0 : 1 ?>"
                                        >

                                        <?php if ($doctor['status'] == 1): ?>

                                            <button
                                                type="submit"
                                                class="status-btn deactivate"
                                            >
                                                Deactivate
                                            </button>

                                        <?php else: ?>

                                            <button
                                                type="submit"
                                                class="status-btn activate"
                                            >
                                                Activate
                                            </button>

                                        <?php endif; ?>

                                    </form>

                                    <!-- Reset Password Button -->
                                    <a
                                        href="/doctors/reset-password?id=<?= (int)$doctor['id'] ?>"
                                        class="reset-btn"
                                        style="text-decoration: none; display: inline-block;"
                                    >
                                        Reset Password
                                    </a>

                                </div>

                            </td>
                                            
                           

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="9" class="empty">
                            No doctors found.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</body>

</html>
