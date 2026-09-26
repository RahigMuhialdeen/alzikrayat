<?php

/**
 * Alzikrayat - Photo Sharing Web Application
 * 
 * @package   Alzikrayat\Views
 * @file      home.php
 * @category  View / Presentation Layer
 * @see       \App\Controllers\HomeController::index()
 * 
 * @var array $photos       List of latest photo records fetched from database.
 * @var int   $userCount    Total number of registered active users.
 * @var int   $photoCount   Total count of uploaded photos across gallery.
 * @var int   $commentCount Total count of comments posted.
 * 
 * Description:
 * Renders the primary landing page interface. Displays the hero section with dynamic image collage, 
 * renders system-wide analytics counter statistics (Users, Photos, Comments), presents the latest 
 * uploaded media cards grid, and provides architectural project overview details.
 */

require __DIR__ . '/layout/header.php';
?>

<!-- Hero Section with Dynamic Collage & Primary CTAs -->
<section class="hero-section rounded-4 p-4 p-md-5 mb-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="badge bg-light text-dark mb-3">Photo Sharing Application</span>
            <h1 class="display-4 fw-bold">Preserve moments. Share memories.</h1>
            <p class="lead">Alzikrayat is a welcoming photo-sharing space where users can upload, explore, and comment on meaningful images.</p>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-light btn-lg" href="<?= url('/gallery') ?>">Explore Gallery</a>
                <?php if (empty($_SESSION['user_id'])): ?>
                    <a class="btn btn-outline-light btn-lg" href="<?= url('/register') ?>">Join Alzikrayat</a>
                <?php else: ?>
                    <a class="btn btn-outline-light btn-lg" href="<?= url('/upload') ?>">Upload Photo</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-5">
            <!-- Hero Collage Previewing Up To 3 Recent Photos -->
            <div class="hero-collage">
                <?php foreach (array_slice($photos, 0, 3) as $photo): ?>
                    <img src="<?= url('/images/uploads/' . rawurlencode($photo['file_name'])) ?>" alt="<?= e($photo['title']) ?>">
                <?php endforeach; ?>
                <?php if (empty($photos)): ?>
                    <div class="empty-hero">Your gallery starts here.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- System Statistics Counter Metrics Grid -->
<section class="row g-3 mb-5">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-number"><?= $userCount ?></div>
            <div>Registered Users</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-number"><?= $photoCount ?></div>
            <div>Shared Photos</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-number"><?= $commentCount ?></div>
            <div>Comments</div>
        </div>
    </div>
</section>

<!-- Latest Community Photos Grid Section -->
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <h2 class="fw-bold">Latest Memories</h2>
            <p class="text-muted mb-0">Recently shared photos from the community.</p>
        </div>
        <a href="<?= url('/gallery') ?>">View all</a>
    </div>
    <div class="row g-4">
        <?php foreach ($photos as $photo): ?>
            <div class="col-md-6 col-lg-4">
                <a class="photo-card" href="<?= url('/photo/' . $photo['id']) ?>">
                    <img src="<?= url('/images/uploads/' . rawurlencode($photo['file_name'])) ?>" alt="<?= e($photo['title']) ?>">
                    <div class="p-3">
                        <h3 class="h5 mb-1"><?= e($photo['title']) ?></h3>
                        <small class="text-muted">By <?= e($photo['first_name'] . ' ' . $photo['last_name']) ?></small>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Application Brief Architecture Overview Panel -->
<section class="about-panel rounded-4 p-4 p-md-5">
    <h2 class="fw-bold">About Alzikrayat</h2>
    <p class="mb-0">Alzikrayat is designed as an MVC-based photo-sharing web application. The project separates presentation, application/business logic, and persistent data while keeping routing, SQL, validation, authentication, uploads, and comments understandable and handwritten.</p>
</section>

<?php require __DIR__ . '/layout/footer.php'; ?>