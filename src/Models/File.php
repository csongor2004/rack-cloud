<?php
namespace App\Models;

use App\Core\Database;

class File
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getConnection();
    }

    public function getAllByUserId($userId): array 
    {
        $stmt = $this->db->prepare("SELECT * FROM files WHERE user_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll() ?: []; 
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO files (user_id, original_name, stored_name, file_size, file_type) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['user_id'],
            $data['original_name'],
            $data['stored_name'],
            $data['file_size'],
            $data['file_type']
        ]);
    }

    public function getTotalSize($userId)
    {
        $stmt = $this->db->prepare("SELECT SUM(file_size) as total FROM files WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch()['total'] ?? 0;
    }
}