<?php
// Entry point của ứng dụng
session_start();

// Load environment configuration
require_once __DIR__ . '/../env.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/core/' . $class . '.php',
        __DIR__ . '/../app/controllers/' . $class . '.php',
        __DIR__ . '/../app/models/' . $class . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            break;
        }
    }
});

// Get URL from query string
$url = $_GET['url'] ?? '';

// Remove trailing slash
$url = rtrim($url, '/');

// Load routes
$routes = require __DIR__ . '/../configs/routes.php';

// Find matching route
$found = false;
foreach ($routes as $route => $handler) {
    if ($url === $route || ($route === '' && $url === '')) {
        $found = true;
        [$controllerName, $method] = $handler;
        
        // Load controller
        $controllerPath = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $controller = new $controllerName();
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                http_response_code(404);
                echo "Method not found: {$method}";
            }
        } else {
            http_response_code(404);
            echo "Controller not found: {$controllerName}";
        }
        break;
    }
}

// 404 if no route found
if (!$found) {
    http_response_code(404);
    echo "Page not found";
}

