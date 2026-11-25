
<?php

class Auth
{
    // Start session
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Login user
    public static function login($user)
    {
        self::start();

        // Prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'email'    => $user['email'],
            'role'     => $user['role']
        ];
    }

    // Check login status
    public static function check()
    {
        self::start();

        return isset($_SESSION['user']);
    }

    // Get logged-in user
    public static function user()
    {
        self::start();

        return $_SESSION['user'] ?? null;
    }

    // Get user role
    public static function role()
    {
        $user = self::user();

        return $user['role'] ?? null;
    }

    // Check role permission

        public static function requireRole($roles)
        {
            self::start();

            // Check whether user is logged in
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                exit;
            }

            // Convert a single role into an array
            if (!is_array($roles)) {
                $roles = [$roles];
            }

            // Check whether logged-in user has permission
            if (!in_array($_SESSION['user']['role'], $roles, true)) {
                http_response_code(403);
                echo "403 - Access Forbidden";
                exit;
            }
        }
    // Logout
    public static function logout()
    {
        self::start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    // Generate CSRF token
    public static function csrfToken()
    {
        self::start();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(
                random_bytes(32)
            );
        }

        return $_SESSION['csrf_token'];
    }

    // Verify CSRF token
    public static function verifyCsrf($token)
    {
        self::start();

        return isset($_SESSION['csrf_token'])
            && is_string($token)
            && hash_equals($_SESSION['csrf_token'], $token);
    }
}
