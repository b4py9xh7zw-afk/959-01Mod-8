<?php
/**
 * Database Configuration
 * Supports multiple database types (MySQL, PostgreSQL, SQLite)
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $dbType = getenv('DB_TYPE') ?: 'mysql';
        $host = getenv('DB_HOST') ?: 'db';
        $port = getenv('DB_PORT') ?: '3306';
        $dbname = getenv('DB_NAME') ?: 'license_platform';
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASS') ?: 'root';
        
        try {
            switch ($dbType) {
                case 'mysql':
                    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                    break;
                case 'pgsql':
                    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
                    break;
                case 'sqlite':
                    $dsn = "sqlite:" . __DIR__ . "/../../database.sqlite";
                    break;
                default:
                    throw new Exception("Unsupported database type: {$dbType}");
            }
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            if ($dbType === 'mysql') {
                $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4";
            }
            
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed. Please check your configuration.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
        }
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function execute($sql, $params = []) {
        return $this->query($sql, $params);
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}
