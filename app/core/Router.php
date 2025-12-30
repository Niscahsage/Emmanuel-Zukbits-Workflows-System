<?php
// app/core/Router.php
// Router registers routes and dispatches HTTP requests to controllers.

namespace App\core;

class Router
{
    // routes[method][] = ['path' => '/uri', 'handler' => ..., 'middleware' => [...]]
    protected static array $routes = [];

    // Registers a GET route
    public static function get(string $path, $handler, array $middleware = []): void
    {
        self::addRoute('GET', $path, $handler, $middleware);
    }

    // Registers a POST route
    public static function post(string $path, $handler, array $middleware = []): void
    {
        self::addRoute('POST', $path, $handler, $middleware);
    }

    // Registers a route for multiple methods
    public static function match(array $methods, string $path, $handler, array $middleware = []): void
    {
        foreach ($methods as $m) {
            self::addRoute(strtoupper($m), $path, $handler, $middleware);
        }
    }

    // Adds a route to the internal registry
    protected static function addRoute(string $method, string $path, $handler, array $middleware): void
    {
        $normalizedPath = self::normalizePath($path);
        $method = strtoupper($method);

        // Convert middleware string shortcuts into callables
        $mwCallables = [];
        foreach ($middleware as $mw) {
            if (is_callable($mw)) {
                $mwCallables[] = $mw;
                continue;
            }
            if (is_string($mw)) {
                $mwCallables[] = self::resolveMiddlewareString($mw);
            }
        }

        self::$routes[$method][] = [
            'path'       => $normalizedPath,
            'handler'    => $handler,
            'middleware' => $mwCallables,
        ];
    }

    // Dispatches the current request to the matched route
    public static function dispatch(string $method, string $uriPath): void
    {
        $method = strtoupper($method);
        $path   = self::normalizePath($uriPath);

        $routes = self::$routes[$method] ?? [];

        foreach ($routes as $route) {
            if ($route['path'] === $path) {
                // Run middleware chain
                foreach ($route['middleware'] as $mw) {
                    $mw(); // Middleware is a callable that may redirect or exit
                }

                self::invokeHandler($route['handler']);
                return;
            }
        }

        // No route matched
        http_response_code(404);
        try {
            View::render('errors/404', [
                'message' => 'Page not found.',
            ], 'error');
        } catch (\Throwable $ignored) {
            echo '<h1>404 Not Found</h1>';
            echo '<p>The requested page could not be found.</p>';
        }
    }

    // Normalizes paths like //projects/ to /projects
    protected static function normalizePath(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }
        return $path;
    }

    // Invokes a route handler (closure, [Controller, method], or "Controller@method")
    protected static function invokeHandler($handler): void
    {
        // Closure or callable array
        if (is_callable($handler)) {
            $handler();
            return;
        }

        // "Controller@method" style
        if (is_string($handler) && str_contains($handler, '@')) {
            [$controllerName, $method] = explode('@', $handler, 2);
            $controllerClass = str_contains($controllerName, '\\')
                ? $controllerName
                : 'App\\controllers\\' . $controllerName;

            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller class {$controllerClass} not found.");
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $method)) {
                throw new \RuntimeException("Method {$method} not found on controller {$controllerClass}.");
            }

            $controller->{$method}();
            return;
        }

        // [ControllerClass::class, 'method']
        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $method] = $handler;
            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller class {$controllerClass} not found.");
            }
            $controller = new $controllerClass();
            if (!method_exists($controller, $method)) {
                throw new \RuntimeException("Method {$method} not found on controller {$controllerClass}.");
            }
            $controller->{$method}();
            return;
        }

        throw new \RuntimeException('Invalid route handler.');
    }

    // Resolves middleware string shortcuts, e.g. "auth", "guest", "role:super_admin,director"
    protected static function resolveMiddlewareString(string $mw): callable
    {
        $mw = trim($mw);

        if ($mw === 'auth') {
            return Middleware::auth();
        }

        if ($mw === 'guest') {
            return Middleware::guest();
        }

        if (str_starts_with($mw, 'role:')) {
            $parts = explode(':', $mw, 2);
            $roles = [];
            if (!empty($parts[1])) {
                $roles = array_map('trim', explode(',', $parts[1]));
            }
            return Middleware::role($roles);
        }

        // Fallback no-op
        return function (): void {
            // no-op
        };
    }
}
