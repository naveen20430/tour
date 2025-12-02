<?php
/**
 * Database Connection Handler
 * PDO-based database connection with proper error handling
 */

require_once __DIR__ . '/../config/database.php';

class DB {
    private static $instance = null;
    private $connection = null;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->connect();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Connect to database using Database class
     */
    private function connect() {
        if ($this->connection === null) {
            $db = new Database();
            $this->connection = $db->getConnection();
        }
    }

    /**
     * Get PDO connection
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Run a query with prepared statements
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return PDOStatement
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database Query Error: " . $e->getMessage());
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Fetch all rows
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return array
     */
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch single row
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return array|false
     */
    public function fetch($sql, $params = []) {
        $result = $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : false;
    }

    /**
     * Execute write query (INSERT, UPDATE, DELETE)
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return int Number of affected rows
     */
    public function execute($sql, $params = []) {
        return $this->query($sql, $params)->rowCount();
    }

    /**
     * Get last inserted ID
     * @return string
     */
    public function lastInsertId() {
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->getConnection()->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->getConnection()->rollBack();
    }
}

