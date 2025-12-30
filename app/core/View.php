<?php
// app/core/View.php
// View handles rendering of PHP templates and layouts.

namespace App\core;

class View
{
    // Renders a view within a layout
    public static function render(string $view, array $params = [], string $layout = 'main'): void
    {
        $basePath   = dirname(__DIR__) . '/views';
        $viewFile   = $basePath . '/' . ltrim($view, '/') . '.php';
        $layoutFile = $basePath . '/layouts/' . $layout . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file not found: {$viewFile}");
        }

        extract($params, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    // Escapes a string for HTML output
    public static function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
