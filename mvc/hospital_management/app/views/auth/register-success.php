
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            text-align: center;
            padding: 60px 15px;
        }

        .success-box {
            background: white;
            max-width: 500px;
            margin: auto;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }

        h2 {
            color: #15803d;
        }

        .number {
            font-size: 22px;
            font-weight: bold;
            color: #176b87;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: #176b87;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="success-box">

    <h2>Registration Successful!</h2>

    <p>Your patient account has been created successfully.</p>

    <p>Your Patient Registration Number:</p>

    <div class="number">
        <?= htmlspecialchars($patientNumber, ENT_QUOTES, 'UTF-8') ?>
    </div>

    <p>
        Username:
        <strong><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></strong>
    </p>

    <p>Please keep your patient registration number safe.</p>

    <a href="/login">Go to Login</a>

</div>

</body>
</html>
