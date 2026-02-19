<?php
require_once __DIR__ . '/../autoloader.php';

use App\Core\Database;
use App\Core\Router;
use App\Controllers\FileController;

session_start();
$db = new Database();
$router = new Router();


$router->add('home', function () use ($db) {
    
    include __DIR__ . '/../views/dashboard.php';
});

$router->add('upload', function () use ($db) {
    
});


$router->dispatch($_GET['url'] ?? '');