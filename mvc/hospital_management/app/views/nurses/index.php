
<?php
function nurseIndexEscape($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Management</title>

    <style>
        *{
            box-sizing:border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
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

        h2 { 
            color: #263b5e; 
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            margin-bottom:25px
        }
        .top h2 {
            margin-bottom:12px;
            color:#263b5e;
        }

        input, button, .btn {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button, .btn {
            background: #263b5e;
            color: white;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th { background: #263b5e; color: white; }

        .active { color: green; font-weight: bold; }
        .inactive { color: red; font-weight: bold; }

        .actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .secondary { background: #64748b; }
        .danger { background: #b42318; }

        @media(max-width: 700px) {
            .container {
                width: 96%;
                overflow-x: auto;
            }
            .table-wrapper {
                 overflow-x: auto; 
                }
        }
    </style>
</head>

<body>

<div class="navbar">

        <h3>Younseii Hospital System</h3>

</div>

<div class="container">

    <div class="top">
    
        <h2>Nurse Management</h2>
        
        <a href="/nurses/create" class="btn">+ Add New Nurse</a>
    </div>

    <form method="GET" action="/nurses">
        <input
            type="text"
            name="keyword"
            placeholder="Search nurses..."
            value="<?= nurseIndexEscape($keyword) ?>"
        >

        <button type="submit">Search</button>
        <a href="/nurses" class="btn secondary">Reset</a>
    </form>

    <div class="table-wrapper">

        <table>
            <thead>
                <tr>
                    <th>Nurse ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Phone</th>
                    <th>Shift</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($nurses)): ?>

                <?php foreach ($nurses as $nurse): ?>

                    <tr>
                        <td><?= nurseIndexEscape($nurse['nurse_number']) ?></td>

                        <td>
                            <?= nurseIndexEscape(
                                $nurse['first_name'] . ' ' . $nurse['last_name']
                            ) ?>
                        </td>

                        <td><?= nurseIndexEscape($nurse['department_name']) ?></td>

                        <td><?= nurseIndexEscape($nurse['phone']) ?></td>

                        <td><?= nurseIndexEscape($nurse['shift']) ?></td>

                        <td>
                            <?php if ((int)$nurse['status'] === 1): ?>
                                <span class="active">Active</span>
                            <?php else: ?>
                                <span class="inactive">Inactive</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="actions">

                                <a class="btn" href="/nurses/show?id=<?= (int)$nurse['id'] ?>">
                                    View
                                </a>

                                <a class="btn secondary" href="/nurses/edit?id=<?= (int)$nurse['id'] ?>">
                                    Edit
                                </a>

                                <form method="POST" action="/nurses/update-status">

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= nurseIndexEscape($csrfToken) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$nurse['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="<?= (int)$nurse['status'] === 1 ? '0' : '1' ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="<?= (int)$nurse['status'] === 1 ? 'danger' : '' ?>"
                                        onclick="return confirm('Change this nurse status?')"
                                    >
                                        <?= (int)$nurse['status'] === 1 ? 'Deactivate' : 'Activate' ?>
                                    </button>

                                </form>

                            </div>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7">No nurses found.</td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>

    </div>

    <p><a href="/">Back to Dashboard</a></p>

</div>

</body>
</html>
