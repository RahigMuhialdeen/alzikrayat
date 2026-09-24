<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Photo.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/PhotoController.php';
require_once __DIR__ . '/../controllers/CommentController.php';

function url(string $path = '/'): string
{
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
    if ($path === '' || $path === '/') return ($base ?: '') . '/';
    return ($base ?: '') . '/' . ltrim($path, '/');
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function asset(string $path): string
{
    return url('/assets/' . ltrim($path, '/'));
}

$router = new Router();
$router->add('GET', '/', ['PhotoController', 'home']);
$router->add('GET', '/login', ['AuthController', 'loginForm']);
$router->add('POST', '/login', ['AuthController', 'login']);
$router->add('GET', '/register', ['AuthController', 'registerForm']);
$router->add('POST', '/register', ['AuthController', 'register']);
$router->add('GET', '/logout', ['AuthController', 'logout']);
$router->add('GET', '/gallery', ['PhotoController', 'index']);
$router->add('GET', '/photo/{id}', ['PhotoController', 'show']);
$router->add('GET', '/upload', ['PhotoController', 'create']);
$router->add('POST', '/photo/store', ['PhotoController', 'store']);
$router->add('POST', '/photo/{id}/delete', ['PhotoController', 'delete']);
$router->add('POST', '/photo/{id}/comment', ['CommentController', 'store']);
$router->add('GET', '/about', ['PhotoController', 'about']);

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (PDOException $e) {
    http_response_code(500);
    echo '<h1>Database Connection Error</h1><p>Make sure MySQL is running and the database "alzikrayat" has been imported.</p>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Application Error</h1><p>' . e($e->getMessage()) . '</p>';
}
