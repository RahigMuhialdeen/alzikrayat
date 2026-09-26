<?php

/**
 * Project overview and architecture presentation view for the
 * Alzikrayat photo-sharing application.
 *
 * Renders the About Us page used to present the application's purpose,
 * three-tier MVC architecture, technology stack, and principal
 * application features for the Advanced Web Technologies course.
 *
 * The view describes the separation between the Presentation,
 * Application/Business, and Data tiers and summarizes the major
 * functionality implemented by the application.
 *
 * @package Alzikrayat\Views
 * @file about.php
 * @category View / Presentation Layer
 *
 * @see \PhotoController::about()
 */

require __DIR__ . '/layout/header.php';
?>

<!--
    Project Overview and Architecture Presentation Section.
    Provides an academic overview of the application and explains
    its three-tier MVC architecture and major implemented features.
-->
<section class="about-panel rounded-4 p-4 p-md-5">

    <h1 class="fw-bold">About Us</h1>

    <p>
        Alzikrayat is a photo-sharing web application created for the
        Advanced Web Technologies course. It demonstrates how a dynamic
        web application can be built from foundational PHP and MySQL
        components without a backend framework.
    </p>

    <!--
        Architectural Tiers Overview.
        Summarizes the responsibilities and technologies used in
        each layer of the application's three-tier architecture.
    -->
    <h2 class="h4 mt-4">Project Architecture</h2>

    <ul>
        <li>
            <strong>Presentation Tier:</strong>
            HTML5, Bootstrap, views and client-side JavaScript validation.
        </li>

        <li>
            <strong>Application / Business Tier:</strong>
            Controllers, custom regex Router, request handling and
            server-side validation.
        </li>

        <li>
            <strong>Data Tier:</strong>
            Models, PDO database connection and handwritten SQL queries.
        </li>
    </ul>

    <!--
        Core Application Capabilities.
        Presents the main functional features implemented in the
        application for academic demonstration.
    -->
    <h2 class="h4 mt-4">Main Features</h2>

    <p class="mb-0">
        Registration, secure login sessions, seven-day last-login cookie,
        responsive home page, multiple gallery layouts, physical image
        uploads, ownership-protected deletion, photo details and comments.
    </p>

</section>

<?php require __DIR__ . '/layout/footer.php'; ?>