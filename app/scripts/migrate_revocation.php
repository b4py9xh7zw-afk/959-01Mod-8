<?php
/**
 * Database migration script for blacklist/graylist revocation feature
 * Run this script once to upgrade existing databases
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance();
    
    echo "Starting database migration...\n";
    
    $migrations = [
        "ALTER TABLE licenses MODIFY COLUMN status ENUM('active', 'inactive', 'expired', 'blacklisted', 'greylisted') DEFAULT 'active'",
        "ALTER TABLE licenses ADD COLUMN revocation_reason TEXT NULL AFTER status",
        "ALTER TABLE licenses ADD COLUMN appeal_channel VARCHAR(255) NULL DEFAULT 'support@example.com' AFTER revocation_reason",
    ];
    
    foreach ($migrations as $sql) {
        try {
            $db->execute($sql);
            echo "✓ Applied: " . substr($sql, 0, 60) . "...\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'already exists') !== false) {
                echo "○ Skipped (already exists): " . substr($sql, 0, 60) . "...\n";
            } else {
                throw $e;
            }
        }
    }
    
    $createTableSql = "CREATE TABLE IF NOT EXISTS license_revocation_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        license_id INT NOT NULL,
        action_type ENUM('blacklist', 'greylist', 'restore') NOT NULL,
        operator_id INT NOT NULL,
        reason TEXT NULL,
        restore_scope VARCHAR(255) NULL,
        responsible_person VARCHAR(255) NULL,
        previous_status ENUM('active', 'inactive', 'expired', 'blacklisted', 'greylisted') NULL,
        new_status ENUM('active', 'inactive', 'expired', 'blacklisted', 'greylisted') NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (license_id) REFERENCES licenses(id) ON DELETE CASCADE,
        FOREIGN KEY (operator_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_license_id (license_id),
        INDEX idx_action_type (action_type),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->execute($createTableSql);
    echo "✓ Created license_revocation_logs table\n";
    
    echo "\nMigration completed successfully!\n";
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
