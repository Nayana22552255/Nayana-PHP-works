
<?php
$wards = $wards ?? [];
$keyword = $keyword ?? '';
$csrfToken = $csrfToken ?? '';

$h = function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ward Management</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
          
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            overflow:hidden;
        }

        .navbar {
            background: #263b5e;
            color: white;
            padding: 18px 30px;
           
        }

        .container {
            width: 94%;
            margin: 35px auto;
        }

        /* Page heading */
        h2 {
            color: #263b5e;
            font-size: 26px;
            margin-bottom: 12px;
        }

        .back-container {
            margin-top:20px;
            text-align:center;
        }
        /* Back to dashboard */
        .back-link {
            display: inline-block;
            color: #263b5e;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .back-link:hover {
            color: #2155ae;
            text-decoration: underline;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 22px;
        }
         .header h2 {
            margin-bottom: 12px;
            color: #263b5e;
           
        }

        .add-btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
        }

        .add-btn:hover {
            background: #1d4ed8;
        }

        /* Search section */
        .search-form {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }

        .search-form input {
            width: 280px;
            max-width: 100%;
            padding: 11px 13px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }

        .search-form input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12);
        }

        .search-btn {
            background: #263b5e;
            color: white;
            border: none;
            padding: 11px 18px;
            border-radius: 6px;
            cursor: pointer;
        }

        .search-btn:hover {
            background: #172b4d;
        }

        .clear-btn {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            padding: 10px;
        }

        .clear-btn:hover {
            text-decoration: underline;
        }

        /* Table */
        .table-container {
            width: 100%;
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.07);
        }

        table {
            width: 100%;
            min-width: 1000px;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 15px 13px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        th {
            background: #e8edf5;
            color: #172b4d;
            font-weight: 700;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        /* Status */
        .active {
            color: #16a34a;
            font-weight: bold;
        }

        .inactive {
            color: #dc2626;
            font-weight: bold;
        }

        /* Action buttons */
        .action-buttons {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 7px;
        }

        .view-btn,
        .edit-btn,
        .status-btn {
            display: inline-block;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-size: 13px;
            cursor: pointer;
        }

        .view-btn {
            background: #2563eb;
        }

        .view-btn:hover {
            background: #1d4ed8;
        }

        .edit-btn {
            background: #f59e0b;
        }

        .edit-btn:hover {
            background: #d97706;
        }

        .deactivate-btn {
            background: #dc2626;
        }

        .deactivate-btn:hover {
            background: #b91c1c;
        }

        .activate-btn {
            background: #16a34a;
        }

        .activate-btn:hover {
            background: #15803d;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #64748b;
        }

        /* Responsive */
        @media (max-width: 700px) {
            .container {
                width: 95%;
                margin: 22px auto;
            }

            .header {
                align-items: flex-start;
                flex-direction: column;
            }

            .search-form input {
                width: 100%;
            }
        }
    </style>
</head>

<body>


<div class="navbar">

        <h3>Younseii Hospital System</h3>

</div>

<div class="container">

    <div class= "top">

       <h2>Ward Management</h2>


    </div>

    <div class="header">

        <h3>Hospital Ward Details</h3>

        <a href="/wards/create" class="add-btn">
            + Add New Ward
        </a>

    </div>

    <!-- Search -->
    <form method="GET" action="/wards" class="search-form">

        <input
            type="text"
            name="keyword"
            placeholder="Search ward..."
            value="<?= $h($keyword) ?>"
        >

        <button type="submit" class="search-btn">
            Search
        </button>

        <a href="/wards" class="clear-btn">
            Clear
        </a>

    </form>

    <!-- Ward table -->
    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>Ward Name</th>
                    <th>Ward Code</th>
                    <th>Ward Type</th>
                    <th>Rooms</th>
                    <th>Total Beds</th>
                    <th>Available Beds</th>
                    <th>Occupied Beds</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($wards)): ?>

                <?php foreach ($wards as $ward): ?>

                    <tr>

                        <td><?= $h($ward['ward_name']) ?></td>

                        <td><?= $h($ward['ward_code']) ?></td>

                        <td><?= $h($ward['ward_type']) ?></td>

                        <td><?= (int)$ward['total_rooms'] ?></td>

                        <td><?= (int)$ward['total_beds'] ?></td>

                        <td><?= (int)$ward['available_beds'] ?></td>

                        <td><?= (int)$ward['occupied_beds'] ?></td>

                        <td>
                            <?php if ($ward['status']): ?>
                                <span class="active">Active</span>
                            <?php else: ?>
                                <span class="inactive">Inactive</span>
                            <?php endif; ?>
                        </td>

                        <td>

                            <div class="action-buttons">

                                <a
                                    href="/wards/show?id=<?= (int)$ward['id'] ?>"
                                    class="view-btn"
                                >
                                    View
                                </a>

                                <a
                                    href="/wards/edit?id=<?= (int)$ward['id'] ?>"
                                    class="edit-btn"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="/wards/update-status"
                                    style="display:inline;"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= $h($csrfToken) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$ward['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="<?= $ward['status'] ? '0' : '1' ?>"
                                    >

                                    <?php if ($ward['status']): ?>

                                        <button
                                            type="submit"
                                            class="status-btn deactivate-btn"
                                        >
                                            Deactivate
                                        </button>

                                    <?php else: ?>

                                        <button
                                            type="submit"
                                            class="status-btn activate-btn"
                                        >
                                            Activate
                                        </button>

                                    <?php endif; ?>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="9" class="empty">
                        No wards found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<div class="back-container">
     <a href="/admin/dashboard" class="back-link">
            ← Back to Dashboard
        </a>
</div>

</body>
</html>
