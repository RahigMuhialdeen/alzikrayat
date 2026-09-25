<?php

/**
 * Alzikrayat - Photo Sharing Web Application
 * 
 * @package   Alzikrayat\Views\Layout
 * @file      404.php
 * @category  View / Presentation Layer
 * @see       \App\Core\Router::dispatch()
 * 
 * Description:
 * Renders the 404 Page Not Found error presentation view. Executed by the application
 * Front Controller router when an incoming HTTP request URI does not match any registered system routes.
 */

require __DIR__ . '/header.php';
?>

<!-- 404 HTTP Error Presentation Component -->
<div class="text-center py-5">
    <div class="display-1 fw-bold">404</div>
    <h1>Page Not Found</h1>
    <p class="text-muted">The requested page does not exist.</p>
    <a class="btn btn-primary" href="<?= url('/') ?>">Back Home</a>
</div>

<?php require __DIR__ . '/footer.php'; ?>