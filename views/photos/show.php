<?php

/**
 * Photo details and interaction view for the Alzikrayat application.
 *
 * Renders the complete details page for a selected photo, including the
 * photo preview, stored metadata, server-generated image filter controls,
 * ownership-protected delete action, existing user comments, and the
 * authenticated comment submission form.
 *
 * Image filters are requested through PhotoController::filter() and are
 * generated on the server using PHP GD. The original stored image is
 * never modified by the filter operation.
 *
 * User-controlled values are escaped before being rendered into HTML,
 * while the application's URL helpers are used to generate internal
 * links and form actions.
 *
 * @package Alzikrayat\Views\Photos
 * @file show.php
 * @category View / Presentation Layer
 *
 * @see \PhotoController::show()
 * @see \PhotoController::filter()
 * @see \PhotoController::delete()
 * @see \CommentController::store()
 *
 * @var array $photo Photo details retrieved from the database.
 * @var array $comments Comments associated with the displayed photo.
 */

require __DIR__ . '/../layout/header.php';
?>

<div class="mb-4">
    <h1 class="fw-bold mb-1">Photo Details</h1>
    <p class="text-muted mb-0">
        View the photo information, apply image filters, and read community comments.
    </p>
</div>

<div class="row g-4">

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm overflow-hidden">

            <!--
                Photo Preview Component.
                Displays the currently selected version of the stored image.
                The image source is initially the original uploaded file and
                can later be replaced by a server-generated filtered image.
            -->
            <div class="bg-light text-center p-3">
                <img
                    id="photoPreview"
                    class="detail-image"
                    src="<?= url('/images/uploads/' . rawurlencode($photo['file_name'])) ?>"
                    alt="<?= e($photo['title']) ?>">
            </div>

            <!--
                Image Filter Controls.
                Each button stores the URL of a server-generated filter
                response in its data-filter-url attribute.
            -->
            <div class="p-3 border-top bg-light">
                <h3 class="h5 fw-bold mb-3">Image Filters</h3>

                <div class="d-flex flex-wrap gap-2">

                    <!-- Original image filter option -->
                    <button
                        type="button"
                        class="btn btn-outline-secondary filter-button"
                        data-filter-url="<?= url('/photo/' . $photo['id'] . '/filter/original') ?>">
                        Original
                    </button>

                    <!-- Grayscale image filter option -->
                    <button
                        type="button"
                        class="btn btn-outline-secondary filter-button"
                        data-filter-url="<?= url('/photo/' . $photo['id'] . '/filter/grayscale') ?>">
                        Grayscale
                    </button>

                    <!-- Sepia image filter option -->
                    <button
                        type="button"
                        class="btn btn-outline-secondary filter-button"
                        data-filter-url="<?= url('/photo/' . $photo['id'] . '/filter/sepia') ?>">
                        Sepia
                    </button>

                    <!-- Invert image filter option -->
                    <button
                        type="button"
                        class="btn btn-outline-secondary filter-button"
                        data-filter-url="<?= url('/photo/' . $photo['id'] . '/filter/invert') ?>">
                        Invert
                    </button>

                </div>

                <small class="text-muted d-block mt-2">
                    Filters are generated on the server using PHP GD.
                    The original stored image is not modified.
                </small>
            </div>

            <!--
                Photo Information Component.
                Displays the title, description, author, and upload
                timestamp associated with the selected photo.
            -->
            <div class="p-4">
                <h2 class="fw-bold"><?= e($photo['title']) ?></h2>

                <p>
                    <?= nl2br(e($photo['description'] ?? '')) ?>
                </p>

                <div class="text-muted small">
                    Shared by
                    <?= e($photo['first_name'] . ' ' . $photo['last_name']) ?>
                    on
                    <?= e($photo['date_time']) ?>
                </div>

                <?php
                /**
                 * Ownership-Protected Delete Action.
                 *
                 * Displays the delete form only when an authenticated
                 * user's session identifier matches the owner identifier
                 * associated with the current photo.
                 *
                 * This presentation-layer check complements the ownership
                 * authorization enforced by the controller/model layer.
                 */
                if (
                    !empty($_SESSION['user_id'])
                    && (int)$_SESSION['user_id'] === (int)$photo['user_id']
                ):
                ?>

                    <form
                        method="post"
                        action="<?= url('/photo/' . $photo['id'] . '/delete') ?>"
                        class="mt-3"
                        onsubmit="return confirm('Delete this photo permanently?');">

                        <button
                            class="btn btn-outline-danger"
                            type="submit">
                            Delete My Photo
                        </button>

                    </form>

                <?php endif; ?>

            </div>
        </div>
    </div>

    <!--
        Comments Presentation Component.
        Displays all comments associated with the current photo and,
        when the visitor is authenticated, provides the comment form.
    -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 h-100">

            <h2 class="h4 fw-bold">Comments</h2>

            <div class="comments-list mb-4">

                <?php foreach ($comments as $comment): ?>

                    <!-- Individual User Comment -->
                    <div class="comment">

                        <strong>
                            <?= e($comment['first_name'] . ' ' . $comment['last_name']) ?>
                        </strong>

                        <p class="mb-1 mt-1">
                            <?= nl2br(e($comment['comment'])) ?>
                        </p>

                        <small class="text-muted">
                            <?= e($comment['date_time']) ?>
                        </small>

                    </div>

                <?php endforeach; ?>

                <?php if (empty($comments)): ?>

                    <!-- Empty Comments State -->
                    <p class="text-muted">
                        No comments yet. Be the first.
                    </p>

                <?php endif; ?>

            </div>

            <?php if (!empty($_SESSION['user_id'])): ?>

                <!--
                    Authenticated Comment Submission Form.
                    Logged-in users can submit a new comment for the
                    currently displayed photo.
                -->
                <form
                    method="post"
                    action="<?= url('/photo/' . $photo['id'] . '/comment') ?>"
                    class="needs-validation"
                    novalidate>

                    <label
                        class="form-label"
                        for="comment">
                        Add a comment
                    </label>

                    <textarea
                        class="form-control mb-2"
                        id="comment"
                        name="comment"
                        maxlength="1000"
                        rows="4"
                        required></textarea>

                    <div class="invalid-feedback mb-2">
                        Comment is required and must be at most 1000 characters.
                    </div>

                    <button
                        class="btn btn-primary w-100"
                        type="submit">
                        Post Comment
                    </button>

                </form>

            <?php else: ?>

                <!--
                    Unauthenticated Comment State.
                    Visitors who are not logged in are directed to the
                    login page before they can submit a comment.
                -->
                <div class="alert alert-info">
                    Please
                    <a href="<?= url('/login') ?>">login</a>
                    to comment.
                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<script>
    /**
     * Registers click handlers for all image-filter buttons.
     *
     * Each filter button contains a server-side filter URL in its
     * data-filter-url attribute. The registered handler uses that URL
     * to replace the source of the photo preview with the selected
     * server-generated filter result.
     *
     * @returns {void}
     */
    document.querySelectorAll('.filter-button').forEach(function(button) {
        /**
         * Handles a click event on an individual image-filter button.
         *
         * Reads the filter URL from the clicked button and updates the
         * photo preview element when both the preview and URL are available.
         *
         * @param {MouseEvent} event The browser click event generated by the button.
         * @returns {void}
         */
        button.addEventListener('click', function(event) {
            const preview = document.getElementById('photoPreview');
            const filterUrl = button.dataset.filterUrl;

            if (preview && filterUrl) {
                preview.src = filterUrl;
            }
        });
    });
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>