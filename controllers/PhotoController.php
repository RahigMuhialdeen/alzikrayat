<?php

/**
 * Handles the application's home page, gallery, photo lifecycle, and About Us page.
 */
class PhotoController extends Controller
{
    /** @var Photo Photo model used for photo records and ownership checks. */
    private Photo $photos;

    /** @var User User model used for home-page statistics. */
    private User $users;

    /** @var Comment Comment model used for home-page statistics and photo details. */
    private Comment $comments;

    /**
     * Creates the controller and initializes all required model dependencies.
     *
     * @return void
     */
    public function __construct()
    {
        $this->photos = new Photo();
        $this->users = new User();
        $this->comments = new Comment();
    }

    /**
     * Displays the public landing page with recent photos and statistics.
     *
     * @return void
     */
    public function home(): void
    {
        $this->view('home', [
            'title' => 'Alzikrayat | Photo Sharing',
            'photos' => $this->photos->latest(6),
            'userCount' => $this->users->count(),
            'photoCount' => $this->photos->count(),
            'commentCount' => $this->comments->count(),
        ]);
    }

    /**
     * Displays all stored photos in the gallery.
     *
     * @return void
     */
    public function index(): void
    {
        $this->view('photos/index', [
            'title' => 'Gallery',
            'photos' => $this->photos->all()
        ]);
    }

    /**
     * Displays one photo, its metadata, and its comments.
     *
     * @param array<string, mixed> $params Route parameters containing the photo ID.
     * @return void
     */
    public function show(array $params): void
    {
        $id = filter_var(
            $params['id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if (!$id) {
            $this->showNotFound();
            return;
        }

        $photo = $this->photos->find($id);
        if (!$photo) {
            $this->showNotFound();
            return;
        }

        $this->view('photos/show', [
            'title' => $photo['title'],
            'photo' => $photo,
            'comments' => $this->comments->forPhoto($id)
        ]);
    }

    /**
     * Displays the authenticated user's photo-upload form.
     *
     * @return void
     */
    public function create(): void
    {
        $this->requireLogin();
        $this->view('photos/create', ['title' => 'Upload Photo']);
    }

    /**
     * Validates an uploaded image, stores it physically, and saves its metadata.
     *
     * The server validates title length, file extension, MIME type, image validity,
     * and file size before moving the upload into the protected uploads directory.
     *
     * @return void
     */
    public function store(): void
    {
        $this->requireLogin();

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $file = $_FILES['photo'] ?? null;
        $errors = [];

        if ($title === '' || strlen($title) > 200) {
            $errors[] = 'Title is required and must be at most 200 characters.';
        }
        if ($description !== '' && strlen($description) > 5000) {
            $errors[] = 'Description is too long.';
        }
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Please select a valid image.';
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

            // Validate both extension and detected MIME type to avoid trusting the filename alone.
            if (!in_array($extension, $allowedExtensions, true) || !in_array($mime, $allowedMimes, true)) {
                $errors[] = 'Only JPG, JPEG, PNG, GIF, and WEBP images are allowed.';
            }
            if ($file['size'] > 5 * 1024 * 1024) {
                $errors[] = 'Image size must not exceed 5 MB.';
            }
            if (@getimagesize($file['tmp_name']) === false) {
                $errors[] = 'The uploaded file is not a valid image.';
            }
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/upload');
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Randomized filenames prevent collisions and avoid trusting user-supplied file names on disk.
        $safeName = bin2hex(random_bytes(12)) . '_' . time() . '.' . $extension;
        $uploadDir = __DIR__ . '/../public/images/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $safeName)) {
            $_SESSION['errors'] = ['Unable to save the uploaded file. Check folder permissions.'];
            $this->redirect('/upload');
        }

        $this->photos->create(
            (int)$_SESSION['user_id'],
            $safeName,
            $title,
            $description
        );

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Photo uploaded successfully.'];
        $this->redirect('/gallery');
    }

    /**
     * Deletes a photo only when the authenticated user owns it.
     *
     * The database record is deleted first inside a transaction. The physical
     * file is then removed from the uploads directory using a basename-only path.
     *
     * @param array<string, mixed> $params Route parameters containing the photo ID.
     * @return void
     */
    public function delete(array $params): void
    {
        $this->requireLogin();

        $id = filter_var(
            $params['id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if (!$id) {
            $this->redirect('/gallery');
        }

        $fileName = $this->photos->deleteOwned($id, (int)$_SESSION['user_id']);
        if ($fileName === null) {
            $_SESSION['errors'] = ['You can delete only your own photos, or the photo no longer exists.'];
            $this->redirect('/gallery');
        }

        // basename() prevents a stored filename from escaping the uploads directory.
        $filePath = __DIR__ . '/../public/images/uploads/' . basename($fileName);
        if (is_file($filePath)) {
            unlink($filePath);
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Photo deleted successfully.'];
        $this->redirect('/gallery');
    }

    /**
     * Displays the static About Us page.
     *
     * @return void
     */
    public function about(): void
    {
        $this->view('about', ['title' => 'About Us']);
    }

    /**
     * Renders the application's custom 404 page with an HTTP 404 status.
     *
     * @return void
     */
    private function showNotFound(): void
    {
        http_response_code(404);
        require __DIR__ . '/../views/layout/404.php';
    }
}
