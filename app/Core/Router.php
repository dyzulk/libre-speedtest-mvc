<?php

namespace App\Core;

class Router
{
    protected $routes = [];

    /**
     * Add a route to the router.
     *
     * @param string $method
     * @param string $route
     * @param string $controllerAction
     * @param array $middlewares
     * @return void
     */
    public function add(string $method, string $route, string $controllerAction, array $middlewares = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'route' => $route,
            'controllerAction' => $controllerAction,
            'middlewares' => $middlewares
        ];
    }

    /**
     * Dispatch the current request to the correct controller action.
     *
     * @param string $requestUri
     * @param string $requestMethod
     * @return void
     */
    public function dispatch(string $requestUri, string $requestMethod): void
    {
        $path = parse_url($requestUri, PHP_URL_PATH);
        $method = strtoupper($requestMethod);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            // Convert dynamic placeholders like {id} to named regex capture groups
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $route['route']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Run middlewares registered on the route
                $middlewares = $route['middlewares'] ?? [];
                foreach ($middlewares as $middlewareName) {
                    $middlewareClass = "App\\Middleware\\" . $middlewareName;
                    if (class_exists($middlewareClass)) {
                        $middleware = new $middlewareClass();
                        $middleware->handle();
                    }
                }
                
                list($controllerName, $actionName) = explode('@', $route['controllerAction']);
                $controllerClass = "App\\Controllers\\" . $controllerName;

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $actionName)) {
                        call_user_func_array([$controller, $actionName], $params);
                        return;
                    }
                }

                $this->abort(500, "Action {$actionName} not found in {$controllerClass}");
                return;
            }
        }

        $this->abort(404, "Page Not Found");
    }

    /**
     * Aborts request with status code and message.
     */
    protected function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        echo "Error {$code}: {$message}";
        exit();
    }
}
