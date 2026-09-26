<?php

/**
 * Registration view for the Alzikrayat photo-sharing application.
 *
 * Renders the account registration form used by new users to create
 * an account. The view restores previously submitted form values from
 * the PHP session when registration validation fails and escapes those
 * values before displaying them in the HTML form.
 *
 * The form also provides HTML5 client-side validation constraints for
 * required fields, maximum lengths, email format, password length,
 * and alphabetic first and last names.
 *
 * This view belongs to the Presentation Layer and is responsible for
 * rendering the registration interface and preserving user input state.
 *
 * @package Alzikrayat\Views\Auth
 * @file register.php
 * @category View / Presentation Layer
 * @see \AuthController::showRegister()
 * @see \AuthController::register()
 */

/**
 * Retrieves previously submitted registration values from the session.
 *
 * The registration controller stores the submitted values in the session
 * when validation fails so that the user does not have to enter the data
 * again. The values are extracted into the local $old variable and then
 * immediately removed from the session, following the flash-data pattern.
 *
 * @var array<string, mixed> $old Previously submitted registration values.
 */
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

require __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 fw-bold mb-2">Create Your Account</h1>
                <p class="text-muted mb-4">Join Alzikrayat and start sharing photos.</p>

                <!--
                    Registration form:
                    Sends the submitted account information to the registration route.
                    HTML5 validation is enabled while the application's JavaScript
                    provides additional client-side validation.
                -->
                <form method="post" action="<?= url('/register') ?>" class="needs-validation" novalidate>

                    <div class="row">
                        <!-- First Name Field -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="first_name">First Name</label>
                            <input
                                class="form-control"
                                id="first_name"
                                name="first_name"
                                maxlength="50"
                                pattern="[A-Za-z]+"
                                value="<?= e($old['first_name'] ?? '') ?>"
                                required>
                            <div class="invalid-feedback">
                                Letters only, maximum 50 characters.
                            </div>
                        </div>

                        <!-- Last Name Field -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="last_name">Last Name</label>
                            <input
                                class="form-control"
                                id="last_name"
                                name="last_name"
                                maxlength="50"
                                pattern="[A-Za-z]+"
                                value="<?= e($old['last_name'] ?? '') ?>"
                                required>
                            <div class="invalid-feedback">
                                Letters only, maximum 50 characters.
                            </div>
                        </div>
                    </div>

                    <!-- Email Address Field -->
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input
                            class="form-control"
                            type="email"
                            id="email"
                            name="email"
                            maxlength="100"
                            value="<?= e($old['email'] ?? '') ?>"
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
                            autocomplete="new-password"
                            required>
                        <div class="form-text">
                            At least 8 characters.
                        </div>
                        <div class="invalid-feedback">
                            Password must be at least 8 characters.
                        </div>
                    </div>

                    <!-- Location & Occupation Fields -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="location">Location</label>
                            <input
                                class="form-control"
                                id="location"
                                name="location"
                                maxlength="100"
                                value="<?= e($old['location'] ?? '') ?>">
                            <div class="invalid-feedback">
                                Maximum 100 characters.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="occupation">Occupation</label>
                            <input
                                class="form-control"
                                id="occupation"
                                name="occupation"
                                maxlength="100"
                                value="<?= e($old['occupation'] ?? '') ?>">
                            <div class="invalid-feedback">
                                Maximum 100 characters.
                            </div>
                        </div>
                    </div>

                    <!-- Description Textarea Field -->
                    <div class="mb-4">
                        <label class="form-label" for="description">Description</label>
                        <textarea
                            class="form-control"
                            id="description"
                            name="description"
                            rows="4"
                            maxlength="5000"><?= e($old['description'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            Maximum 5000 characters.
                        </div>
                    </div>

                    <!-- Navigation and Action Controls -->
                    <button class="btn btn-primary" type="submit">
                        Register
                    </button>

                    <a
                        class="btn btn-outline-secondary ms-2"
                        href="<?= url('/login') ?>">
                        Already have an account?
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>