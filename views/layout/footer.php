<?php

/**
 * Global footer layout view for the Alzikrayat photo-sharing application.
 *
 * Renders the common footer shared by the application's presentation
 * pages. The footer closes the main semantic content container, displays
 * the application's copyright information, and loads the client-side
 * JavaScript resources required by the application.
 *
 * The Bootstrap JavaScript bundle is loaded from the configured CDN
 * and the application's custom JavaScript file is loaded from the
 * public assets directory using the application's URL helper.
 *
 * This view is included by individual presentation pages after their
 * page-specific content has been rendered.
 *
 * @package Alzikrayat\Views\Layout
 * @file footer.php
 * @category View / Presentation Layer
 */
?>
</main>

<!-- Global Application Footer Component -->
<footer class="site-footer mt-5">
    <div class="container py-4 text-center">
        <h5 class="mb-2">Alzikrayat — Photo Sharing Application</h5>
        <p class="mb-0">
            © 2026 Alzikrayat. All rights reserved.
        </p>
    </div>
</footer>

<!--
    Local Bootstrap 5 JavaScript bundle.
    Includes Popper and allows Bootstrap interactive
    components to work without requiring an internet connection.
-->
<script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>

<!--
    Application-specific client-side JavaScript.
    This file contains the custom validation and interaction logic
    used by the application's presentation layer.
-->
<script src="<?= url('/assets/js/app.js') ?>"></script>

</body>

</html>