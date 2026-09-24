<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="mb-4">
    <h1 class="fw-bold mb-1">Photo Details</h1>
    <p class="text-muted mb-0">View the photo information and community comments.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm overflow-hidden">
            <img
                class="detail-image"
                src="<?= url('/images/uploads/' . rawurlencode($photo['file_name'])) ?>"
                alt="<?= e($photo['title']) ?>"
            >

            <div class="p-4">
                <h2 class="fw-bold"><?= e($photo['title']) ?></h2>
                <p><?= nl2br(e($photo['description'] ?? '')) ?></p>
                <div class="text-muted small">
                    Shared by <?= e($photo['first_name'] . ' ' . $photo['last_name']) ?>
                    on <?= e($photo['date_time']) ?>
                </div>

                <?php
                // Only the authenticated owner receives a delete action.
                if (!empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$photo['user_id']):
                ?>
                    <form
                        method="post"
                        action="<?= url('/photo/' . $photo['id'] . '/delete') ?>"
                        class="mt-3"
                        onsubmit="return confirm('Delete this photo permanently?');"
                    >
                        <button class="btn btn-outline-danger" type="submit">Delete My Photo</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h2 class="h4 fw-bold">Comments</h2>

            <div class="comments-list mb-4">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <strong><?= e($comment['first_name'] . ' ' . $comment['last_name']) ?></strong>
                        <p class="mb-1 mt-1"><?= nl2br(e($comment['comment'])) ?></p>
                        <small class="text-muted"><?= e($comment['date_time']) ?></small>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($comments)): ?>
                    <p class="text-muted">No comments yet. Be the first.</p>
                <?php endif; ?>
            </div>

            <?php if (!empty($_SESSION['user_id'])): ?>
                <form method="post" action="<?= url('/photo/' . $photo['id'] . '/comment') ?>" class="needs-validation" novalidate>
                    <label class="form-label" for="comment">Add a comment</label>
                    <textarea
                        class="form-control mb-2"
                        id="comment"
                        name="comment"
                        maxlength="1000"
                        rows="4"
                        required
                    ></textarea>
                    <div class="invalid-feedback mb-2">Comment is required and must be at most 1000 characters.</div>
                    <button class="btn btn-primary w-100" type="submit">Post Comment</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info">
                    Please <a href="<?= url('/login') ?>">login</a> to comment.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
