<?php
/**
 * License Revocation Log Model
 * For audit trail of blacklist/graylist operations and restorations
 */

require_once __DIR__ . '/../config/database.php';

class LicenseRevocationLog {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO license_revocation_logs (
                    license_id, action_type, operator_id, reason, 
                    restore_scope, responsible_person, previous_status, new_status
                ) VALUES (
                    :license_id, :action_type, :operator_id, :reason,
                    :restore_scope, :responsible_person, :previous_status, :new_status
                )";
        
        $params = [
            ':license_id' => $data['license_id'],
            ':action_type' => $data['action_type'],
            ':operator_id' => $data['operator_id'],
            ':reason' => $data['reason'] ?? null,
            ':restore_scope' => $data['restore_scope'] ?? null,
            ':responsible_person' => $data['responsible_person'] ?? null,
            ':previous_status' => $data['previous_status'] ?? null,
            ':new_status' => $data['new_status'] ?? null,
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    public function logBlacklist($licenseId, $operatorId, $reason, $previousStatus) {
        return $this->create([
            'license_id' => $licenseId,
            'action_type' => 'blacklist',
            'operator_id' => $operatorId,
            'reason' => $reason,
            'previous_status' => $previousStatus,
            'new_status' => 'blacklisted'
        ]);
    }
    
    public function logGreylist($licenseId, $operatorId, $reason, $previousStatus) {
        return $this->create([
            'license_id' => $licenseId,
            'action_type' => 'greylist',
            'operator_id' => $operatorId,
            'reason' => $reason,
            'previous_status' => $previousStatus,
            'new_status' => 'greylisted'
        ]);
    }
    
    public function logRestore($licenseId, $operatorId, $reason, $restoreScope, $responsiblePerson, $previousStatus) {
        return $this->create([
            'license_id' => $licenseId,
            'action_type' => 'restore',
            'operator_id' => $operatorId,
            'reason' => $reason,
            'restore_scope' => $restoreScope,
            'responsible_person' => $responsiblePerson,
            'previous_status' => $previousStatus,
            'new_status' => 'active'
        ]);
    }
    
    public function findByLicenseId($licenseId, $limit = 50, $offset = 0) {
        $limit = max(1, min(1000, (int)$limit));
        $offset = max(0, (int)$offset);
        $sql = "SELECT l.*, u.username as operator_name 
                FROM license_revocation_logs l 
                LEFT JOIN users u ON l.operator_id = u.id 
                WHERE l.license_id = :license_id 
                ORDER BY l.created_at DESC 
                LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql, [':license_id' => $licenseId]);
    }
    
    public function findAll($limit = 50, $offset = 0) {
        $limit = max(1, min(1000, (int)$limit));
        $offset = max(0, (int)$offset);
        $sql = "SELECT l.*, u.username as operator_name, lic.license_key 
                FROM license_revocation_logs l 
                LEFT JOIN users u ON l.operator_id = u.id 
                LEFT JOIN licenses lic ON l.license_id = lic.id 
                ORDER BY l.created_at DESC 
                LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql);
    }
    
    public function findByActionType($actionType, $limit = 50, $offset = 0) {
        $limit = max(1, min(1000, (int)$limit));
        $offset = max(0, (int)$offset);
        $sql = "SELECT l.*, u.username as operator_name, lic.license_key 
                FROM license_revocation_logs l 
                LEFT JOIN users u ON l.operator_id = u.id 
                LEFT JOIN licenses lic ON l.license_id = lic.id 
                WHERE l.action_type = :action_type 
                ORDER BY l.created_at DESC 
                LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql, [':action_type' => $actionType]);
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM license_revocation_logs";
        $result = $this->db->fetchOne($sql);
        return $result['count'] ?? 0;
    }
    
    public function countByActionType($actionType) {
        $sql = "SELECT COUNT(*) as count FROM license_revocation_logs WHERE action_type = :action_type";
        $result = $this->db->fetchOne($sql, [':action_type' => $actionType]);
        return $result['count'] ?? 0;
    }
}
