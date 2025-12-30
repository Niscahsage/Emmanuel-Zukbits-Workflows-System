<?php
// app/config/env.php
// Simple .env loader for the ZukBits Workflows System.
// Loads key=value pairs from the project root .env into $_ENV / $_SERVER.

function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $name  = trim($parts[0]);
        $value = trim($parts[1]);

        if ($value !== '' && $value[0] === '"' && str_ends_with($value, '"')) {
            $value = substr($value, 1, -1);
        }

        $_ENV[$name]    = $value;
        $_SERVER[$name] = $value;
        putenv($name . '=' . $value);
    }
}

// Load from project root .env
$envPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
loadEnv($envPath);
