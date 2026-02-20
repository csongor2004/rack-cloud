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
        $fileModel = new \App\Models\File(new \App\Core\Database());
        $stats = [];
        $stats['total_files'] = $this->db->query("SELECT COUNT(*) FROM files")->fetchColumn();
        $stats['total_size'] = $this->db->query("SELECT SUM(file_size) FROM files")->fetchColumn();
        $stats['top_users'] = $this->db->query("... a régi SQL ...")->fetchAll();
        $stats['type_stats'] = $fileModel->getTypeStatistics();

        return $stats;
    }
    
}