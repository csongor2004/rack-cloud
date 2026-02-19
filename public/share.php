<?php
use App\Core\Database;
use App\Core\Uploader;
use App\Core\Logger;
use App\Core\Config;
require_once __DIR__ . '/../autoloader.php';


use App\Controllers\ShareController;

$db = new Database();
$shareController = new ShareController($db);

if (isset($_GET['token'])) {
    $file = $shareController->validateToken($_GET['token']);

    if ($file) {
        $storagePath = __DIR__ . '/../storage/' . $file['stored_name'];
        if (file_exists($storagePath)) {
            header('Content-Type: ' . $file['file_type']);
            header('Content-Disposition: attachment; filename="SHARED_' . $file['original_name'] . '"');
            readfile($storagePath);
            exit;
        }
    } else {
        die("<h1>Hiba: A link érvénytelen vagy lejárt!</h1>");
    }
}