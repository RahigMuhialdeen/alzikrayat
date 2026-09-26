<?php

/**
 * Handles creation and validation of comments submitted for photo detail pages.
 *
 * Verifies that the user is authenticated, validates the target photo and
 * comment content, persists the comment through the Comment model, and
 * redirects the user with an appropriate success or error message.
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
     * Verifies that the user is authenticated, the target photo exists, and
     * the submitted comment is not empty and does not exceed 1000 characters.
     * Invalid input or a missing photo is stored in the session as an error
     * before redirecting the user.
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
