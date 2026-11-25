
<?php
function nurseCreateEscape($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Nurse</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 25px;
        }

        .container {
            max-width: 850px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h2 { color: #263b5e; }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input, select, textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .full { grid-column: 1 / -1; }

        button {
            background: #263b5e;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        @media(max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Add New Nurse</h2>

    <form method="POST" action="/nurses/store">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= nurseCreateEscape($csrfToken) ?>"
        >

        <div class="form-grid">

            <div>
                <label>Nurse Number *</label>
                <input type="text" name="nurse_number" required>
            </div>

            <div class="form-group">
    <label for="username">Username *</label>
    <input
        type="text"
        id="username"
        name="username"
        placeholder="Enter login username"
        required
    >
</div>

<div class="form-group">
    <label for="password">Login Password *</label>
    <input
        type="password"
        id="password"
        name="password"
        placeholder="Enter temporary password"
        minlength="8"
        required
    >
</div>

            <div>
                <label>First Name *</label>
                <input type="text" name="first_name" required>
            </div>

            <div>
                <label>Last Name *</label>
                <input type="text" name="last_name" required>
            </div>

            <div>
                <label>Department *</label>
                <select name="department_id" required>
                    <option value="">Select Department</option>

                    <?php foreach ($departments as $department): ?>
                        <option value="<?= (int)$department['id'] ?>">
                            <?= nurseCreateEscape($department['department_name']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div>
                <label>Gender</label>
                <select name="gender">
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div>
                <label>Qualification</label>
                <input type="text" name="qualification" placeholder="e.g. B.Sc Nursing">
            </div>

            <div>
                <label>Phone</label>
                <input type="tel" name="phone">
            </div>

            <div>
                <label>Email</label>
                <input type="email" name="email">
            </div>

            <div>
                <label>Experience (Years)</label>
                <input type="number" name="experience_years" min="0" max="999.9" step="0.1" value="0">
            </div>

            <div>
                <label>Shift</label>
                <select name="shift">
                    <option value="">Select Shift</option>
                    <option value="Day">Day</option>
                    <option value="Night">Night</option>
                    <option value="Rotational">Rotational</option>
                </select>
            </div>

            <div class="full">
                <label>Address</label>
                <textarea name="address" rows="3"></textarea>
            </div>

        </div>

        <br>

        <button type="submit">Save Nurse</button>

        <a href="/nurses">Cancel</a>

    </form>

</div>

</body>
</html>
