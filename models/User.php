<?php

/**
 * Data-access model for the Users table.
 *
 * Provides database operations for locating users by email or ID,
 * checking email uniqueness, creating new user records, and counting
 * registered users.
 */
class User extends Model
{
    /**
     * Finds a user by the unique email address.
     *
     * The email is expected to be normalized before being passed to
     * this method. The complete user row is returned because the
     * authentication process requires access to the stored password hash.
     *
     * @param string $email Normalized email address.
     * @return array<string, mixed>|null Matching user row or null when not found.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM Users WHERE email = ? LIMIT 1'
        );

        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Finds a user by numeric ID without returning the password hash.
     *
     * Only profile and identification fields required by the application
     * are selected. The password hash is intentionally excluded from the
     * result because this method is used for retrieving user information,
     * not for authentication.
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
     * The method performs a parameterized COUNT query and returns a
     * boolean result indicating whether another user already uses
     * the supplied email address.
     *
     * @param string $email Email address to check.
     * @return bool True when the email already exists; false otherwise.
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM Users WHERE email = ?'
        );

        $stmt->execute([$email]);

        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Creates a user from validated registration data.
     *
     * The supplied password must already be securely hashed before this
     * method is called. Optional profile fields are stored as NULL when
     * their values are empty. All values are passed through a parameterized
     * INSERT statement rather than being concatenated into SQL.
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
     * This method returns the total number of rows currently stored
     * in the Users table and can be used for application statistics.
     *
     * @return int Number of users.
     */
    public function count(): int
    {
        return (int)$this->db->query(
            'SELECT COUNT(*) FROM Users'
        )->fetchColumn();
    }
}
