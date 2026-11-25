
<?php

class Router
{
    private $routes;

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove trailing slash
        $uri = rtrim($uri, '/') ?: '/';

        if (isset($this->routes[$method][$uri])) {

            [$controllerName, $action] =
                $this->routes[$method][$uri];

            $controller = new $controllerName();

            $controller->$action();

        } else {

            http_response_code(404);

            echo "404 - Page Not Found";
        }
    }
}
