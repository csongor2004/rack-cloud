<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private $pdo;

    public function __construct()
    {
        
        $host = Config::get('db.host');
        $db = Config::get('db.name');
        $user = Config::get('db.user');
        $pass = Config::get('db.pass');

        try {
            $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Adatbázis hiba: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}