
<?php

$isEdit = !empty($doctor);

$doctor = $doctor ?? [];

$firstName = $doctor['first_name'] ?? '';
$lastName = $doctor['last_name'] ?? '';
$gender = $doctor['gender'] ?? '';
$dob = $doctor['date_of_birth'] ?? '';
$phone = $doctor['phone'] ?? '';
$address = $doctor['address'] ?? '';
$qualification = $doctor['qualification'] ?? '';
$specialization = $doctor['specialization'] ?? '';
$experience = $doctor['experience'] ?? '';
$registration = $doctor['registration_number'] ?? '';
$departmentId = $doctor['department_id'] ?? '';

$username = $doctor['username'] ?? '';
$email = $doctor['email'] ?? '';

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
            margin: 35px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px #ddd;
        }

        h2 {
            text-align: center;
            color: #263b5e;
            margin-bottom: 30px;
        }

        h3 {
            color: #2563eb;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-top: 25px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        .full {
            grid-column: 1 / -1;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
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


            <!-- PERSONAL INFORMATION -->

            <h3>Personal Information</h3>

            <div class="form-grid">

                <div class="form-group">

                    <label>First Name *</label>

                    <input
                        type="text"
                        name="first_name"
                        value="<?= htmlspecialchars($firstName) ?>"
                        maxlength="100"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Last Name *</label>

                    <input
                        type="text"
                        name="last_name"
                        value="<?= htmlspecialchars($lastName) ?>"
                        maxlength="100"
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
                        max="<?= date('Y-m-d', strtotime('-1 day')) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Phone Number *</label>

                    <input
                        type="tel"
                        name="phone"
                        value="<?= htmlspecialchars($phone) ?>"
                        maxlength="20"
                        required
                    >

                </div>


                <div class="form-group full">

                    <label>Address</label>

                    <textarea
                        name="address"
                        rows="3"
                    ><?= htmlspecialchars($address) ?></textarea>

                </div>

            </div>


            <!-- PROFESSIONAL INFORMATION -->

            <h3>Professional Information</h3>

            <div class="form-grid">

                <div class="form-group">

                    <label>Department *</label>

                    <select name="department_id" required>

                        <option value="">Select Department</option>

                        <?php foreach ($departments as $department): ?>

                            <option
                                value="<?= (int)$department['id'] ?>"
                                <?= (string)$departmentId ===
                                    (string)$department['id']
                                    ? 'selected'
                                    : '' ?>
                            >

                                <?= htmlspecialchars(
                                    $department['department_name']
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label>Qualification *</label>

                    <input
                        type="text"
                        name="qualification"
                        placeholder="Example: MBBS, MD"
                        value="<?= htmlspecialchars($qualification) ?>"
                        maxlength="150"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Specialization *</label>

                    <input
                        type="text"
                        name="specialization"
                        placeholder="Example: Cardiology"
                        value="<?= htmlspecialchars($specialization) ?>"
                        maxlength="150"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Experience (Years) *</label>

                    <input
                        type="number"
                        name="experience"
                        min="0"
                        max="80"
                        value="<?= htmlspecialchars((string)$experience) ?>"
                        required
                    >

                </div>


                <div class="form-group full">

                    <label>Medical Registration Number *</label>

                    <input
                        type="text"
                        name="registration_number"
                        value="<?= htmlspecialchars($registration) ?>"
                        maxlength="100"
                        required
                    >

                </div>

            </div>


            <!-- LOGIN INFORMATION -->

            <h3>Login Information</h3>

            <div class="form-grid">

                <?php if (!$isEdit): ?>

                    <div class="form-group">

                        <label>Username *</label>

                        <input
                            type="text"
                            name="username"
                            value="<?= htmlspecialchars($username) ?>"
                            maxlength="100"
                            autocomplete="off"
                            required
                        >

                    </div>

                <?php else: ?>

                    <div class="form-group">

                        <label>Username</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars($username) ?>"
                            disabled
                        >

                    </div>

                <?php endif; ?>


                <div class="form-group">

                    <label>Email *</label>

                    <input
                        type="email"
                        name="email"
                        value="<?= htmlspecialchars($email) ?>"
                        maxlength="150"
                        required
                    >

                </div>


                <?php if (!$isEdit): ?>

                    <div class="form-group full">

                        <label>Initial Password *</label>

                        <input
                            type="password"
                            name="password"
                            minlength="12"
                            autocomplete="new-password"
                            required
                        >

                        <small>
                            Use at least 12 characters.
                            Share credentials securely with the doctor.
                        </small>

                    </div>

                <?php endif; ?>

            </div>


            <button type="submit">

                <?= $isEdit
                    ? 'Update Doctor'
                    : 'Register Doctor' ?>

            </button>

        </form>


        <a href="/doctors" class="back">
            Back to Doctor List
        </a>

    </div>

</body>

</html>
