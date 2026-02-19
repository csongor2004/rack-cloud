<?php
namespace App\Controllers;
use App\Core\Database;
use App\Core\Uploader;
use App\Core\Logger;
use App\Core\Config;

class FileController
{
    private $db;
    private $uploader;

    private $logger;

    public function __construct(Database $db, Uploader $uploader, Logger $logger)
    {
        $this->db = $db;
        $this->uploader = $uploader;
        $this->logger = $logger;
    }

    public function handleUpload($fileArray, $userId)
    {
        // Feltöltjük fizikailag a storage-ba
        $storedName = $this->uploader->upload($fileArray);

        if ($storedName) {
            
            $stmt = $this->db->getConnection()->prepare("
                INSERT INTO files (user_id, original_name, stored_name, file_size, file_type) 
                VALUES (?, ?, ?, ?, ?)
            ");

            return $stmt->execute([
                $userId,
                $fileArray['name'],
                $storedName,
                $fileArray['size'],
                $fileArray['type']
            ]);
        }
        return false;
    }

    public function getUserFiles($userId)
    {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM files WHERE user_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function hasEnoughSpace($userId, $newFileSize)
    {
        $stmt = $this->db->getConnection()->prepare("SELECT SUM(file_size) as total FROM files WHERE user_id = ?");
        $stmt->execute([$userId]);
        $usage = $stmt->fetch();

        $limit = Config::get('app.storage_limit');
        return ($usage['total'] + $newFileSize) <= $limit;
    }

    
    public function deleteFile($fileId, $userId)
    {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
        $stmt->execute([$fileId, $userId]);
        $file = $stmt->fetch();

        if ($file) {
            
            $delStmt = $this->db->getConnection()->prepare("DELETE FROM files WHERE id = ?");
            if ($delStmt->execute([$fileId])) {
                
                $path = __DIR__ . '/../../storage/' . $file['stored_name'];
                if (file_exists($path)) {
                    return unlink($path);
                }
            }
        }
        return false;
    }
}