<?php
namespace App\Core;

class FileController
{
    private $db;
    private $uploader;

    public function __construct(Database $db, Uploader $uploader)
    {
        $this->db = $db;
        $this->uploader = $uploader;
    }

    public function handleUpload($fileArray, $userId)
    {
        // Feltöltjük fizikailag a storage-ba
        $storedName = $this->uploader->upload($fileArray);

        if ($storedName) {
            // Bejegyezzük az adatbázisba
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
}