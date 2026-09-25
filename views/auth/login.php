<?php

/**
 * Alzikrayat - Photo Sharing Web Application
 * 
 * @package   Alzikrayat\Views\Auth
 * @file      login.php
 * @category  View / Presentation Layer
 * @see       \App\Controllers\AuthController::showLogin()
 * @see       \App\Controllers\AuthController::login()
 * 
 * Description:
 * Renders the user authentication login form interface. Displays the 7-day last login 
 * context timestamp retrieved from session state or persistent HTTP cookies, and 
 * provides client-side validation triggers.
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
                 * Last Login Context Logic Block
                 * 
                 * Evaluates and renders the timestamp of the previous successful login session.
                 * Checks flash session array first, then falls back to persistent HTTP cookie storage.
                 */
                // Show the previous successful login timestamp once, then clear the session copy.
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

                <!-- User Authentication HTML Form Component -->
                <form method="post" action="<?= url('/login') ?>" class="needs-validation" novalidate>

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
                        <div class="invalid-feedback">Enter a valid email.</div>
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
                        <div class="invalid-feedback">Password is required.</div>
                    </div>

                    <!-- Form Submission Control -->
                    <button class="btn btn-primary w-100" type="submit">Login</button>
                </form>

                <p class="text-center mt-4 mb-0">
                    New here? <a href="<?= url('/register') ?>">Create an account</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>