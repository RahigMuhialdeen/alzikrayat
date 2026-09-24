<?php

/**
 * Hand-written regular-expression router used instead of a backend framework.
 *
 * Route placeholders such as {id} are converted into regular-expression groups,
 * then matched against the current request path before the controller action is called.
 */
class Router
{
    /** @var array<int, array{0:string,1:string,2:array{0:string,1:string}}> Registered routes. */
    private array $routes = [];

    /**
     * Registers an HTTP route and its controller handler.
     *
     * @param string $method HTTP method such as GET or POST.
     * @param string $path Route path, optionally containing placeholders such as {id}.
     * @param array{0:string,1:string} $handler Controller class and action method.
     * @return void
     */
    public function add(string $method, string $path, array $handler): void
    {
        $this->routes[] = [strtoupper($method), $path, $handler];
    }

    /**
     * Matches a request against the registered routes and dispatches its controller action.
     *
     * @param string $method Current HTTP request method.
     * @param string $uri Current request URI, including any query string.
     * @return void
     */
    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Remove the physical application base path so routes remain portable within XAMPP.
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        if ($basePath !== '' && $basePath !== '/' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }
        $path = '/' . ltrim($path, '/');

        foreach ($this->routes as [$routeMethod, $routePath, $handler]) {
            if ($routeMethod !== $method) {
                continue;
            }

            $paramNames = [];
            $pattern = preg_replace_callback(
                '/\{([a-zA-Z][a-zA-Z0-9_]*)\}/',
                function (array $match) use (&$paramNames): string {
                    $paramNames[] = $match[1];
                    return '([^/]+)';
                },
                $routePath
            );

            $pattern = '#^' . $pattern . '$#';
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                $params = [];

                foreach ($paramNames as $index => $name) {
                    $params[$name] = $matches[$index] ?? null;
                }

                [$controllerClass, $action] = $handler;
                $controller = new $controllerClass();
                $controller->$action($params);
                return;
            }
        }

        http_response_code(404);
        require __DIR__ . '/../views/layout/404.php';
    }
}
