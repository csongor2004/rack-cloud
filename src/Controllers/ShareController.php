<?php
namespace App\Controllers;
use App\Core\Database;
use App\Core\Uploader;
use App\Core\Logger;
use App\Core\Config;
class ShareController
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    // Létrehoz egy megosztási linket 24 órára
    public function createShare($fileId)
    {
        $token = bin2hex(random_bytes(16)); // 32 karakteres titkos kód
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $stmt = $this->db->getConnection()->prepare("
            INSERT INTO shares (file_id, share_token, expires_at) 
            VALUES (?, ?, ?)
        ");

        if ($stmt->execute([$fileId, $token, $expires])) {
            return $token;
        }
        return false;
    }

    // Ellenőrzi a tokent és visszaadja a fájl adatait
    public function validateToken($token)
    {
        $stmt = $this->db->getConnection()->prepare("
            SELECT f.* FROM files f
            JOIN shares s ON f.id = s.file_id
            WHERE s.share_token = ? AND s.expires_at > NOW()
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
}