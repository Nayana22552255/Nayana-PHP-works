
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Patient Profile</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
        }

        .container {
            max-width: 750px;
            width: 95%;
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
        }

        .patient-id {
            background: #eff6ff;
            color: #1d4ed8;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            font-size: 18px;
            margin: 20px 0;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .detail {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .detail strong {
            display: block;
            color: #555;
            margin-bottom: 7px;
        }

        .buttons {
            margin-top: 25px;
            display: flex;
            gap: 12px;
        }

        .buttons a {
            padding: 12px 18px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
        }

        .edit {
            background: #f59e0b;
        }

        .back {
            background: #2563eb;
        }

        @media(max-width: 550px) {
            .details {
                grid-template-columns: 1fr;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <h2>Patient Profile</h2>


    <div class="patient-id">

        Patient Registration Number:

        <strong>
            <?= htmlspecialchars($patient['patient_number']) ?>
        </strong>

    </div>


    <h3>Personal Information</h3>

    <div class="details">

        <div class="detail">

            <strong>Patient Name</strong>

            <?= htmlspecialchars(
                $patient['first_name'] . ' ' .
                $patient['last_name']
            ) ?>

        </div>


        <div class="detail">

            <strong>Gender</strong>

            <?= htmlspecialchars($patient['gender']) ?>

        </div>


        <div class="detail">

            <strong>Date of Birth</strong>

            <?= htmlspecialchars($patient['date_of_birth']) ?>

        </div>


        <div class="detail">

            <strong>Blood Group</strong>

            <?= htmlspecialchars($patient['blood_group']) ?>

        </div>


        <div class="detail">

            <strong>Phone Number</strong>

            <?= htmlspecialchars($patient['phone']) ?>

        </div>


        <div class="detail">

            <strong>Email</strong>

            <?= htmlspecialchars($patient['email'] ?: 'Not provided') ?>

        </div>


        <div class="detail">

            <strong>Address</strong>

            <?= nl2br(htmlspecialchars($patient['address'] ?: 'Not provided')) ?>

        </div>

    </div>


    <h3>Emergency Contact Information</h3>

    <div class="details">

        <div class="detail">

            <strong>Contact Person</strong>

            <?= htmlspecialchars(
                $patient['emergency_contact_name'] ?: 'Not provided'
            ) ?>

        </div>


        <div class="detail">

            <strong>Emergency Phone</strong>

            <?= htmlspecialchars(
                $patient['emergency_contact_phone'] ?: 'Not provided'
            ) ?>

        </div>

    </div>


    <h3>Registration Information</h3>

    <div class="details">

        <div class="detail">

            <strong>Registration Date</strong>

            <?= htmlspecialchars($patient['created_at']) ?>

        </div>


        <div class="detail">

            <strong>Account Status</strong>

            <?= $patient['status'] == 1 ? 'Active' : 'Inactive' ?>

        </div>

    </div>


    <div class="buttons">

        <a
            href="/patients/edit?id=<?= (int)$patient['id'] ?>"
            class="edit"
        >
            Edit Patient
        </a>

        <a href="/patients" class="back">
            Back to Patients
        </a>

    </div>

</div>

</body>
</html>
