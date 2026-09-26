<?php

/**
 * Singleton database connection manager for the Data Tier.
 *
 * Provides a single configured PDO connection that can be shared by
 * the application's model classes. The Singleton pattern prevents
 * unnecessary creation of multiple database connection objects during
 * the same request lifecycle.
 *
 * The PDO connection is configured to:
 * - Throw PDOException instances when database operations fail.
 * - Return query results as associative arrays by default.
 * - Disable emulated prepared statements so that PDO uses native
 *   prepared statements where supported by the database driver.
 *
 * @package Alzikrayat\Core
 * @category Data Access / Database Infrastructure
 */
class Database
{
    /** @var Database|null Shared Singleton database manager instance. */
    private static ?Database $instance = null;

    /** @var PDO Active configured PDO database connection. */
    private PDO $connection;

    /**
     * Creates and configures the application's PDO database connection.
     *
     * The constructor is private because Database instances must be
     * obtained through getInstance(). It configures the MySQL connection
     * using the application's database name, credentials, character set,
     * PDO error mode, default fetch mode, and prepared-statement settings.
     *
     * @return void
     *
     * @throws PDOException
     * Thrown when PDO cannot establish a connection to the configured
     * MySQL database or when the PDO connection initialization fails.
     */
    private function __construct()
    {
        $host = '127.0.0.1';
        $db = 'alzikrayat';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        // Build the MySQL DSN using the configured host, database, and character set.
        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

        /*
         * Configure PDO for exception-based error handling, associative
         * array result sets, and native prepared statements.
         */
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // Establish the configured database connection.
        $this->connection = new PDO($dsn, $user, $pass, $options);
    }

    /**
     * Returns the shared Singleton Database instance.
     *
     * Creates the Database object on the first call and returns the
     * already-created instance on subsequent calls during the request.
     *
     * @return Database The shared Database manager instance.
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            // Lazily create the single Database instance when first requested.
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Returns the active PDO connection used by the application's models.
     *
     * Provides model classes with access to the centrally configured
     * PDO connection without exposing the connection initialization
     * process itself.
     *
     * @return PDO The active configured PDO database connection.
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
