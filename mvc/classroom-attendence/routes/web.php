
<?php

$routes = [

    'GET' => [
        '/' => ['AuthController', 'login'],
        '/login' => ['AuthController', 'login'],
        '/logout' => ['AuthController', 'logout'],
        '/dashboard' => ['DashboardController', 'index'],
    ],

    'POST' => [
        '/login' => ['AuthController', 'authenticate'],
    ]

];
