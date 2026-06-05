<?php
/**
 * Password Hash Generator
 * Run this script to generate password hashes for database seeding
 * 
 * Usage: php generate_password_hash.php <password>
 */

if ($argc < 2) {
    echo "Usage: php generate_password_hash.php <password>\n";
    echo "Example: php generate_password_hash.php admin123\n";
    exit(1);
}

$password = $argv[1];
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: {$password}\n";
echo "Hash: {$hash}\n";
echo "\nSQL INSERT statement:\n";
echo "INSERT INTO users (username, email, password_hash, role) VALUES\n";
echo "('username', 'email@example.com', '{$hash}', 'user');\n";
