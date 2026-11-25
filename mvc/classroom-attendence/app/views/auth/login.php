
<?php
$error = $error ?? null;
$csrfToken = $csrfToken ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Classroom Attendance System</title>

    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

<div class="login-container">

    <div class="login-card">

        <div class="logo">
            <div class="logo-icon">CA</div>
            <h2>Classroom Attendance</h2>
            <p>Attendance Management System</p>
        </div>

        <div class="login-heading">
            <h2>Welcome Back!</h2>
            <p>Please login to your account</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($csrfToken) ?>"
            >

            <div class="form-group">
                <label>Username</label>

                <input
                    type="text"
                    name="username"
                    placeholder="Enter your username"
                    required
                    autocomplete="username"
                >
            </div>

            <div class="form-group">
                <label>Password</label>

                <div class="password-box">

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password"
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        id="togglePassword"
                    >
                        Show
                    </button>

                </div>
            </div>

            <button type="submit" class="login-btn">
                Login
            </button>

        </form>
        <div class="register-link">
        <p>New user? 
            <a href="/register">Create an account</a>
        </p>
        </div>



        <div class="login-footer">
            Classroom Attendance System
            <br>
            <span>© <?= date('Y') ?> All Rights Reserved</span>
        </div>

    </div>

</div>

<script src="/js/script.js"></script>

</body>
</html>
