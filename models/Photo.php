<?php

/**
 * Data-access model for the Photos table.
 *
 * Provides database operations for retrieving photo records,
 * creating new photo metadata, deleting photos owned by the
 * authenticated user, and counting stored photos.
 */
class Photo extends Model
{
    /**
     * Retrieves the newest photos up to the requested safe limit.
     *
     * The method limits the requested value to a safe integer range
     * before placing it in the SQL statement because the LIMIT value
     * is not bound through the normal PDO parameter placeholder used
     * for other query values.
     *
     * @param int $limit Maximum number of rows to return.
     * @return array<int, array<string, mixed>> Photo rows with owner names.
     */
    public function latest(int $limit = 12): array
    {
        // LIMIT cannot be bound as a normal PDO placeholder in this query,
        // so clamp the integer first to prevent an unsafe SQL value.
        $limit = max(1, min($limit, 50));

        $stmt = $this->db->query(
            "SELECT p.*, u.first_name, u.last_name
             FROM Photos p JOIN Users u ON u.id = p.user_id
             ORDER BY p.date_time DESC LIMIT {$limit}"
        );

        return $stmt->fetchAll();
    }

    /**
     * Retrieves every photo ordered from newest to oldest.
     *
     * The query joins each photo with its owner so that the returned
     * rows contain the first and last name of the user who uploaded
     * each photo.
     *
     * @return array<int, array<string, mixed>> Photo rows with owner names.
     */
    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT p.*, u.first_name, u.last_name
             FROM Photos p JOIN Users u ON u.id = p.user_id
             ORDER BY p.date_time DESC'
        );

        return $stmt->fetchAll();
    }

    /**
     * Finds one photo and its owner information by ID.
     *
     * The method returns the photo metadata together with the owner's
     * name and email. If no photo exists with the supplied identifier,
     * the method returns null.
     *
     * @param int $id Photo identifier.
     * @return array<string, mixed>|null Matching photo row or null when not found.
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, u.first_name, u.last_name, u.email
             FROM Photos p JOIN Users u ON u.id = p.user_id
             WHERE p.id = ? LIMIT 1'
        );

        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Inserts a new photo metadata record into the Photos table.
     *
     * The physical image file is handled separately by the controller;
     * this method stores the database metadata required to associate
     * the uploaded file with its owner and descriptive information.
     *
     * @param int $userId Owner identifier.
     * @param string $fileName Safe physical filename stored for the photo.
     * @param string $title Photo title.
     * @param string|null $description Optional photo description.
     * @return int Newly created photo ID.
     */
    public function create(
        int $userId,
        string $fileName,
        string $title,
        ?string $description
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO Photos (user_id, file_name, title, description) VALUES (?, ?, ?, ?)'
        );

        $stmt->execute([$userId, $fileName, $title, $description ?: null]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Deletes a photo only when its database owner matches the authenticated user.
     *
     * The method first retrieves the physical filename while checking
     * both the photo identifier and owner identifier. If the ownership
     * check fails, no deletion is performed and null is returned.
     *
     * The database operation is wrapped in a transaction so that a
     * failure during deletion does not leave the transaction open.
     * When successful, the physical filename is returned so that the
     * controller can remove the corresponding uploaded file.
     *
     * @param int $photoId Photo identifier to delete.
     * @param int $userId Authenticated user identifier.
     * @return string|null Physical filename when deletion succeeds,
     *                     otherwise null when the photo does not exist
     *                     or is not owned by the supplied user.
     * @throws Throwable When the database transaction or deletion fails.
     */
    public function deleteOwned(int $photoId, int $userId): ?string
    {
        $this->db->beginTransaction();

        try {
            // Retrieve the filename only when both the photo ID and owner ID match.
            // This prevents a user from deleting another user's photo.
            $stmt = $this->db->prepare(
                'SELECT file_name FROM Photos WHERE id = ? AND user_id = ? LIMIT 1'
            );

            $stmt->execute([$photoId, $userId]);
            $photo = $stmt->fetch();

            if (!$photo) {
                $this->db->rollBack();
                return null;
            }

            // Delete using the same ownership condition as the initial lookup
            // so the database operation remains protected against ownership changes.
            $delete = $this->db->prepare(
                'DELETE FROM Photos WHERE id = ? AND user_id = ?'
            );

            $delete->execute([$photoId, $userId]);
            $this->db->commit();

            return $photo['file_name'];
        } catch (Throwable $e) {
            // Roll back only when the transaction is still active.
            // The original exception is then re-thrown for the controller to handle.
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Counts all photo records currently stored in the database.
     *
     * This method returns the total number of rows in the Photos table
     * and can be used by the application when displaying photo statistics.
     *
     * @return int Number of photos currently stored.
     */
    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM Photos')->fetchColumn();
    }
}
