<?php

/**
 * Singleton database connection for the Data Tier.
 *
 * Keeps one configured PDO connection available to all model classes.
 */
class Database
{
    /** @var Database|null Shared singleton instance. */
    private static ?Database $instance = null;

    /** @var PDO Active PDO connection. */
    private PDO $connection;

    /**
     * Creates the PDO connection with secure error and prepared-statement settings.
     *
     * @return void
     * @throws PDOException When MySQL cannot be reached or the database is unavailable.
     */
    private function __construct()
    {
        $host = '127.0.0.1';
        $db = 'alzikrayat';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->connection = new PDO($dsn, $user, $pass, $options);
    }

    /**
     * Returns the single Database instance, creating it on first use.
     *
     * @return Database Shared database wrapper instance.
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Returns the configured PDO connection used by the models.
     *
     * @return PDO Active PDO connection.
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
