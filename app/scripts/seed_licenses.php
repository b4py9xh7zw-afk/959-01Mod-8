<?php
/**
 * Seed Licenses Script
 * This script creates sample licenses after users are created
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/License.php';

try {
    $userModel = new User();
    $licenseModel = new License();
    
    // Get admin user
    $admin = $userModel->findByEmail('admin@license-platform.com');
    if (!$admin) {
        echo "Admin user not found. Please run seed_users.php first.\n";
        exit(1);
    }
    
    // Get test user
    $testUser = $userModel->findByEmail('user@license-platform.com');
    if (!$testUser) {
        echo "Test user not found. Please run seed_users.php first.\n";
        exit(1);
    }
    
    // Check if licenses already exist
    $existingLicenses = $licenseModel->findAll(10, 0);
    if (count($existingLicenses) > 0) {
        echo "Licenses already exist. Skipping seed.\n";
        exit(0);
    }
    
    // Create sample licenses for admin
    $licenseModel->create([
        'user_id' => $admin['id'],
        'product_name' => 'Premium Software License',
        'status' => 'active',
        'expires_at' => date('Y-m-d H:i:s', strtotime('+1 year'))
    ]);
    
    $licenseModel->create([
        'user_id' => $admin['id'],
        'product_name' => 'Enterprise License',
        'status' => 'active',
        'expires_at' => date('Y-m-d H:i:s', strtotime('+2 years'))
    ]);
    
    // Create sample licenses for test user
    $licenseModel->create([
        'user_id' => $testUser['id'],
        'product_name' => 'Basic License',
        'status' => 'active',
        'expires_at' => date('Y-m-d H:i:s', strtotime('+6 months'))
    ]);
    
    $licenseModel->create([
        'user_id' => $testUser['id'],
        'product_name' => 'Trial License',
        'status' => 'expired',
        'expires_at' => date('Y-m-d H:i:s', strtotime('-1 month'))
    ]);
    
    echo "Sample licenses created successfully!\n";
} catch (Exception $e) {
    error_log("License seeding failed: " . $e->getMessage());
    echo "License seeding failed: " . $e->getMessage() . "\n";
    exit(1);
}
