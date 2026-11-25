
<?php
function nurseEditEscape($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Nurse</title>

    <style>
        body { font-family: Arial; background: #f4f6f9; padding: 25px; }
        .container { max-width: 850px; margin: auto; background: white; padding: 25px; border-radius: 10px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
        label { display: block; font-weight: bold; margin-bottom: 7px; }
        input, select, textarea { width: 100%; box-sizing: border-box; padding: 11px; border: 1px solid #ccc; border-radius: 5px; }
        .full { grid-column: 1 / -1; }
        button { background: #263b5e; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; }
        @media(max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Edit Nurse Details</h2>

    <form method="POST" action="/nurses/update">

        <input type="hidden" name="csrf_token" value="<?= nurseEditEscape($csrfToken) ?>">
        <input type="hidden" name="id" value="<?= (int)$nurse['id'] ?>">

        <div class="form-grid">

            <div>
                <label>Nurse Number</label>
                <input type="text" value="<?= nurseEditEscape($nurse['nurse_number']) ?>" disabled>
            </div>

            <div>
                <label>First Name *</label>
                <input type="text" name="first_name" value="<?= nurseEditEscape($nurse['first_name']) ?>" required>
            </div>

            <div>
                <label>Last Name *</label>
                <input type="text" name="last_name" value="<?= nurseEditEscape($nurse['last_name']) ?>" required>
            </div>

            <div>
                <label>Department *</label>
                <select name="department_id" required>
                    <?php foreach ($departments as $department): ?>
                        <option
                            value="<?= (int)$department['id'] ?>"
                            <?= (int)$nurse['department_id'] === (int)$department['id'] ? 'selected' : '' ?>
                        >
                            <?= nurseEditEscape($department['department_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label>Gender</label>
                <select name="gender">
                    <?php foreach (['Male', 'Female', 'Other'] as $gender): ?>
                        <option
                            value="<?= $gender ?>"
                            <?= $nurse['gender'] === $gender ? 'selected' : '' ?>
                        >
                            <?= $gender ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label>Qualification</label>
                <input type="text" name="qualification" value="<?= nurseEditEscape($nurse['qualification']) ?>">
            </div>

            <div>
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= nurseEditEscape($nurse['phone']) ?>">
            </div>

            <div>
                <label>Email</label>
                <input type="email" name="email" value="<?= nurseEditEscape($nurse['email']) ?>">
            </div>

            <div>
                <label>Experience (Years)</label>
                <input type="number" name="experience_years" min="0" max="999.9" step="0.1" value="<?= nurseEditEscape($nurse['experience_years']) ?>">
            </div>

            <div>
                <label>Shift</label>
                <select name="shift">
                    <?php foreach (['Day', 'Night', 'Rotational'] as $shift): ?>
                        <option
                            value="<?= $shift ?>"
                            <?= $nurse['shift'] === $shift ? 'selected' : '' ?>
                        >
                            <?= $shift ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="full">
                <label>Address</label>
                <textarea name="address" rows="3"><?= nurseEditEscape($nurse['address']) ?></textarea>
            </div>

        </div>

        <br>

        <button type="submit">Update Nurse</button>

        <a href="/nurses">Cancel</a>

    </form>

</div>

</body>
</html>
