
<?php

class Router
{
    private $routes = [];

    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch($method, $uri)
    {
        $method = strtoupper($method);

        // Remove query string from URL
        $uri = parse_url($uri, PHP_URL_PATH);

        // Check registered routes
        foreach ($this->routes[$method] ?? [] as $route => $callback) {

            // Convert {id} into a numeric parameter
            $pattern = preg_replace(
                '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
                '([0-9]+)',
                $route
            );

            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {

                array_shift($matches);

                call_user_func_array($callback, $matches);

                return;
            }
        }

        http_response_code(404);
        echo "404 - Page Not Found";
    }
}
