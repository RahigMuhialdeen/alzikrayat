<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 fw-bold mb-2">Upload a Photo</h1>
                <p class="text-muted">
                    Images are physically saved in <code>public/images/uploads/</code>
                    and their metadata is stored in MySQL.
                </p>

                <form method="post" action="<?= url('/photo/store') ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input class="form-control" id="title" name="title" maxlength="200" required>
                        <div class="invalid-feedback">A title is required.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" maxlength="5000"></textarea>
                        <div class="invalid-feedback">Maximum 5000 characters.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="photo">Image</label>
                        <input
                            class="form-control"
                            type="file"
                            id="photo"
                            name="photo"
                            accept="image/jpeg,image/png,image/gif,image/webp"
                            required
                        >
                        <div class="form-text">JPG, JPEG, PNG, GIF or WEBP. Maximum 5 MB.</div>
                        <div class="invalid-feedback">Choose a valid image up to 5 MB.</div>
                    </div>

                    <button class="btn btn-primary" type="submit">Upload Photo</button>
                    <a class="btn btn-outline-secondary ms-2" href="<?= url('/gallery') ?>">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
