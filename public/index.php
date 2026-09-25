<?php

/**
 * Alzikrayat - Photo Sharing Web Application
 * 
 * Front Controller Entry Point
 * Architecture: Manual 3-Tier MVC
 * 
 * This file serves as the single entry point for all HTTP requests.
 * It initializes application configuration, loads core classes/models/controllers,
 * sets up helper functions, registers application routes, and dispatches requests.
 */

declare(strict_types=1);

// Start native PHP session management for authentication state tracking
session_start();

// Load Core System Configuration & Database Layer
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Router.php';

// Load Data Models (Data Tier)
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Photo.php';
require_once __DIR__ . '/../models/Comment.php';

// Load Controllers (Application/Business Logic Tier)
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/PhotoController.php';
require_once __DIR__ . '/../controllers/CommentController.php';

/**
 * Dynamic URL Generator Helper
 * 
 * Calculates absolute relative paths based on the server script environment
 * to ensure seamless functionality across local XAMPP subfolders.
 * 
 * @param string $path Target relative route/asset path
 * @return string Sanitized relative URL
 */
function url(string $path = '/'): string
{
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
    if ($path === '' || $path === '/') return ($base ?: '') . '/';
    return ($base ?: '') . '/' . ltrim($path, '/');
}

/**
 * XSS Prevention Output Sanitization Helper
 * 
 * Escapes HTML entities using UTF-8 encoding to mitigate Cross-Site Scripting (XSS).
 * 
 * @param string|null $value Raw input text
 * @return string XSS-safe escaped text
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Asset URL Generator Helper
 * 
 * @param string $path Path relative to public/assets/
 * @return string Full asset URL
 */
function asset(string $path): string
{
    return url('/assets/' . ltrim($path, '/'));
}

// Initialize Custom Regex Router and Register Application Endpoints
$router = new Router();

// Public & Static Routes
$router->add('GET', '/', ['PhotoController', 'home']);
$router->add('GET', '/gallery', ['PhotoController', 'index']);
$router->add('GET', '/about', ['PhotoController', 'about']);

// Authentication Routes
$router->add('GET', '/login', ['AuthController', 'loginForm']);
$router->add('POST', '/login', ['AuthController', 'login']);
$router->add('GET', '/register', ['AuthController', 'registerForm']);
$router->add('POST', '/register', ['AuthController', 'register']);
$router->add('GET', '/logout', ['AuthController', 'logout']);

// Authenticated Photo Operations (CRUD & Details)
$router->add('GET', '/photo/{id}', ['PhotoController', 'show']);
$router->add('GET', '/upload', ['PhotoController', 'create']);
$router->add('POST', '/photo/store', ['PhotoController', 'store']);
$router->add('POST', '/photo/{id}/delete', ['PhotoController', 'delete']);

// Authenticated Comment Operation
$router->add('POST', '/photo/{id}/comment', ['CommentController', 'store']);

// Global Exception & Error Handling Dispatch Block
try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (PDOException $e) {
    // Handle Database Connection Failures Gracefully
    http_response_code(500);
    echo '<h1>Database Connection Error</h1><p>Make sure MySQL is running and the database "alzikrayat" has been imported.</p>';
} catch (Throwable $e) {
    // Catch-all Internal Application Errors with XSS Protection
    http_response_code(500);
    echo '<h1>Application Error</h1><p>' . e($e->getMessage()) . '</p>';
}
