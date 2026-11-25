
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Hospital Login</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #edf2f7;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 380px;
            max-width: 92%;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 20px #d5dce5;
        }

        h2 {
            text-align: center;
            color: #263b5e;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 6px;
            background: #176b87;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #10536b;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

    </style>

</head>

<body>

    <div class="login-container">

        <h2>Younseii Hospital</h2>

        <p class="subtitle">
            Login to your account
        </p>

        <?php if (!empty($error)): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form action="/login" method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($csrfToken) ?>"
            >

            <label>Username</label>

            <input
                type="text"
                name="username"
                placeholder="Enter username"
                required
                autocomplete="username"
            >

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter password"
                required
                autocomplete="current-password"
            >

            <button type="submit">
                Login
            </button>

        </form>

    </div>

</body>

</html>
