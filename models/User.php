<?php

/**
 * Data-access model for the Users table.
 */
class User extends Model
{
    /**
     * Finds a user by the unique email address.
     *
     * @param string $email Normalized email address.
     * @return array<string, mixed>|null Matching user row or null when not found.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM Users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Finds a user by numeric ID without returning the password hash.
     *
     * @param int $id User identifier.
     * @return array<string, mixed>|null Matching user row or null when not found.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, first_name, last_name, email, location, description, occupation
             FROM Users WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Checks whether an email address is already registered.
     *
     * @param string $email Email address to check.
     * @return bool True when the email already exists.
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM Users WHERE email = ?');
        $stmt->execute([$email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Creates a user from validated registration data.
     *
     * @param array<string, mixed> $data User registration values, including a hashed password.
     * @return int Newly created user ID.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO Users (first_name, last_name, email, password, location, description, occupation)
             VALUES (:first_name, :last_name, :email, :password, :location, :description, :occupation)'
        );
        $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':location' => $data['location'] ?: null,
            ':description' => $data['description'] ?: null,
            ':occupation' => $data['occupation'] ?: null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Counts all registered users.
     *
     * @return int Number of users.
     */
    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM Users')->fetchColumn();
    }
}
