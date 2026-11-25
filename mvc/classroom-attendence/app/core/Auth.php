<?php
 
 class Auth 
 {
    public static function check()
    {
        return isset($_SESSION['user_id']);
    }

    public static function user()
    {
        return $_SESSION['user']?? null;
    }

    public static function login($user)
    {
        session_regenerate_id(true);

        $_SESSION['user_id']= $user['id'];
        $_SESSION['user']=$user;
    }
    
    public static function logout()
    {
        // Clear all session variables
        $_SESSION = [];

        // Delete the session cookie
        if (ini_get("session.use_cookies")) {

            $params = session_get_cookie_params();

            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax'
            ]);
        }

        // Destroy the session
        session_destroy();
    }


    public static function csrfToken()
    {
        if(empty($_SESSION['csrf_token']))
            {
                $_SESSION['csrf_token']=bin2hex(random_bytes(32));
            }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf($token)
    {
        return isset($_SESSION['csrf_token']) &&
        is_string($token)&&
        hash_equals($_SESSION['csrf_token'],$token);
    } 
 }
?>