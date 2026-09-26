<?php

/**
 * Alzikrayat - Photo Sharing Web Application
 *
 * Front Controller Entry Point
 * Architecture: Manual 3-Tier MVC
 *
 * This file is the single HTTP entry point for the Alzikrayat application.
 * It initializes the PHP session, loads the configuration, database layer,
 * core MVC components, data models, and application controllers.
 *
 * It also defines shared URL, asset, and HTML-escaping helper functions,
 * registers all application routes with the custom regular-expression router,
 * and dispatches the current HTTP request to the appropriate controller action.
 *
 * Database and application exceptions are handled at the outermost level so
 * that unexpected failures result in an HTTP 500 response rather than exposing
 * an uncontrolled PHP error page.
 */

declare(strict_types=1);

/*
 * Start native PHP session management so authentication state and
 * temporary session messages can be accessed by controllers and views.
 */
session_start();

/*
 * Load the core configuration and MVC infrastructure.
 * These files must be loaded before models and controllers because
 * the latter depend on the shared Model, Controller, and Router classes.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Router.php';

/*
 * Load Data Tier model classes used by the application controllers.
 */
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Photo.php';
require_once __DIR__ . '/../models/Comment.php';

/*
 * Load Application/Business Logic Tier controllers responsible
 * for authentication, photo operations, and comments.
 */
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/PhotoController.php';
require_once __DIR__ . '/../controllers/CommentController.php';

/**
 * Generates an application-relative URL for a route or resource.
 *
 * The helper determines the directory in which the front controller is
 * running and prefixes that directory to the supplied path. This allows
 * the application to operate correctly when installed inside an XAMPP
 * subdirectory instead of directly at the web server document root.
 *
 * A root path is normalized to the application's base URL followed by
 * a trailing slash. Non-root paths are normalized so that they contain
 * exactly one separator between the application base and the supplied path.
 *
 * @param string $path Target application route or relative path.
 *                     Defaults to the application root.
 * @return string Application-relative URL suitable for use in HTML links
 *                and form actions.
 */
function url(string $path = '/'): string
{
    /*
     * Determine the directory containing the currently executing
     * front controller so URLs continue to work inside an XAMPP subfolder.
     */
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');

    /*
     * Normalize requests for the application root and preserve
     * the expected trailing slash.
     */
    if ($path === '' || $path === '/') {
        return ($base ?: '') . '/';
    }

    /*
     * Remove any leading slash from the supplied path before joining it
     * with the application's base directory.
     */
    return ($base ?: '') . '/' . ltrim($path, '/');
}

/**
 * Escapes text before rendering it inside an HTML document.
 *
 * This helper converts special HTML characters to entities using UTF-8
 * encoding and ENT_QUOTES, preventing untrusted values from being interpreted
 * as HTML or JavaScript when displayed by application views.
 *
 * The helper is intended for output escaping and does not modify or sanitize
 * the original value stored in the database.
 *
 * @param string|null $value Raw text value that will be rendered in HTML.
 *                           Null values are treated as an empty string.
 * @return string HTML-escaped UTF-8 string safe for normal HTML output.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generates a URL for a static application asset.
 *
 * The helper prefixes the supplied asset path with the public assets
 * directory and delegates URL construction to the shared url() helper.
 * This keeps asset references consistent when the application is hosted
 * inside an XAMPP subdirectory.
 *
 * @param string $path Path relative to the public/assets directory.
 * @return string Application-relative URL pointing to the requested asset.
 */
function asset(string $path): string
{
    /*
     * Normalize the supplied asset path and pass it through the
     * application's central URL generator.
     */
    return url('/assets/' . ltrim($path, '/'));
}

/*
 * Create the application's custom router.
 *
 * The Router class performs manual HTTP method and regular-expression
 * route matching without relying on a backend framework.
 */
$router = new Router();

/*
 * Register public pages and static application endpoints.
 */
$router->add('GET', '/', ['PhotoController', 'home']);
$router->add('GET', '/gallery', ['PhotoController', 'index']);
$router->add('GET', '/about', ['PhotoController', 'about']);

/*
 * Register authentication endpoints.
 *
 * GET requests display the corresponding forms, while POST requests
 * submit credentials or registration data for processing.
 */
$router->add('GET', '/login', ['AuthController', 'loginForm']);
$router->add('POST', '/login', ['AuthController', 'login']);
$router->add('GET', '/register', ['AuthController', 'registerForm']);
$router->add('POST', '/register', ['AuthController', 'register']);
$router->add('GET', '/logout', ['AuthController', 'logout']);

/*
 * Register photo operations.
 *
 * The {id} and {filter} placeholders are interpreted by the custom
 * Router as dynamic route parameters and passed to the controller action.
 */
$router->add('GET', '/photo/{id}', ['PhotoController', 'show']);
$router->add('GET', '/photo/{id}/filter/{filter}', ['PhotoController', 'filter']);
$router->add('GET', '/upload', ['PhotoController', 'create']);
$router->add('POST', '/photo/store', ['PhotoController', 'store']);
$router->add('POST', '/photo/{id}/delete', ['PhotoController', 'delete']);

/*
 * Register the authenticated comment submission endpoint.
 * The photo ID is supplied dynamically through the route.
 */
$router->add('POST', '/photo/{id}/comment', ['CommentController', 'store']);

/*
 * Dispatch the current HTTP request through the manual router.
 *
 * Database exceptions are handled separately because they usually indicate
 * a database availability or connection problem. Other Throwable instances
 * are handled by the general application error handler.
 */
try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (PDOException $e) {
    /*
     * Return HTTP 500 for database connection or database availability
     * failures while providing the developer with the required setup hint.
     */
    http_response_code(500);
    echo '<h1>Database Connection Error</h1><p>Make sure MySQL is running and the database "alzikrayat" has been imported.</p>';
} catch (Throwable $e) {
    /*
     * Catch unexpected application failures and escape the exception message
     * before placing it into HTML output to prevent an XSS response.
     */
    http_response_code(500);
    echo '<h1>Application Error</h1><p>' . e($e->getMessage()) . '</p>';
}
