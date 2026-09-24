<?php

/**
 * Handles creation of comments on photo detail pages.
 */
class CommentController extends Controller
{
    /** @var Comment Comment model used to persist comments. */
    private Comment $comments;

    /** @var Photo Photo model used to verify the target photo. */
    private Photo $photos;

    /**
     * Creates the controller and initializes its model dependencies.
     *
     * @return void
     */
    public function __construct()
    {
        $this->comments = new Comment();
        $this->photos = new Photo();
    }

    /**
     * Validates and stores a comment for an existing photo.
     *
     * @param array<string, mixed> $params Route parameters containing the photo ID.
     * @return void
     */
    public function store(array $params): void
    {
        $this->requireLogin();

        $photoId = filter_var(
            $params['id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );
        $comment = trim($_POST['comment'] ?? '');

        if (!$photoId || !$this->photos->find($photoId)) {
            $_SESSION['errors'] = ['Photo not found.'];
            $this->redirect('/gallery');
        }

        // Keep the same length boundary on the server that is presented by the form.
        if ($comment === '' || strlen($comment) > 1000) {
            $_SESSION['errors'] = ['Comment is required and must be at most 1000 characters.'];
            $this->redirect('/photo/' . $photoId);
        }

        $this->comments->create($photoId, (int)$_SESSION['user_id'], $comment);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Comment added.'];
        $this->redirect('/photo/' . $photoId);
    }
}
