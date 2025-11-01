<?php
class Database {
    // 🔹 Hostinger MySQL credentials
    private $host = 'srv1499.hstgr.io'; // or '193.203.184.99'
    private $database = 'u255007981_tour';
    private $username = 'u255007981_tour'; // likely same as DB name; replace if your panel shows different
    private $password = 'u255007981_Tour'; // ⚠️ replace with actual password from Hostinger panel

    private $connection = null;

    public function __construct() {
        $this->connect();
    }

    /**
     * Connect to database
     */
    public function connect() {
        if ($this->connection === null) {
            try {
                $this->connection = new PDO(
                    "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4",
                    $this->username,
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Get PDO connection
     */
    public function getConnection() {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Disconnect database
     */
    public function disconnect() {
        $this->connection = null;
    }

    /**
     * Run a query
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Fetch all rows
     */
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Fetch single row
     */
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Execute write query (INSERT, UPDATE, DELETE)
     */
    public function execute($sql, $params = []) {
        return $this->query($sql, $params)->rowCount();
    }

    /**
     * Get last inserted ID
     */
    public function lastInsertId() {
        return $this->getConnection()->lastInsertId();
    }
}
?>
