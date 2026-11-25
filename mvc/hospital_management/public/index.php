
<?php

// Development error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Define project directory
define('BASE_PATH', dirname(__DIR__));



// Load Core Classes
require_once BASE_PATH . '/app/core/Database.php';

require_once BASE_PATH . '/app/core/Model.php';

require_once BASE_PATH . '/app/core/Controller.php';

require_once BASE_PATH . '/app/core/Router.php';

require_once BASE_PATH . '/app/core/Auth.php';


// Start session before sending any output

Auth::start();






// Load Models
require_once BASE_PATH . '/app/models/User.php';

require_once BASE_PATH . '/app/models/Department.php';

require_once BASE_PATH . '/app/models/Doctor.php';

require_once BASE_PATH . '/app/models/Patient.php';

require_once BASE_PATH . '/app/models/Appointment.php';

require_once BASE_PATH . '/app/models/Billing.php';

require_once BASE_PATH . '/app/models/Prescription.php';

require_once BASE_PATH . '/app/models/Nurse.php';

require_once __DIR__ . '/../app/models/Ward.php';


// Load Controllers
require_once BASE_PATH . '/app/controllers/HomeController.php';

require_once BASE_PATH . '/app/controllers/AuthController.php';

require_once BASE_PATH . '/app/controllers/DashboardController.php';

require_once BASE_PATH . '/app/controllers/DepartmentController.php';

require_once BASE_PATH . '/app/controllers/DoctorController.php';

require_once BASE_PATH . '/app/controllers/PatientController.php';

require_once BASE_PATH . '/app/controllers/AppointmentController.php';

require_once BASE_PATH . '/app/controllers/BillingController.php';

require_once BASE_PATH . '/app/controllers/PrescriptionController.php';

require_once BASE_PATH . '/app/controllers/NurseController.php';

require_once __DIR__ . '/../app/controllers/WardController.php';
// Create Router
$router = new Router();


// Load Routes
require_once BASE_PATH . '/routes/web.php';


// Get requested URL
$uri = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
);

// Remove trailing slash
if ($uri !== '/') {
    $uri = rtrim($uri, '/');
}

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];


// Dispatch request
$router->dispatch($method, $uri);

