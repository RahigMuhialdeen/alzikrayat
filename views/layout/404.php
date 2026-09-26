<?php

/**
 * 404 Page Not Found view for the Alzikrayat photo-sharing application.
 *
 * Renders the Presentation Layer response displayed when the application's
 * router cannot find a registered route matching the requested HTTP method
 * and URI. The view provides a clear error message and a navigation link
 * that allows the user to return to the application home page.
 *
 * This view is loaded by the application's routing/front-controller flow
 * when no matching route can be dispatched successfully.
 *
 * @package Alzikrayat\Views\Layout
 * @file 404.php
 * @category View / Presentation Layer
 * @see \Router::dispatch()
 */

require __DIR__ . '/header.php';
?>

<!-- 404 HTTP Error Presentation Component -->
<div class="text-center py-5">
    <div class="display-1 fw-bold">404</div>

    <h1>Page Not Found</h1>

    <p class="text-muted">
        The requested page does not exist.
    </p>

    <a class="btn btn-primary" href="<?= url('/') ?>">
        Back Home
    </a>
</div>

<?php require __DIR__ . '/footer.php'; ?>