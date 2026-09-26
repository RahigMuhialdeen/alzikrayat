<?php

/**
 * Data-access model for the Comments table.
 *
 * Provides database operations for retrieving comments by photo,
 * creating new comments, and counting stored comments.
 */
class Comment extends Model
{
    /**
     * Retrieves all comments belonging to one photo in chronological order.
     *
     * The query joins the Comments table with the Users table so that
     * each returned comment also contains the author's first and last name.
     *
     * @param int $photoId Photo identifier used to filter comments.
     * @return array<int, array<string, mixed>> Comment rows with author names.
     */
    public function forPhoto(int $photoId): array
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, u.first_name, u.last_name
             FROM Comments c JOIN Users u ON u.id = c.user_id
             WHERE c.photo_id = ? ORDER BY c.date_time ASC'
        );

        $stmt->execute([$photoId]);

        return $stmt->fetchAll();
    }

    /**
     * Creates a new comment associated with a photo and user.
     *
     * The method inserts the supplied comment text together with the
     * identifiers of the target photo and authenticated author.
     *
     * @param int $photoId Target photo identifier.
     * @param int $userId Authenticated author identifier.
     * @param string $comment Comment text to store in the database.
     * @return int Newly created comment ID.
     */
    public function create(int $photoId, int $userId, string $comment): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO Comments (photo_id, user_id, comment) VALUES (?, ?, ?)'
        );

        $stmt->execute([$photoId, $userId, $comment]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Counts all comments currently stored in the database.
     *
     * This method is used when the application needs the total number
     * of comments, such as displaying application statistics.
     *
     * @return int Number of comments currently stored.
     */
    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM Comments')->fetchColumn();
    }
}
