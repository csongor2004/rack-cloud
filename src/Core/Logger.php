<?php
namespace App\Core;

class Logger
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function log($action, $details = null, $userId = null)
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $stmt = $this->db->getConnection()->prepare("
            INSERT INTO activity_logs (user_id, action, details, ip_address) 
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([$userId, $action, $details, $ip]);
    }
}