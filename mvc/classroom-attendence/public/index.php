<?php

session_start();

//load core files
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/Router.php';

//load models
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Dashboard.php';

//load Controllers
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';

//load routes
require_once __DIR__ . '/../routes/web.php';

//start router
$router=new Router($routes);

$router->dispatch();


try{ 
    $db=Database::connect();
}catch(Exception $e)
{
    echo $e->getMessage();
}
?>