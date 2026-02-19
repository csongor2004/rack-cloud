<?php
namespace App\Controllers;
use App\Core\Database;
use App\Core\Uploader;
use App\Core\Logger;
use App\Core\Config;
class DownloadController
{
    private $db;
    private $storageDir;

    public function __construct(Database $db, $storageDir)
    {
        $this->db = $db;
        $this->storageDir = $storageDir;
    }

    public function download($fileId, $userId)
    {
        // Ellenőrizzük, hogy a fájl létezik-e és a felhasználóé-e
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
        $stmt->execute([$fileId, $userId]);
        $file = $stmt->fetch();

        if (!$file) {
            die("Hiba: A fájl nem található vagy nincs hozzáférésed!");
        }

        $fullPath = $this->storageDir . $file['stored_name'];

        if (file_exists($fullPath)) {
            // Fejlécek beállítása, hogy a böngésző letöltésként kezelje
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $file['file_type']);
            header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fullPath));

            // A fájl tartalmának kiírása (streaming)
            readfile($fullPath);
            exit;
        }
    }
}