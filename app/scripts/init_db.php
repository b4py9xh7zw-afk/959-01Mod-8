<?php
/**
 * Database initialization script
 * This script can be run manually to initialize the database
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    // Read and execute SQL file
    $sqlFile = __DIR__ . '/init.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: {$sqlFile}");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $connection->exec($statement);
            } catch (PDOException $e) {
                // Ignore errors for duplicate keys, etc.
                if (strpos($e->getMessage(), 'Duplicate') === false) {
                    error_log("SQL execution warning: " . $e->getMessage());
                }
            }
        }
    }
    
    echo "Database initialized successfully!\n";
} catch (Exception $e) {
    error_log("Database initialization failed: " . $e->getMessage());
    echo "Database initialization failed: " . $e->getMessage() . "\n";
    exit(1);
}
