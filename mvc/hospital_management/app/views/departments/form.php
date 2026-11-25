

<?php

$isEdit = !empty($department);

$name = $department['department_name'] ?? '';

$description = $department['description'] ?? '';

$status = $department['status'] ?? 1;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
        }

        .container {
            width: 450px;
            max-width: 94%;
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px #ddd;
        }

        h2 {
            text-align: center;
            color: #263b5e;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 13px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #2563eb;
            text-decoration: none;
        }

        .error {
            color: #b91c1c;
            background: #fee2e2;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

    </style>

</head>

<body>

    <div class="container">

        <h2>
            <?= htmlspecialchars($pageTitle) ?>
        </h2>


        <?php if (!empty($error)): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <form action="<?= htmlspecialchars($formAction) ?>"
              method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($csrfToken) ?>"
            >


            <label>Department Name</label>

            <input
                type="text"
                name="department_name"
                placeholder="Enter department name"
                value="<?= htmlspecialchars($name) ?>"
                maxlength="100"
                required
            >


            <label>Description</label>

            <textarea
                name="description"
                rows="5"
                placeholder="Enter department description"
            ><?= htmlspecialchars($description) ?></textarea>


            <?php if ($isEdit): ?>

                <label>Department Status</label>

                <select name="status">

                    <option value="1"
                        <?= (string)$status === '1' ? 'selected' : '' ?>>
                        Active
                    </option>

                    <option value="0"
                        <?= (string)$status === '0' ? 'selected' : '' ?>>
                        Inactive
                    </option>

                </select>

            <?php endif; ?>


            <button type="submit">

                <?= $isEdit ? 'Update Department' : 'Save Department' ?>

            </button>

        </form>


        <a href="/departments" class="back">
            Back to Department List
        </a>

    </div>

</body>

</html>
