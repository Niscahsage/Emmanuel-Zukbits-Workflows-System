<?php
// public/index.php
// index.php is the single entry point for all HTTP requests into the ZukBits Workflows System.

declare(strict_types=1);

// Project root
$root = dirname(__DIR__);

// Load app config helpers (app_config, etc.) early
$configFile = $root . '/app/config/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
}

// Load Composer autoloader if available
$vendorAutoload = $root . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

// Simple PSR-4 autoload for the App\ namespace (fallback / in addition to Composer)
spl_autoload_register(function (string $class): void {
    $prefix  = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\core\App;

// Run the application
App::run();
