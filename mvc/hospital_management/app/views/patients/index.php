
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Patient Management</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f6f9;
              padding-top: 90px;
       
        }

        .navbar {
           position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 90px;
            background: #263e60;
            display: flex;
            align-items: center;
            padding: 0 30px;
            box-sizing: border-box;
            z-index: 1000;
        }
        .navbar h3 {
            color: #ffffff;
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
            flex-wrap: wrap;
            gap: 15px;
        }

        h2 {
            color: #263b5e;
        }
        .back-container {
            margin-top:20px;
            text-align:center;
        }
        .back-link {
            display:inline-block;
            color: #263b5e;
            text-decoration:none;
            font-weight:600;
            margin-bottom:25px;
        }
        .back-link:hover {
            color: #2155ae;
        }

        .add-btn {
            background: #263b5e;
            color: white;
            padding: 12px 18px;
            border-radius: 5px;
            text-decoration: none;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin: 25px 0;
        }

        .search-form input {
            width: 350px;
            max-width: 70%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-form button {
            padding: 11px 18px;
            background: #263b5e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .clear {
            padding: 11px;
            color: #2563eb;
            text-decoration: none;
        }

        table {
            width: 100%;
            min-width: 1000px;
            background: white;
            border-collapse: collapse;
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

        .view-btn {
            background: #2563eb;
            color: white;
            padding: 7px 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .edit-btn {
            background: #f59e0b;
            color: white;
            padding: 7px 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: nowrap;
        }

        .action-buttons .action-form {
            display: inline-flex;
            margin: 0;
        }

        .status-btn {
            padding: 8px 10px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }

        .activate {
            background: #16a34a;
        }

        .deactivate {
            background: #dc2626;
        }

        .action-form {
            display: inline;
        }

        .empty {
            text-align: center;
            padding: 25px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 13px;
            margin: 15px 0;
            border-radius: 5px;
        }

    </style>

</head>

<body>

<div class="navbar">

    <h3>Hospital Management System</h3>

</div>


<div class="container">

    <div class="header">

        <h2>Patient Management</h2>

        <a href="/patients/create" class="add-btn">
            + Register Patient
        </a>

    </div>


    <?php if (!empty($_GET['registered'])): ?>

        <div class="success">

            Patient registered successfully!

            Patient ID:

            <strong>
                <?= htmlspecialchars($_GET['registered']) ?>
            </strong>

        </div>

    <?php endif; ?>


    <!-- SEARCH -->

    <form action="/patients"
          method="GET"
          class="search-form">

        <input
            type="text"
            name="search"
            placeholder="Search by patient ID, name or phone"
            value="<?= htmlspecialchars($keyword) ?>"
        >

        <button type="submit">
            Search
        </button>

        <?php if ($keyword !== ''): ?>

            <a href="/patients" class="clear">
                Clear
            </a>

        <?php endif; ?>

    </form>


    <!-- PATIENT TABLE -->

    <table>

        <thead>

            <tr>

                <th>ID</th>

                <th>Patient Number</th>

                <th>Patient Name</th>

                <th>Gender</th>

                <th>Age</th>

                <th>Phone</th>

                <th>Blood Group</th>

                <th>Status</th>

                <th>Actions</th>

            </tr>

        </thead>


        <tbody>

        <?php if (!empty($patients)): ?>

            <?php foreach ($patients as $patient): ?>

                <tr>

                    <td>
                        <?= (int)$patient['id'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($patient['patient_number']) ?>
                    </td>

                    <td>

                        <?= htmlspecialchars(
                            $patient['first_name'] . ' ' .
                            $patient['last_name']
                        ) ?>

                    </td>

                    <td>
                        <?= htmlspecialchars($patient['gender']) ?>
                    </td>

                    <td>

                        <?php
                        $birthDate = new DateTime(
                            $patient['date_of_birth']
                        );

                        $today = new DateTime();

                        echo $birthDate->diff($today)->y;
                        ?>

                    </td>

                    <td>
                        <?= htmlspecialchars($patient['phone']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($patient['blood_group']) ?>
                    </td>

                    <td>

                        <?php if ($patient['status'] == 1): ?>

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

                        <a
                            href="/patients/show?id=<?= (int)$patient['id'] ?>"
                            class="view-btn"
                        >
                            View
                        </a>


                        <a
                            href="/patients/edit?id=<?= (int)$patient['id'] ?>"
                            class="edit-btn"
                        >
                            Edit
                        </a>


                        <form
                            action="/patients/toggle-status"
                            method="POST"
                            class="action-form"
                            onsubmit="return confirm('Are you sure you want to change patient status?');"
                        >

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars($csrfToken) ?>"
                            >

                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int)$patient['id'] ?>"
                            >

                            <input
                                type="hidden"
                                name="status"
                                value="<?= $patient['status'] == 1 ? 0 : 1 ?>"
                            >

                            <?php if ($patient['status'] == 1): ?>

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
                        </div>
                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>

                <td colspan="9" class="empty">

                    No patients found.

                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>

<div class="back-container">
     <a href="/admin/dashboard" class="back-link">
            ← Back to Dashboard
        </a>
</div>

</body>
</html>
