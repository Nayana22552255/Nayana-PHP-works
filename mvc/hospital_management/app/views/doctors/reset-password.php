
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Doctor Password</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            overflow:hidden;
        }

        .navbar {
            background: #263b5e;
            color: white;
            padding: 18px 30px;
        }

        .container {
            max-width: 500px;
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 12px #ddd;
        }

        h2 {
            color: #263b5e;
            margin-top: 0;
        }

        label {
            display: block;
            margin-top: 20px;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .reset-btn {
            width: 100%;
            margin-top: 25px;
            padding: 12px;
            background: #7c3aed;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .back-btn {
            display: inline-block;
            margin-top: 18px;
            color: #263b5e;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <h3>Hospital Management System - Admin</h3>
    </div>

    <div class="container">

        <h2>Reset Doctor Password</h2>

        <p>
            Set a new login password for this doctor.
        </p>

        <form
            action="/admin/doctors/<?= (int)$doctor['id'] ?>/reset-password"
            method="POST"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($csrfToken) ?>"
            >

            <label>Username</label>

            <input
                type="text"
                value="<?= htmlspecialchars($doctor['username']) ?>"
                readonly
            >

            <label>New Password</label>

            <input
                type="password"
                name="new_password"
                placeholder="Enter new password"
                minlength="8"
                required
            >

            <button type="submit" class="reset-btn">
                Reset
            </button>

        </form>

        <a href="/doctors" class="back-btn">
            ← Back to Doctor List
        </a>

    </div>

</body>
</html>
