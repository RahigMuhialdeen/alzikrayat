<?php

/**
 * Data-access model for the Photos table.
 */
class Photo extends Model
{
    /**
     * Retrieves the newest photos up to the requested safe limit.
     *
     * @param int $limit Maximum number of rows to return.
     * @return array<int, array<string, mixed>> Photo rows with owner names.
     */
    public function latest(int $limit = 12): array
    {
        // LIMIT cannot be bound as a normal PDO placeholder in this query, so clamp the integer first.
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
     * Inserts a new photo metadata record.
     *
     * @param int $userId Owner identifier.
     * @param string $fileName Safe physical filename.
     * @param string $title Photo title.
     * @param string|null $description Optional photo description.
     * @return int Newly created photo ID.
     */
    public function create(int $userId, string $fileName, string $title, ?string $description): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO Photos (user_id, file_name, title, description) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $fileName, $title, $description ?: null]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Deletes a photo only when its database owner matches the authenticated user.
     *
     * @param int $photoId Photo identifier to delete.
     * @param int $userId Authenticated user identifier.
     * @return string|null Physical filename when deletion succeeds, otherwise null.
     * @throws Throwable When the database transaction fails.
     */
    public function deleteOwned(int $photoId, int $userId): ?string
    {
        $this->db->beginTransaction();

        try {
            // Fetch the filename only after confirming both the photo ID and owner ID.
            $stmt = $this->db->prepare(
                'SELECT file_name FROM Photos WHERE id = ? AND user_id = ? LIMIT 1'
            );
            $stmt->execute([$photoId, $userId]);
            $photo = $stmt->fetch();

            if (!$photo) {
                $this->db->rollBack();
                return null;
            }

            $delete = $this->db->prepare(
                'DELETE FROM Photos WHERE id = ? AND user_id = ?'
            );
            $delete->execute([$photoId, $userId]);
            $this->db->commit();

            return $photo['file_name'];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Counts all photo records in the database.
     *
     * @return int Number of photos.
     */
    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM Photos')->fetchColumn();
    }
}
