
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Department Management</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f6f9;
            overflow:hidden;
            
          
        }

        .navbar {
            background: #263b5e;
            color: white;
            padding: 18px 30px;
           
        }

        .container {
            width: 90%;
            margin: 35px auto;
           
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .header h2 {
            margin-bottom: 12px;
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
            background: #0c3884;
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 10px #ddd;
        }

        th, td {
            padding: 15px;
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
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        .delete-btn {
            background: #dc2626;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .empty {
            text-align: center;
            padding: 25px;
        }

        .action-form {
            display: inline;
        }
        td a.edit-btn {
            margin-right: 8px;
        }

        @media(max-width: 700px) {
            .container {
                width: 96%;
                overflow-x: auto;
            }

            table {
                min-width: 650px;
            }
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
            <h2>Department Management</h2>
            <a href="/admin/dashboard" class="back-link">
                ← Back to Dashboard
            </a>
        </div>

            <a href="/departments/create" class="add-btn">
                + Add Department
            </a>

        </div>


        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Department Name</th>

                    <th>Description</th>

                    <th>Status</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                <?php if (!empty($departments)): ?>

                    <?php foreach ($departments as $department): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars((string)$department['id']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($department['department_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($department['description'] ?? '') ?>
                            </td>

                            <td>

                                <?php if ($department['status'] == 1): ?>

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

                                <a
                                    href="/departments/edit?id=<?= (int)$department['id'] ?>"
                                    class="edit-btn"
                                >
                                    Edit
                                </a>


                                <form
                                    action="/departments/delete"
                                    method="POST"
                                    class="action-form"
                                    onsubmit="return confirm('Are you sure you want to delete this department?');"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$department['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars($csrfToken) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="delete-btn"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="5" class="empty">
                            No departments found.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</body>

</html>
