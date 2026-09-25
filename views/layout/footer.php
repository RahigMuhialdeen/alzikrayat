<?php

/**
 * Alzikrayat - Photo Sharing Web Application
 * 
 * @package   Alzikrayat\Views\Layout
 * @file      footer.php
 * @category  View / Presentation Layer
 * @see       \App\Controllers\HomeController
 * 
 * Description:
 * Renders the global layout footer for the application. Closes the primary semantic 
 * HTML container element, presents copyright information, and loads required client-side 
 * JavaScript assets including Bootstrap 5 framework bundles and custom app interactions.
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

<!-- External Bootstrap 5 JavaScript Bundle (Includes Popper for Dropdowns and Modals) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Application Custom Client-Side Script Asset -->
<script src="<?= url('/assets/js/app.js') ?>"></script>

</body>

</html>