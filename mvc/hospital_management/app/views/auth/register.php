
<?php
$data = $data ?? [];

function registerValue($field, $data)
{
    return htmlspecialchars($data[$field] ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Patient Registration</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 30px 15px;
        }

        .container {
            max-width: 750px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            color: #176b87;
            margin-bottom: 25px;
        }

        h3 {
            color: #176b87;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
            margin-top: 25px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 13px;
            background: #176b87;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
        }

        button:hover {
            background: #10536b;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: auto;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Patient Registration</h2>

    <?php if (!empty($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form action="/register" method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
        >

        <h3>Personal Information</h3>

        <div class="form-grid">

            <div class="form-group">
                <label>First Name *</label>
                <input type="text" name="first_name"
                       value="<?= registerValue('first_name', $data) ?>"
                       maxlength="100" required>
            </div>

            <div class="form-group">
                <label>Last Name *</label>
                <input type="text" name="last_name"
                       value="<?= registerValue('last_name', $data) ?>"
                       maxlength="100" required>
            </div>

            <div class="form-group">
                <label>Gender *</label>
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <?php foreach (['Male', 'Female', 'Other'] as $gender): ?>
                        <option value="<?= $gender ?>"
                            <?= (($data['gender'] ?? '') === $gender) ? 'selected' : '' ?>>
                            <?= $gender ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Date of Birth *</label>
                <input type="date" name="date_of_birth"
                       value="<?= registerValue('date_of_birth', $data) ?>"
                       max="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label>Blood Group</label>
                <select name="blood_group">
                    <?php
                    $groups = [
                        'Unknown', 'A+', 'A-', 'B+', 'B-',
                        'AB+', 'AB-', 'O+', 'O-'
                    ];
                    ?>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= $group ?>"
                            <?= (($data['blood_group'] ?? 'Unknown') === $group) ? 'selected' : '' ?>>
                            <?= $group ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Phone Number *</label>
                <input type="tel" name="phone"
                       value="<?= registerValue('phone', $data) ?>"
                       maxlength="20" required>
            </div>

            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email"
                       value="<?= registerValue('email', $data) ?>"
                       maxlength="150" required>
            </div>

            <div class="form-group full">
                <label>Address</label>
                <textarea name="address" rows="3"><?= registerValue('address', $data) ?></textarea>
            </div>

        </div>

        <h3>Emergency Contact</h3>

        <div class="form-grid">

            <div class="form-group">
                <label>Contact Name</label>
                <input type="text" name="emergency_contact_name"
                       value="<?= registerValue('emergency_contact_name', $data) ?>"
                       maxlength="150">
            </div>

            <div class="form-group">
                <label>Contact Phone</label>
                <input type="tel" name="emergency_contact_phone"
                       value="<?= registerValue('emergency_contact_phone', $data) ?>"
                       maxlength="20">
            </div>

        </div>

        <h3>Login Information</h3>

        <div class="form-grid">

            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username"
                       value="<?= registerValue('username', $data) ?>"
                       minlength="4" maxlength="100"
                       pattern="[A-Za-z0-9_]{4,100}"
                       required>
            </div>

            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password"
                       minlength="8" required>
            </div>

            <div class="form-group full">
                <label>Confirm Password *</label>
                <input type="password" name="confirm_password"
                       minlength="8" required>
            </div>

        </div>

        <button type="submit">Create Patient Account</button>

    </form>

    <div class="login-link">
        Already have an account?
        <a href="/login">Login here</a>
    </div>

</div>

</body>
</html>
