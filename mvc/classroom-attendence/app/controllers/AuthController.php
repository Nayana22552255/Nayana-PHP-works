
<?php

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // Display login page
    public function login()
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/login', [
            'error' => null,
            'csrfToken' => Auth::csrfToken()
        ]);
    }

    // Process login
    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);

            $this->view('auth/login', [
                'error' => 'Invalid security token. Please try again.',
                'csrfToken' => Auth::csrfToken()
            ]);

            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $this->view('auth/login', [
                'error' => 'Please enter username and password.',
                'csrfToken' => Auth::csrfToken()
            ]);

            return;
        }

        $user = $this->userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->view('auth/login', [
                'error' => 'Invalid username or password.',
                'csrfToken' => Auth::csrfToken()
            ]);

            return;
        }

        Auth::login($user);

        $this->redirect('/dashboard');
    }

    // Logout
    public function logout()
    {
        Auth::logout();

        $this->redirect('/login');
    }
}
