<?php

/**
 * Handles the application's home page, gallery, photo lifecycle,
 * server-side image filtering, and About Us page.
 *
 * Coordinates photo, user, and comment models to provide the application's
 * presentation-layer actions while enforcing authentication, ownership,
 * upload validation, and photo-processing rules.
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
     * Retrieves the latest photos and aggregate counts for users, photos,
     * and comments, then passes the data to the home-page view.
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
     * Retrieves all photo records from the Photo model and passes them
     * to the gallery view for presentation.
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
     * Displays one photo, its metadata, and its associated comments.
     *
     * Validates the photo ID from the route, verifies that the photo exists,
     * and displays a custom 404 response when the ID or photo is invalid.
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
     * Generates and displays a filtered version of a stored photo.
     *
     * Supported filters are original, grayscale, sepia, and invert.
     * The original image stored on disk is never modified. Filtered images
     * are generated dynamically using PHP GD and returned as PNG data.
     *
     * Invalid route parameters, missing photos, unavailable image files,
     * missing GD support, unreadable files, and invalid image data result
     * in an appropriate HTTP error response or custom 404 page.
     *
     * @param array<string, mixed> $params Route parameters containing photo ID and filter name.
     * @return void
     */
    public function filter(array $params): void
    {
        $id = filter_var(
            $params['id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $filter = strtolower(trim((string)($params['filter'] ?? '')));

        $allowedFilters = [
            'original',
            'grayscale',
            'sepia',
            'invert'
        ];

        if (!$id || !in_array($filter, $allowedFilters, true)) {
            $this->showNotFound();
            return;
        }

        $photo = $this->photos->find($id);

        if (!$photo) {
            $this->showNotFound();
            return;
        }

        $fileName = basename((string)$photo['file_name']);
        $filePath = __DIR__ . '/../public/images/uploads/' . $fileName;

        if (!is_file($filePath) || !is_readable($filePath)) {
            http_response_code(404);
            echo 'Image file not found.';
            return;
        }

        /*
         * GD is required because the novelty feature performs real
         * server-side image manipulation rather than only applying CSS.
         */
        if (!extension_loaded('gd') || !function_exists('imagecreatefromstring')) {
            http_response_code(500);
            echo 'PHP GD extension is required for image filters.';
            return;
        }

        $imageData = file_get_contents($filePath);

        if ($imageData === false) {
            http_response_code(500);
            echo 'Unable to read the image.';
            return;
        }

        /*
         * The original image is returned unchanged when the user selects
         * the Original option.
         */
        if ($filter === 'original') {
            $imageInfo = @getimagesize($filePath);

            if ($imageInfo === false || empty($imageInfo['mime'])) {
                http_response_code(500);
                echo 'Invalid image file.';
                return;
            }

            header('Content-Type: ' . $imageInfo['mime']);
            header('Cache-Control: public, max-age=3600');

            echo $imageData;
            return;
        }

        $image = @imagecreatefromstring($imageData);

        if ($image === false) {
            http_response_code(500);
            echo 'Unable to process the image.';
            return;
        }

        /*
         * Preserve transparency while processing PNG/GIF/WEBP images.
         * The final filtered image is exported as PNG.
         */
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $width = imagesx($image);
        $height = imagesy($image);

        /*
         * Pixel-by-pixel processing is intentional here.
         * It demonstrates a custom image-filter algorithm rather than
         * relying on a CSS filter or an external image-processing library.
         */
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($image, $x, $y);

                $red = ($rgb >> 16) & 0xFF;
                $green = ($rgb >> 8) & 0xFF;
                $blue = $rgb & 0xFF;

                switch ($filter) {
                    case 'grayscale':
                        // Standard luminance formula approximating human brightness perception.
                        $gray = (int)round(
                            (0.299 * $red) +
                                (0.587 * $green) +
                                (0.114 * $blue)
                        );

                        $red = $gray;
                        $green = $gray;
                        $blue = $gray;
                        break;

                    case 'sepia':
                        // Custom sepia transformation based on weighted RGB channels.
                        $newRed = (int)min(
                            255,
                            round((0.393 * $red) + (0.769 * $green) + (0.189 * $blue))
                        );

                        $newGreen = (int)min(
                            255,
                            round((0.349 * $red) + (0.686 * $green) + (0.168 * $blue))
                        );

                        $newBlue = (int)min(
                            255,
                            round((0.272 * $red) + (0.534 * $green) + (0.131 * $blue))
                        );

                        $red = $newRed;
                        $green = $newGreen;
                        $blue = $newBlue;
                        break;

                    case 'invert':
                        $red = 255 - $red;
                        $green = 255 - $green;
                        $blue = 255 - $blue;
                        break;
                }

                /*
                 * Preserve the original alpha channel.
                 * GD stores alpha using 7-bit values from 0 to 127.
                 */
                $alpha = ($rgb >> 24) & 0x7F;

                $newColor = imagecolorallocatealpha(
                    $image,
                    $red,
                    $green,
                    $blue,
                    $alpha
                );

                imagesetpixel($image, $x, $y, $newColor);
            }
        }

        header('Content-Type: image/png');
        header('Cache-Control: public, max-age=3600');

        imagepng($image, null, 6);
        imagedestroy($image);
    }

    /**
     * Displays the authenticated user's photo-upload form.
     *
     * Authentication is required before the upload form can be displayed.
     *
     * @return void
     */
    public function create(): void
    {
        $this->requireLogin();

        $this->view('photos/create', [
            'title' => 'Upload Photo'
        ]);
    }

    /**
     * Validates an uploaded image, stores it physically, and saves its metadata.
     *
     * The server validates the title, description, file extension, detected
     * MIME type, image validity, and file size before moving the uploaded file.
     * Validation failures are stored in the session and redirect the user back
     * to the upload form. File-system failures also produce an error message.
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

        $allowedExtensions = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp'
        ];

        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(
                pathinfo($file['name'], PATHINFO_EXTENSION)
            );

            $mime = (new finfo(FILEINFO_MIME_TYPE))->file(
                $file['tmp_name']
            );

            // Validate both extension and detected MIME type to avoid trusting the filename alone.
            if (
                !in_array($extension, $allowedExtensions, true) ||
                !in_array($mime, $allowedMimes, true)
            ) {
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

        $extension = strtolower(
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        // Randomized filenames prevent collisions and avoid trusting user-supplied file names on disk.
        $safeName = bin2hex(random_bytes(12))
            . '_'
            . time()
            . '.'
            . $extension;

        $uploadDir = __DIR__ . '/../public/images/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file(
            $file['tmp_name'],
            $uploadDir . $safeName
        )) {
            $_SESSION['errors'] = [
                'Unable to save the uploaded file. Check folder permissions.'
            ];

            $this->redirect('/upload');
        }

        $this->photos->create(
            (int)$_SESSION['user_id'],
            $safeName,
            $title,
            $description
        );

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Photo uploaded successfully.'
        ];

        $this->redirect('/gallery');
    }

    /**
     * Deletes a photo only when the authenticated user owns it.
     *
     * Validates the route ID, verifies ownership through the Photo model,
     * removes the database record, and then removes the corresponding
     * physical file from the uploads directory.
     *
     * If the ID is invalid or the authenticated user does not own the photo,
     * an error message is stored in the session and the user is redirected
     * to the gallery.
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

        $fileName = $this->photos->deleteOwned(
            $id,
            (int)$_SESSION['user_id']
        );

        if ($fileName === null) {
            $_SESSION['errors'] = [
                'You can delete only your own photos, or the photo no longer exists.'
            ];

            $this->redirect('/gallery');
        }

        // basename() prevents a stored filename from escaping the uploads directory.
        $filePath = __DIR__
            . '/../public/images/uploads/'
            . basename($fileName);

        if (is_file($filePath)) {
            unlink($filePath);
        }

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Photo deleted successfully.'
        ];

        $this->redirect('/gallery');
    }

    /**
     * Displays the static About Us page.
     *
     * @return void
     */
    public function about(): void
    {
        $this->view('about', [
            'title' => 'About Us'
        ]);
    }

    /**
     * Renders the application's custom 404 page and sets the HTTP status.
     *
     * This method is used when a requested photo or route parameter
     * cannot be found or is invalid.
     *
     * @return void
     */
    private function showNotFound(): void
    {
        http_response_code(404);
        require __DIR__ . '/../views/layout/404.php';
    }
}
