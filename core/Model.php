<?php

/**
 * Abstract base model for the Data Tier.
 *
 * Concrete models inherit one shared PDO connection from the Database singleton.
 */
abstract class Model
{
    /** @var PDO Active PDO connection used by model queries. */
    protected PDO $db;

    /**
     * Initializes the model with the shared database connection.
     *
     * @return void
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
}
