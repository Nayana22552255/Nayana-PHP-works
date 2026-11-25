
<?php

class AuthController extends Controller
{
    // Display login page
    public function login()
    {
        if (Auth::check()) {
            $this->redirectToDashboard();
        }

        $this->view('auth/login', [
            'error' => null,
            'csrfToken' => Auth::csrfToken()
        ]);
    }

        // Display public patient registration page
   /* public function register()
    {
        if (Auth::check()) {
            $this->redirectToDashboard();
        }

        $this->view('auth/register', [
            'error' => null,
            'csrfToken' => Auth::csrfToken()
        ]);
    }*/

    // Process login
    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $token = $_POST['csrf_token'] ?? '';

        if (!Auth::verifyCsrf($token)) {
            http_response_code(419);
            exit('Invalid or expired form token. Please reload the login page.');
        }

        $username = trim($_POST['username'] ?? '');

        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {

            $this->showLoginError(
                'Please enter username and password.'
            );

            return;
        }

        $userModel = new User();

        $user = $userModel->authenticate(
            $username,
            $password
        );

        if (!$user) {

            $this->showLoginError(
                'Invalid username or password.'
            );

            return;
        }

        // Create login session
        Auth::login($user);

        // Redirect based on role
        $this->redirectToDashboard();
    }

    // Redirect users according to their role
    private function redirectToDashboard()
    {
        $role = Auth::role();

        $routes = [
            'admin' => '/admin/dashboard',
            'doctor' => '/doctor/dashboard',
            'receptionist' => '/receptionist/dashboard',
            'nurse' => '/nurse/dashboard',
            'patient' => '/patient/dashboard'
        ];

        if (isset($routes[$role])) {

            header('Location: ' . $routes[$role]);
            exit;
        }

        Auth::logout();

        header('Location: /login');
        exit;
    }

    // Display login errors
    private function showLoginError($message)
    {
        $this->view('auth/login', [
            'error' => $message,
            'csrfToken' => Auth::csrfToken()
        ]);
    }

    // Logout
    public function logout()
{
    if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('Invalid form token.');
    }

    Auth::logout();

    header('Location: /');
    exit;
}


    // Display public patient registration form
    public function register()
    {
        $this->view('auth/register', [
            'error' => null,
            'csrfToken' => Auth::csrfToken(),
            'data' => []
        ]);
    }

    // Process public patient registration
    public function storeRegistration()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(419);
            exit('Invalid or expired form token. Please reload the page.');
        }

        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'gender' => $_POST['gender'] ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? '',
            'blood_group' => $_POST['blood_group'] ?? 'Unknown',
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'emergency_contact_name' =>
                trim($_POST['emergency_contact_name'] ?? ''),
            'emergency_contact_phone' =>
                trim($_POST['emergency_contact_phone'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
        ];

        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate required fields
        if (
            $data['first_name'] === '' ||
            $data['last_name'] === '' ||
            $data['gender'] === '' ||
            $data['date_of_birth'] === '' ||
            $data['phone'] === '' ||
            $data['email'] === '' ||
            $data['username'] === '' ||
            $password === '' ||
            $confirmPassword === ''
        ) {
            $this->showRegistrationError(
                'Please fill in all required fields.',
                $data
            );
            return;
        }

        // Validate email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->showRegistrationError(
                'Please enter a valid email address.',
                $data
            );
            return;
        }

        // Validate username
        if (!preg_match('/^[A-Za-z0-9_]{4,100}$/', $data['username'])) {
            $this->showRegistrationError(
                'Username must be 4-100 characters and contain only letters, numbers, and underscores.',
                $data
            );
            return;
        }

        // Validate password
        if (strlen($password) < 8) {
            $this->showRegistrationError(
                'Password must contain at least 8 characters.',
                $data
            );
            return;
        }

        if ($password !== $confirmPassword) {
            $this->showRegistrationError(
                'Passwords do not match.',
                $data
            );
            return;
        }

        // Validate gender
        if (!in_array($data['gender'], ['Male', 'Female', 'Other'], true)) {
            $this->showRegistrationError(
                'Invalid gender selection.',
                $data
            );
            return;
        }

        // Validate blood group
        $bloodGroups = [
            'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-',
            'O+', 'O-', 'Unknown'
        ];

        if (!in_array($data['blood_group'], $bloodGroups, true)) {
            $this->showRegistrationError(
                'Invalid blood group.',
                $data
            );
            return;
        }

        // Validate date of birth
        $date = DateTime::createFromFormat(
            '!Y-m-d',
            $data['date_of_birth']
        );

        if (
            !$date ||
            $date->format('Y-m-d') !== $data['date_of_birth'] ||
            $date > new DateTime('today')
        ) {
            $this->showRegistrationError(
                'Please enter a valid date of birth.',
                $data
            );
            return;
        }

        // Validate phone number
        if (!preg_match('/^\+?[0-9][0-9\s\-]{6,18}[0-9]$/', $data['phone'])) {
            $this->showRegistrationError(
                'Please enter a valid phone number.',
                $data
            );
            return;
        }

        if (
            $data['emergency_contact_phone'] !== '' &&
            !preg_match(
                '/^\+?[0-9][0-9\s\-]{6,18}[0-9]$/',
                $data['emergency_contact_phone']
            )
        ) {
            $this->showRegistrationError(
                'Please enter a valid emergency contact number.',
                $data
            );
            return;
        }

        if (
            $data['emergency_contact_name'] === '' &&
            $data['emergency_contact_phone'] !== ''
        ) {
            $this->showRegistrationError(
                'Please enter the emergency contact name.',
                $data
            );
            return;
        }

        try {
            $userModel = new User();
            $patientModel = new Patient();

            // Check for existing username
            if ($userModel->findByUsername($data['username'])) {
                $this->showRegistrationError(
                    'This username is already taken.',
                    $data
                );
                return;
            }

            // Check for existing email
            if ($userModel->findByEmail($data['email'])) {
                $this->showRegistrationError(
                    'This email address is already registered.',
                    $data
                );
                return;
            }

            // Both models use the same shared PDO connection
            $db = Database::connect();

            $db->beginTransaction();

            try {
                // Create patient login account
                $userId = $userModel->createUser([
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => $password,
                    'role' => 'patient'
                ]);

                // Create linked patient record
                $patientNumber = $patientModel->addPatientForUser(
                    $userId,
                    $data
                );

                $db->commit();

            } catch (Throwable $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }

                throw $e;
            }

            // Show successful registration
            $this->view('auth/register-success', [
                'patientNumber' => $patientNumber,
                'username' => $data['username']
            ]);

        } catch (Throwable $e) {
            error_log($e->getMessage());

            $this->showRegistrationError(
                'Unable to complete registration. Please try again.',
                $data
            );
        }
    }

    // Display registration form errors
    private function showRegistrationError($message, $data = [])
    {
        $this->view('auth/register', [
            'error' => $message,
            'csrfToken' => Auth::csrfToken(),
            'data' => $data
        ]);
    }

}
