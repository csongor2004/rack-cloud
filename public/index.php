<?php
require_once __DIR__ . '/../autoloader.php';

use App\Core\Database;
use App\Core\Router;
use App\Core\Uploader;
use App\Core\Logger;
use App\Core\Config;
use App\Controllers\FileController;
use App\Models\File;

session_start();


$db = new Database();
$uploader = new Uploader(__DIR__ . '/../storage/');
$logger = new Logger($db);
$router = new Router();


$router->add('home', function () use ($db, $uploader, $logger) {
    if (!App\Core\Auth::check()) {
        header("Location: login.php"); 
        exit;
    }

    $fileModel = new File($db);
    $files = $fileModel->getAllByUserId(App\Core\Auth::id());
    $usedSpace = $fileModel->getTotalSize(App\Core\Auth::id());

   
    include __DIR__ . '/../views/dashboard.php';
});

$router->add('upload', function () use ($db, $uploader, $logger) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cloud_file'])) {
        $controller = new FileController($db, $uploader, $logger);
        $controller->handleUpload($_FILES['cloud_file'], App\Core\Auth::id());
    }
    header("Location: index.php?url=home");
});



$router->add('batch_action', function () use ($db, $uploader, $logger) {
    if (!App\Core\Auth::check())
        exit;

    $ids = $_POST['files'] ?? [];
    $action = $_POST['action'] ?? '';
    $userId = App\Core\Auth::id();
    $controller = new FileController($db, $uploader, $logger);

    if ($action === 'delete') {
        $controller->batchDelete($ids, $userId);
        header("Location: index.php?url=home");
    } elseif ($action === 'zip' && !empty($ids)) {
        
        $fileModel = new File($db);
        $allFiles = $fileModel->getAllByUserId($userId);

        
        $selectedFiles = array_filter($allFiles, function ($f) use ($ids) {
            return in_array($f['id'], $ids);
        });

        $zipService = new \App\Services\ZipService();
        $zipPath = $zipService->createZipFromFiles($selectedFiles, __DIR__ . '/../storage/');

        if ($zipPath && file_exists($zipPath)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="RackCloud_Export.zip"');
            readfile($zipPath);
            unlink($zipPath); 
            exit;
        }
    }
    header("Location: index.php?url=home");
});
$router->dispatch($_GET['url'] ?? 'home');