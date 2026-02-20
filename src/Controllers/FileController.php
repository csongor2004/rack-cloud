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
        
        $newStoredName = $this->uploader->upload($fileArray);
        if (!$newStoredName)
            return false;

        $db = $this->db->getConnection();

        $stmt = $db->prepare("SELECT id, stored_name FROM files WHERE user_id = ? AND original_name = ?");
        $stmt->execute([$userId, $fileArray['name']]);
        $existingFile = $stmt->fetch();

        if ($existingFile) {
            
            $vStmt = $db->prepare("
            INSERT INTO file_versions (file_id, stored_name, version_number) 
            SELECT id, stored_name, (SELECT COUNT(*) + 1 FROM file_versions WHERE file_id = ?) 
            FROM files WHERE id = ?
        ");
            $vStmt->execute([$existingFile['id'], $existingFile['id']]);

            
            $updateStmt = $db->prepare("
            UPDATE files 
            SET stored_name = ?, file_size = ?, file_type = ?, uploaded_at = NOW() 
            WHERE id = ?
        ");
            $updateStmt->execute([$newStoredName, $fileArray['size'], $fileArray['type'], $existingFile['id']]);

            $this->logger->log('VERSION_UPLOAD', "Új verzió feltöltve: " . $fileArray['name'], $userId);
            return true;
        } else {
           
            $insertStmt = $db->prepare("
            INSERT INTO files (user_id, original_name, stored_name, file_size, file_type) 
            VALUES (?, ?, ?, ?, ?)
        ");

            $success = $insertStmt->execute([
                $userId,
                $fileArray['name'],
                $newStoredName,
                $fileArray['size'],
                $fileArray['type']
            ]);

            if ($success) {
                $this->logger->log('UPLOAD', "Új fájl feltöltve: " . $fileArray['name'], $userId);
            }
            return $success;
        }
    }

    public function getUserFiles($userId)
    {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM files WHERE user_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    public function batchDelete($fileIds, $userId)
    {
        foreach ($fileIds as $id) {
            $this->deleteFile($id, $userId);
        }
        return true;
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