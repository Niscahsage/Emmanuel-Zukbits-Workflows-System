<?php
// app/config/config.php
// Central configuration helpers for the ZukBits Workflows System.

require_once __DIR__ . '/env.php';

/**
 * Read an environment variable with a default fallback.
 */
function env(string $key, mixed $default = null): mixed
{
    if (array_key_exists($key, $_ENV)) {
        return $_ENV[$key];
    }
    if (array_key_exists($key, $_SERVER)) {
        return $_SERVER[$key];
    }
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }
    return $default;
}

/**
 * Application-level configuration.
 */
function app_config(): array
{
    return [
        'env'   => env('APP_ENV', 'local'),
        'debug' => env('APP_DEBUG', 'false') === 'true',
        'url'   => env('APP_URL', 'http://localhost'),
        'name'  => env('APP_NAME', 'ZukBits Workflows System'),
    ];
}

/**
 * Database configuration (PostgreSQL on Supabase in dev).
 */
function db_config(): array
{
    return [
        'driver'    => 'pgsql',
        'host'      => env('DB_HOST', 'localhost'),
        'port'      => (int) env('DB_PORT', 5432),
        'database'  => env('DB_NAME', 'postgres'),
        'username'  => env('DB_USER', 'postgres'),
        'password'  => env('DB_PASSWORD', ''),
        'schema'    => env('DB_SCHEMA', 'public'),
        'sslmode'   => env('DB_SSLMODE', 'prefer'),
        'dsn_url'   => env('DATABASE_URL'), // optional full URL if needed
    ];
}

/**
 * Security-related configuration: sessions, CSRF, encryption, auth cookies.
 */
function security_config(): array
{
    return [
        'session_secret'   => env('SESSION_SECRET'),
        'session_name'     => env('SESSION_NAME', 'workflows'),
        'cookie_secure'    => env('COOKIE_SECURE', 'false') === 'true',
        'cookie_samesite'  => env('COOKIE_SAMESITE', 'Strict'),
        'csrf_secret'      => env('CSRF_SECRET'),
        'encryption_key'   => env('ENCRYPTION_KEY'),
        'auth_secret'      => env('AUTH_SECRET'),
        'auth_cookie_name' => env('AUTH_COOKIE_NAME', 'workflows_auth'),
    ];
}

/**
 * Branding / contact information for the system owner.
 */
function branding_config(): array
{
    return [
        'owner_name'     => env('OWNER_NAME', 'Zukbits Online'),
        'owner_email'    => env('OWNER_EMAIL', 'info@zukbitsonline.co.ke'),
        'owner_phone'    => env('OWNER_PHONE', '+254000000000'),
        'owner_location' => env('OWNER_LOCATION', 'Nairobi, Kenya'),
    ];
}

/**
 * Supabase / storage configuration.
 */
function storage_config(): array
{
    return [
        'supabase_url'           => env('SUPABASE_PROJECT_URL'),
        'supabase_service_key'   => env('SUPABASE_SERVICE_ROLE_KEY'),
        'supabase_anon_key'      => env('SUPABASE_ANON_KEY'),
        'storage_bucket'         => env('SUPABASE_STORAGE_BUCKET', 'workflows'),
        'signed_url_ttl'         => (int) env('SUPABASE_SIGNED_URL_TTL', 3600),
        'skip_tls_verify'        => env('SUPABASE_SKIP_TLS_VERIFY', 'false') === 'true',
        'curl_ca_bundle'         => env('CURL_CA_BUNDLE'),
        'supabase_ca_file'       => env('SUPABASE_CA_FILE'),
        'max_upload_mb'          => (int) env('MAX_UPLOAD_MB', 5),
        'allowed_image_ext'      => array_filter(array_map('trim', explode(',', env('ALLOWED_IMAGE_EXT', 'jpg,jpeg,png,webp,gif,avif')))),
        'allowed_html_ext'       => array_filter(array_map('trim', explode(',', env('ALLOWED_HTML_EXT', 'html')))),
    ];
}

/**
 * Rate limiting configuration.
 */
function rate_limit_config(): array
{
    return [
        'window_seconds' => (int) env('RATE_LIMIT_WINDOW', 3600),
        'max_requests'   => (int) env('RATE_LIMIT_MAX', 300),
    ];
}

/**
 * SEO and CSP configuration flags.
 */
function csp_config(): array
{
    return [
        'robots_index'         => env('ROBOTS_INDEX', 'true') === 'true',
        'allow_supabase_image' => env('CSP_ALLOW_SUPABASE_IMG', 'true') === 'true',
    ];
}

/**
 * Mail (SMTP) configuration for password resets and notifications.
 */
function mail_config(): array
{
    return [
        'driver'      => env('MAIL_DRIVER', 'smtp'),
        'host'        => env('MAIL_HOST'),
        'port'        => (int) env('MAIL_PORT', 587),
        'username'    => env('MAIL_USERNAME'),
        'password'    => env('MAIL_PASSWORD'),
        'encryption'  => env('MAIL_ENCRYPTION', 'tls'),
        'from_email'  => env('MAIL_FROM_ADDRESS', 'info@zukbitsonline.co.ke'),
        'from_name'   => env('MAIL_FROM_NAME', 'Zukbits Workflows'),
    ];
}
