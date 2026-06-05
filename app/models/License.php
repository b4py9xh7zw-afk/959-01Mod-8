<?php
/**
 * License Model
 */

require_once __DIR__ . '/../config/database.php';

class License {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO licenses (license_key, user_id, product_name, status, expires_at, created_at) 
                VALUES (:license_key, :user_id, :product_name, :status, :expires_at, NOW())";
        
        $params = [
            ':license_key' => $this->generateLicenseKey(),
            ':user_id' => $data['user_id'],
            ':product_name' => $data['product_name'],
            ':status' => $data['status'] ?? 'active',
            ':expires_at' => $data['expires_at'] ?? null
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    public function findById($id) {
        $sql = "SELECT l.*, u.username, u.email 
                FROM licenses l 
                LEFT JOIN users u ON l.user_id = u.id 
                WHERE l.id = :id";
        return $this->db->fetchOne($sql, [':id' => $id]);
    }
    
    public function findByKey($key) {
        $sql = "SELECT l.*, u.username, u.email 
                FROM licenses l 
                LEFT JOIN users u ON l.user_id = u.id 
                WHERE l.license_key = :key";
        return $this->db->fetchOne($sql, [':key' => $key]);
    }
    
    public function findByUserId($userId, $limit = 100, $offset = 0) {
        // Ensure limit and offset are integers to prevent SQL injection
        $limit = max(1, min(1000, (int)$limit));
        $offset = max(0, (int)$offset);
        $sql = "SELECT l.*, u.username, u.email 
                FROM licenses l 
                LEFT JOIN users u ON l.user_id = u.id 
                WHERE l.user_id = :user_id 
                ORDER BY l.created_at DESC 
                LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql, [':user_id' => $userId]);
    }
    
    public function findAll($limit = 100, $offset = 0) {
        // Ensure limit and offset are integers to prevent SQL injection
        $limit = max(1, min(1000, (int)$limit));
        $offset = max(0, (int)$offset);
        $sql = "SELECT l.*, u.username, u.email 
                FROM licenses l 
                LEFT JOIN users u ON l.user_id = u.id 
                ORDER BY l.created_at DESC 
                LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql);
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM licenses";
        $result = $this->db->fetchOne($sql);
        return $result['count'] ?? 0;
    }
    
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as count FROM licenses WHERE status = :status";
        $result = $this->db->fetchOne($sql, [':status' => $status]);
        return $result['count'] ?? 0;
    }
    
    public function countByUserId($userId) {
        $sql = "SELECT COUNT(*) as count FROM licenses WHERE user_id = :user_id";
        $result = $this->db->fetchOne($sql, [':user_id' => $userId]);
        return $result['count'] ?? 0;
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        if (isset($data['product_name'])) {
            $fields[] = "product_name = :product_name";
            $params[':product_name'] = $data['product_name'];
        }
        if (isset($data['status'])) {
            $fields[] = "status = :status";
            $params[':status'] = $data['status'];
        }
        if (isset($data['expires_at'])) {
            $fields[] = "expires_at = :expires_at";
            $params[':expires_at'] = $data['expires_at'];
        }
        if (isset($data['user_id'])) {
            $fields[] = "user_id = :user_id";
            $params[':user_id'] = $data['user_id'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE licenses SET " . implode(', ', $fields) . " WHERE id = :id";
        $this->db->execute($sql, $params);
        return true;
    }
    
    public function delete($id) {
        $sql = "DELETE FROM licenses WHERE id = :id";
        $this->db->execute($sql, [':id' => $id]);
        return true;
    }
    
    private function generateLicenseKey() {
        return strtoupper(
            substr(md5(uniqid(rand(), true)), 0, 8) . '-' .
            substr(md5(uniqid(rand(), true)), 0, 8) . '-' .
            substr(md5(uniqid(rand(), true)), 0, 8) . '-' .
            substr(md5(uniqid(rand(), true)), 0, 8)
        );
    }
    
    public function validate($licenseKey) {
        $license = $this->findByKey($licenseKey);
        if (!$license) {
            return ['valid' => false, 'message' => 'License key not found'];
        }
        
        if ($license['status'] !== 'active') {
            return ['valid' => false, 'message' => 'License is not active'];
        }
        
        if ($license['expires_at'] && strtotime($license['expires_at']) < time()) {
            return ['valid' => false, 'message' => 'License has expired'];
        }
        
        return ['valid' => true, 'license' => $license];
    }
}
