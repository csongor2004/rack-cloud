<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Auth;

class AdminController
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getConnection();
        
        if ($_SESSION['role'] !== 'admin')
            die("Nincs jogosultságod!");
    }

    public function getGlobalStats()
    {
        $stats = [];
        // Összes fájl és méret
        $stats['total_files'] = $this->db->query("SELECT COUNT(*) FROM files")->fetchColumn();
        $stats['total_size'] = $this->db->query("SELECT SUM(file_size) FROM files")->fetchColumn();

        // Top 5 legtöbb tárhelyet használó user
        $stats['top_users'] = $this->db->query("
            SELECT u.username, SUM(f.file_size) as used 
            FROM users u 
            JOIN files f ON u.id = f.user_id 
            GROUP BY u.id 
            ORDER BY used DESC LIMIT 5
        ")->fetchAll();

        return $stats;
    }
}