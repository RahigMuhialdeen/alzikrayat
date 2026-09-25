<?php

/**
 * Alzikrayat - Photo Sharing Web Application
 * 
 * @package   Alzikrayat\Views\Photos
 * @file      create.php (Upload)
 * @category  View / Presentation Layer
 * @see       \App\Controllers\PhotoController::create()
 * @see       \App\Controllers\PhotoController::store()
 * 
 * Description:
 * Renders the photo creation/upload form interface. Supports multipart/form-data POST submissions
 * for physical file uploads, enforces client-side file MIME-type filtering, and captures 
 * metadata (title, description) for database persistence.
 */

require __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 fw-bold mb-2">Upload a Photo</h1>
                <p class="text-muted">
                    Images are physically saved in <code>public/images/uploads/</code>
                    and their metadata is stored in MySQL.
                </p>

                <!-- Photo Upload HTML Form Component supporting Multipart EncType -->
                <form method="post" action="<?= url('/photo/store') ?>" enctype="multipart/form-data" class="needs-validation" novalidate>

                    <!-- Title Input Field -->
                    <div class="mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input class="form-control" id="title" name="title" maxlength="200" required>
                        <div class="invalid-feedback">A title is required.</div>
                    </div>

                    <!-- Description Input Field -->
                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" maxlength="5000"></textarea>
                        <div class="invalid-feedback">Maximum 5000 characters.</div>
                    </div>

                    <!-- Physical File Upload Input Field -->
                    <div class="mb-4">
                        <label class="form-label" for="photo">Image</label>
                        <input
                            class="form-control"
                            type="file"
                            id="photo"
                            name="photo"
                            accept="image/jpeg,image/png,image/gif,image/webp"
                            required>
                        <div class="form-text">JPG, JPEG, PNG, GIF or WEBP. Maximum 5 MB.</div>
                        <div class="invalid-feedback">Choose a valid image up to 5 MB.</div>
                    </div>

                    <!-- Form Action Controls -->
                    <button class="btn btn-primary" type="submit">Upload Photo</button>
                    <a class="btn btn-outline-secondary ms-2" href="<?= url('/gallery') ?>">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>