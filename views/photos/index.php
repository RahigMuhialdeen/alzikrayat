<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="fw-bold mb-1">Gallery</h1>
        <p class="text-muted mb-0">Explore the memories shared on Alzikrayat.</p>
    </div>
    <div class="btn-group" role="group" aria-label="Gallery layout">
        <button class="btn btn-outline-primary active" data-layout="grid3">3 Columns</button>
        <button class="btn btn-outline-primary" data-layout="grid4">4 Columns</button>
        <button class="btn btn-outline-primary" data-layout="list">List</button>
    </div>
</div>
<div id="gallery" class="row g-4 gallery-grid-3">
    <?php foreach ($photos as $photo): ?>
        <div class="gallery-item col-md-6 col-lg-4">
            <article class="photo-card h-100">
                <a href="<?= url('/photo/' . $photo['id']) ?>"><img src="<?= url('/images/uploads/' . rawurlencode($photo['file_name'])) ?>" alt="<?= e($photo['title']) ?>"></a>
                <div class="p-3">
                    <h2 class="h5"><a class="text-decoration-none text-dark" href="<?= url('/photo/' . $photo['id']) ?>"><?= e($photo['title']) ?></a></h2>
                    <p class="text-muted small mb-2"><?= e($photo['description'] ?? '') ?></p><small>By <?= e($photo['first_name'] . ' ' . $photo['last_name']) ?></small>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</div>
<?php if (empty($photos)): ?><div class="empty-state text-center py-5">
        <h2>No photos yet</h2>
        <p>Register and upload the first memory.</p><a class="btn btn-primary" href="<?= url('/register') ?>">Get Started</a>
    </div><?php endif; ?>
<?php require __DIR__ . '/../layout/footer.php'; ?>