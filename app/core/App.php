<?php
// app/core/App.php
// App is the main entry point that boots the environment and dispatches HTTP requests.

namespace App\core;

class App
{
    // Bootstraps the application and handles the request lifecycle
    public static function run(): void
    {
        self::bootstrap();

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';
        $path   = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Load routes configuration
        $routesFile = dirname(__DIR__) . '/config/routes.php';
        if (file_exists($routesFile)) {
            require $routesFile;
        }

        try {
            Router::dispatch($method, $path);
        } catch (\Throwable $e) {
            self::handleException($e);
        }
    }

    // Loads environment, starts session, and configures basic settings
    protected static function bootstrap(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Load .env variables
        $envLoader = dirname(__DIR__) . '/config/env.php';
        if (file_exists($envLoader)) {
            require_once $envLoader;
        }

        // Load app config helpers if present
        $configFile = dirname(__DIR__) . '/config/config.php';
        if (file_exists($configFile)) {
            require_once $configFile;
        }

        // Basic error reporting based on APP_DEBUG
        $debug = $_ENV['APP_DEBUG'] ?? 'false';
        $isDebug = $debug === '1' || strtolower($debug) === 'true';
        if ($isDebug) {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', '0');
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
        }
    }

    // Handles unhandled exceptions and renders a basic error response
    protected static function handleException(\Throwable $e): void
    {
        $debug = $_ENV['APP_DEBUG'] ?? 'false';
        $isDebug = $debug === '1' || strtolower($debug) === 'true';

        http_response_code(500);

        if ($isDebug) {
            // Simple debug output
            echo '<h1>Application error</h1>';
            echo '<p><strong>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</strong></p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
            return;
        }

        // Production friendly message
        try {
            View::render('errors/500', [
                'message' => 'Something went wrong. Please try again later.',
            ], 'error');
        } catch (\Throwable $ignored) {
            echo '<h1>Something went wrong</h1>';
            echo '<p>Please try again later.</p>';
        }
    }
}
