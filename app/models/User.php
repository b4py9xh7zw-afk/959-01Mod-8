<?php
/**
 * User Model
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO users (username, email, password_hash, role, created_at) 
                VALUES (:username, :email, :password_hash, :role, NOW())";
        
        $params = [
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role' => $data['role'] ?? 'user'
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        return $this->db->fetchOne($sql, [':email' => $email]);
    }
    
    public function findById($id) {
        $sql = "SELECT id, username, email, role, created_at FROM users WHERE id = :id";
        return $this->db->fetchOne($sql, [':id' => $id]);
    }
    
    public function findAll($limit = 100, $offset = 0) {
        // Ensure limit and offset are integers to prevent SQL injection
        $limit = max(1, min(1000, (int)$limit));
        $offset = max(0, (int)$offset);
        $sql = "SELECT id, username, email, role, created_at FROM users 
                ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql);
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM users";
        $result = $this->db->fetchOne($sql);
        return $result['count'] ?? 0;
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        if (isset($data['username'])) {
            $fields[] = "username = :username";
            $params[':username'] = $data['username'];
        }
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }
        if (isset($data['password'])) {
            $fields[] = "password_hash = :password_hash";
            $params[':password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $params[':role'] = $data['role'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $this->db->execute($sql, $params);
        return true;
    }
    
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $this->db->execute($sql, [':id' => $id]);
        return true;
    }
    
    public function verifyPassword($email, $password) {
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }
        return password_verify($password, $user['password_hash']);
    }
}
