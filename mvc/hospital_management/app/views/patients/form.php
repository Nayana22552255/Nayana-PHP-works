
<?php

$isEdit = !empty($patient);

$patient = $patient ?? [];

$firstName = $patient['first_name'] ?? '';
$lastName = $patient['last_name'] ?? '';
$gender = $patient['gender'] ?? '';
$dob = $patient['date_of_birth'] ?? '';
$bloodGroup = $patient['blood_group'] ?? 'Unknown';
$phone = $patient['phone'] ?? '';
$email = $patient['email'] ?? '';
$address = $patient['address'] ?? '';
$emergencyName = $patient['emergency_contact_name'] ?? '';
$emergencyPhone = $patient['emergency_contact_phone'] ?? '';

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
            width: 750px;
            max-width: 95%;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px #ddd;
        }

        h2 {
            color: #263b5e;
            text-align: center;
        }

        h3 {
            color: #2563eb;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-top: 30px;
        }

        .patient-number {
            background: #eff6ff;
            padding: 12px;
            border-radius: 5px;
            color: #1d4ed8;
            margin: 20px 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input, select, textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 25px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #2563eb;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 5px;
            margin: 20px 0;
        }

        @media(max-width: 600px) {
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

    <h2><?= htmlspecialchars($pageTitle) ?></h2>


    <?php if ($isEdit && !empty($patient['patient_number'])): ?>

        <div class="patient-number">

            Patient ID:
            <strong>
                <?= htmlspecialchars($patient['patient_number']) ?>
            </strong>

        </div>

    <?php endif; ?>


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


        <!-- PERSONAL DETAILS -->

        <h3>Personal Information</h3>

        <div class="form-grid">

            <div class="form-group">

                <label>First Name *</label>

                <input
                    type="text"
                    name="first_name"
                    maxlength="100"
                    value="<?= htmlspecialchars($firstName) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Last Name *</label>

                <input
                    type="text"
                    name="last_name"
                    maxlength="100"
                    value="<?= htmlspecialchars($lastName) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Gender *</label>

                <select name="gender" required>

                    <option value="">Select Gender</option>

                    <option value="Male"
                        <?= $gender === 'Male' ? 'selected' : '' ?>>
                        Male
                    </option>

                    <option value="Female"
                        <?= $gender === 'Female' ? 'selected' : '' ?>>
                        Female
                    </option>

                    <option value="Other"
                        <?= $gender === 'Other' ? 'selected' : '' ?>>
                        Other
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label>Date of Birth *</label>

                <input
                    type="date"
                    name="date_of_birth"
                    value="<?= htmlspecialchars($dob) ?>"
                    max="<?= date('Y-m-d') ?>"
                    required
                >

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

                        <option
                            value="<?= $group ?>"
                            <?= $bloodGroup === $group ? 'selected' : '' ?>
                        >
                            <?= $group ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="form-group">

                <label>Phone Number *</label>

                <input
                    type="tel"
                    name="phone"
                    maxlength="20"
                    value="<?= htmlspecialchars($phone) ?>"
                    required
                >

            </div>


            <div class="form-group full">

                <label>Email Address</label>

                <input
                    type="email"
                    name="email"
                    maxlength="150"
                    value="<?= htmlspecialchars($email) ?>"
                >

            </div>


            <div class="form-group full">

                <label>Residential Address</label>

                <textarea
                    name="address"
                    rows="3"
                ><?= htmlspecialchars($address) ?></textarea>

            </div>

        </div>


        <!-- EMERGENCY CONTACT -->

        <h3>Emergency Contact Information</h3>

        <div class="form-grid">

            <div class="form-group">

                <label>Contact Person Name</label>

                <input
                    type="text"
                    name="emergency_contact_name"
                    maxlength="150"
                    value="<?= htmlspecialchars($emergencyName) ?>"
                >

            </div>


            <div class="form-group">

                <label>Emergency Phone Number</label>

                <input
                    type="tel"
                    name="emergency_contact_phone"
                    maxlength="20"
                    value="<?= htmlspecialchars($emergencyPhone) ?>"
                >

            </div>

        </div>


        <button type="submit">

            <?= $isEdit
                ? 'Update Patient'
                : 'Register Patient' ?>

        </button>

    </form>


    <a href="/patients" class="back">
        Back to Patient List
    </a>

</div>

</body>
</html>
