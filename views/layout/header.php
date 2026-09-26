<?php

/**
 * Global header layout view for the Alzikrayat photo-sharing application.
 *
 * Renders the common HTML document header and navigation interface shared
 * by the application's presentation-layer views.
 *
 * This view initializes the HTML document metadata, applies the current
 * page title, loads Bootstrap 5 and the application's custom stylesheet,
 * and renders the global navigation bar.
 *
 * The navigation state is authentication-aware:
 * - Authenticated users see their display name, the Upload link, and Logout.
 * - Unauthenticated users see the Please Login message and Login link.
 *
 * The view also opens the main page content container and displays any
 * flash message or validation errors stored in the current session.
 * Session feedback is consumed after being rendered so that the same
 * message is not displayed repeatedly on subsequent requests.
 *
 * @package Alzikrayat\Views\Layout
 * @file header.php
 * @category View / Presentation Layer
 */

$pageTitle = $title ?? 'Alzikrayat';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Alzikrayat - an MVC-based photo sharing web application.">
    <title><?= e($pageTitle) ?></title>

    <!--
        Local Bootstrap 5 CSS framework.
        Provides the responsive layout system and reusable UI components
        used throughout the application's presentation layer without requiring an internet connection.
    -->
    <link href="<?= asset('css/bootstrap.min.css') ?>" rel="stylesheet">

    <!--
        Application-specific stylesheet.
        Contains the custom visual design and responsive presentation
        rules used in addition to the Bootstrap framework.
    -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>

<body>

    <!--
        Sticky Global Application Navigation Header.
        Provides the primary navigation links and authentication-aware
        controls available throughout the application.
    -->
    <nav class="navbar navbar-expand-lg navbar-dark site-nav sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= url('/') ?>">Alzikrayat</a>

            <!--
                Responsive navigation toggle.
                Bootstrap uses this button to collapse and expand the
                navigation links on smaller viewport sizes.
            -->
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">

                <!-- Primary Application Navigation Links -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/') ?>">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/gallery') ?>">Gallery</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/about') ?>">About Us</a>
                    </li>

                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('/upload') ?>">Upload</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <!--
                    Dynamic Authentication State Controls.
                    The controls displayed here depend on whether a valid
                    authenticated user identifier exists in the session.
                -->
                <div class="d-flex align-items-center gap-2">
                    <?php if (!empty($_SESSION['user_id'])): ?>

                        <!-- Authenticated User Information -->
                        <span class="text-white small">
                            Hi <?= e($_SESSION['user_name'] ?? '') ?>
                        </span>

                        <!-- Logout Navigation Control -->
                        <a class="btn btn-light btn-sm" href="<?= url('/logout') ?>">
                            Logout
                        </a>

                    <?php else: ?>

                        <!-- Unauthenticated User Information -->
                        <span class="text-white small">Please Login</span>

                        <!-- Login Navigation Control -->
                        <a class="btn btn-light btn-sm" href="<?= url('/login') ?>">
                            Login
                        </a>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!--
        Main Layout Body Container.
        Opens the primary semantic content area used by individual
        presentation views and provides a consistent Bootstrap container
        and vertical spacing across application pages.
    -->
    <main class="container py-4">

        <?php
        /**
         * Flash Feedback Message Processing.
         *
         * Retrieves a single temporary feedback message from the session,
         * renders it as a Bootstrap alert, and removes it from the session
         * immediately after retrieval so that it is displayed only once.
         */
        if (!empty($_SESSION['flash'])):
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        ?>
            <div
                class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show"
                role="alert">
                <?= e($flash['message']) ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php
        /**
         * Validation Error Feedback Processing.
         *
         * Retrieves the collection of validation error messages stored
         * in the session, renders each message as a list item inside
         * a Bootstrap danger alert, and removes the errors from the
         * session after retrieval to prevent repeated display.
         */
        if (!empty($_SESSION['errors'])):
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>