
<?php

// Load core files
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Controller.php';

require_once __DIR__ . '/../routes/web.php';

// Support running from a subdirectory such as /hospital-management/public.
$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if ($baseUrl === '/') {
    $baseUrl = '';
}
define('APP_BASE_URL', $baseUrl);

// Create database connection
$database = new Database();
$db = $database->connect();

// Get URL path
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (
    APP_BASE_URL !== '' &&
    ($url === APP_BASE_URL || strpos($url, APP_BASE_URL . '/') === 0)
) {
    $url = substr($url, strlen(APP_BASE_URL));
}

$url = rtrim($url, '/');

if ($url === '') {
    $url = '/';
}

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Build route key
$routeKey = $method . ' ' . $url;

// Check route
if (isset($routes[$routeKey])) {

    $controllerName = $routes[$routeKey]['controller'];

    $methodName = $routes[$routeKey]['method'];

    // Controller file path
    $controllerFile = __DIR__ . '/../app/Controllers/'. $controllerName . '.php';

    // Check controller file
    if (file_exists($controllerFile)) {

        require_once $controllerFile;

        // Create controller with database connection
        $controller = new $controllerName($db);

        // Call controller method
        $controller->$methodName();

    } else {

        http_response_code(500);

        echo "Controller file not found: " . $controllerFile;

    }

} else {

    http_response_code(404);

    echo "404 - Page Not Found";

}
