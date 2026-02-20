<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Logger;
use App\Core\Auth;

class AuthController
{
    private $db;
    private $logger;

    public function __construct(Database $db, Logger $logger)
    {
        $this->db = $db->getConnection();
        $this->logger = $logger;
    }

    public function register($username, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
        try {
            $stmt->execute([$username, $hashedPassword]);
            $userId = $this->db->lastInsertId();

            
            $this->logger->log('REGISTER', "Új felhasználó regisztrált: $username", $userId);
            return true;
        } catch (\PDOException $e) {
            return false; 
        }
    }
}