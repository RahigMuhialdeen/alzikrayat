<?php

/**
 * Login view for the Alzikrayat photo-sharing application.
 *
 * Renders the authentication form used by unauthenticated users to
 * submit their email address and password. The view also displays
 * the user's previous successful login timestamp when it is available
 * through the session or the persistent last-login cookie.
 *
 * This view belongs to the Presentation Layer and is responsible only
 * for rendering HTML and presenting authentication-related information.
 *
 * @package Alzikrayat\Views\Auth
 * @file login.php
 * @category View / Presentation Layer
 * @see \AuthController::loginForm() 
 * @see \AuthController::login()
 */

require __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 fw-bold mb-2">Welcome Back</h1>
                <p class="text-muted mb-4">Login to continue sharing your memories.</p>

                <?php
                /**
                 * Displays the previous successful login timestamp.
                 *
                 * The session value is checked first because it represents
                 * the most recently stored login context. When available,
                 * it is displayed once and then removed from the session.
                 *
                 * If no session value exists, the view falls back to the
                 * persistent last-login cookie stored on the user's computer.
                 */

                // Display the session-based timestamp once, then clear it.
                if (!empty($_SESSION['last_login_context'])):
                ?>
                    <div class="alert alert-info small">
                        Last login from this computer was
                        <strong><?= e($_SESSION['last_login_context']) ?></strong>.
                    </div>
                    <?php unset($_SESSION['last_login_context']); ?>

                <?php elseif (!empty($_COOKIE['alzikrayat_last_login'])): ?>

                    <div class="alert alert-info small">
                        Last login from this computer was
                        <strong><?= e($_COOKIE['alzikrayat_last_login']) ?></strong>.
                    </div>

                <?php endif; ?>

                <!--
                    Authentication form:
                    Submits the user's credentials to the login route.
                    HTML5 validation is enabled while JavaScript validation
                    provides the application's additional client-side checks.
                -->
                <form method="post" action="<?= url('/login') ?>" class="needs-validation" novalidate>

                    <!--
                        Authentication token field.
                        The value is escaped before being rendered into the HTML.
                    -->
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($csrf_token ?? '') ?>">

                    <!-- Email Field -->
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>

                        <input
                            class="form-control"
                            type="email"
                            id="email"
                            name="email"
                            maxlength="100"
                            autocomplete="email"
                            required>

                        <div class="invalid-feedback">
                            Enter a valid email.
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>

                        <input
                            class="form-control"
                            type="password"
                            id="password"
                            name="password"
                            minlength="8"
                            autocomplete="current-password"
                            required>

                        <div class="invalid-feedback">
                            Password is required.
                        </div>
                    </div>

                    <!-- Form Submission Control -->
                    <button class="btn btn-primary w-100" type="submit">
                        Login
                    </button>
                </form>

                <!-- Registration Navigation -->
                <p class="text-center mt-4 mb-0">
                    New here?
                    <a href="<?= url('/register') ?>">Create an account</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>