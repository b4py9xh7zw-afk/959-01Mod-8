<?php
/**
 * Seed Users Script
 * This script creates initial users with correct password hashes
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

try {
    $userModel = new User();
    
    // Check if admin user exists
    $admin = $userModel->findByEmail('admin@license-platform.com');
    if (!$admin) {
        $userModel->create([
            'username' => 'admin',
            'email' => 'admin@license-platform.com',
            'password' => 'admin123',
            'role' => 'admin'
        ]);
        echo "Admin user created successfully.\n";
    } else {
        echo "Admin user already exists.\n";
    }
    
    // Check if test user exists
    $testUser = $userModel->findByEmail('user@license-platform.com');
    if (!$testUser) {
        $userModel->create([
            'username' => 'testuser',
            'email' => 'user@license-platform.com',
            'password' => 'user123',
            'role' => 'user'
        ]);
        echo "Test user created successfully.\n";
    } else {
        echo "Test user already exists.\n";
    }
    
    echo "User seeding completed!\n";
} catch (Exception $e) {
    error_log("User seeding failed: " . $e->getMessage());
    echo "User seeding failed: " . $e->getMessage() . "\n";
    exit(1);
}
