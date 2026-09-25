<?php

/**
 * Alzikrayat - Photo Sharing Web Application
 * 
 * @package   Alzikrayat\Views\Layout
 * @file      header.php
 * @category  View / Presentation Layer
 * @see       \App\Controllers\HomeController
 * 
 * Description:
 * Renders the global layout header for the application. Sets up HTML head metadata, 
 * page titles, external CSS stylesheets (Bootstrap 5 & custom assets), sticky navigation bar 
 * with authentication-aware state controls, and flash alert feedback notifications.
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
    <!-- External Bootstrap 5 CSS Component Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Application CSS Stylesheet -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>

<body>

    <!-- Sticky Global Application Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-dark site-nav sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= url('/') ?>">Alzikrayat</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <!-- Navigation Links -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?= url('/') ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/gallery') ?>">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/about') ?>">About Us</a></li>
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= url('/upload') ?>">Upload</a></li>
                    <?php endif; ?>
                </ul>

                <!-- Dynamic Authentication State Controls -->
                <div class="d-flex align-items-center gap-2">
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <span class="text-white small">Hi <?= e($_SESSION['user_name'] ?? '') ?></span>
                        <a class="btn btn-light btn-sm" href="<?= url('/logout') ?>">Logout</a>
                    <?php else: ?>
                        <span class="text-white small">Please Login</span>
                        <a class="btn btn-light btn-sm" href="<?= url('/login') ?>">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Layout Body Container & Flash Feedback Notifications -->
    <main class="container py-4">
        <?php
        /**
         * Single Flash Feedback Message Processing
         * Renders and flushes single feedback alerts stored in session memory.
         */
        if (!empty($_SESSION['flash'])): $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php
        /**
         * Multi-Error Validation Feedback List
         * Renders and flushes validation error lists captured during form submissions.
         */
        if (!empty($_SESSION['errors'])): $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>