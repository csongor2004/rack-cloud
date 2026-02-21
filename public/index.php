<?php
require_once __DIR__ . '/../autoloader.php';

use App\Core\{Database, Router, Uploader, Logger, Auth};
use App\Controllers\{FileController, DownloadController, ShareController, AdminController};
use App\Models\File;

session_start();

$db = new Database(); 
$fileModel = new File($db); 
$uploader = new Uploader(__DIR__ . '/../storage/');
$logger = new Logger($db);
$router = new Router();

$search = $_GET['search'] ?? null;
if (Auth::check()) {
    $files = $search ? $fileModel->searchFiles(Auth::id(), $search) : $fileModel->getAllByUserId(Auth::id());
}

use App\Controllers\AuthController;





$router->add('register', function () {
    include __DIR__ . '/../views/register.php';
});

$router->add('register_action', function () use ($db, $logger) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $authCtrl = new \App\Controllers\AuthController($db, $logger);
        if ($authCtrl->register($_POST['username'], $_POST['password'])) {
            header("Location: index.php?url=login&registered=1");
            exit;
        }
        $error = "A regisztráció sikertelen! (Lehet, hogy a név már foglalt)";
    }
    include __DIR__ . '/../views/register.php';
});


$router->add('home', function () use ($db, $uploader, $logger) {
    if (!Auth::check()) {
        header("Location: index.php?url=login");
        exit;
    }

    $fileModel = new File($db);
    $files = $fileModel->getAllByUserId(Auth::id());
    $usedSpace = $fileModel->getTotalSize(Auth::id());
    include __DIR__ . '/../views/dashboard.php';
});


$router->add('share', function () use ($db) {
    if (!App\Core\Auth::check())
        exit;

    $shareCtrl = new \App\Controllers\ShareController($db);
    $token = $shareCtrl->createShare($_GET['id']);

    if ($token) {
        
        header("Location: index.php?url=home&share_token=" . $token);
        exit;
    }
});

// --- CSOPORTOS MŰVELETEK (Törlés, ZIP letöltés) ---
$router->add('batch_action', function () use ($db, $uploader, $logger) {
    if (!App\Core\Auth::check())
        exit;

    $ids = $_POST['files'] ?? [];
    $action = $_POST['action'] ?? '';
    $userId = App\Core\Auth::id();

    if (empty($ids)) {
        header("Location: index.php?url=home");
        exit;
    }

    if ($action === 'delete') {
        $controller = new \App\Controllers\FileController($db, $uploader, $logger);
        $controller->batchDelete($ids, $userId);
    } elseif ($action === 'zip') {
        $fileModel = new \App\Models\File($db);
        $allFiles = $fileModel->getAllByUserId($userId);

        // Csak a kijelölt fájlok szűrése
        $selectedFiles = array_filter($allFiles, function ($f) use ($ids) {
            return in_array($f['id'], $ids);
        });

        $zipService = new \App\Services\ZipService();
        $zipPath = $zipService->createZipFromFiles($selectedFiles, __DIR__ . '/../storage/');

        if ($zipPath && file_exists($zipPath)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="RackCloud_Export_' . time() . '.zip"');
            readfile($zipPath);
            unlink($zipPath); // Ideiglenes fájl törlése
            exit;
        }
    }
    header("Location: index.php?url=home");
});


$router->add('login', function () use ($db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (Auth::login($_POST['username'], $_POST['password'], $db)) {
            header("Location: index.php?url=home");
            exit;
        }
        $error = "Helytelen felhasználónév vagy jelszó!";
    }
    include __DIR__ . '/../views/login.php';
});

$router->add('logout', function () {
    session_destroy();
    header("Location: index.php?url=login");
    exit;
});

// --- FÁJL MŰVELETEK ---
$router->add('upload', function () use ($db, $uploader, $logger) {
    if (Auth::check() && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new FileController($db, $uploader, $logger);
        $controller->handleUpload($_FILES['cloud_file'], Auth::id());
    }
    header("Location: index.php?url=home");
});

$router->add('download', function () use ($db) {
    if (Auth::check()) {
        $dl = new DownloadController($db, __DIR__ . '/../storage/');
        $dl->download($_GET['id'], Auth::id());
    }
});

$router->add('delete', function () use ($db, $uploader, $logger) {
    if (Auth::check()) {
        $controller = new FileController($db, $uploader, $logger);
        $controller->deleteFile($_GET['id'], Auth::id());
    }
    header("Location: index.php?url=home");
});

// --- ADMIN ---
$router->add('admin', function () use ($db) {
    if (!Auth::check() || $_SESSION['role'] !== 'admin')
        die("Hozzáférés megtagadva!");
    $adminCtrl = new AdminController($db);
    $stats = $adminCtrl->getGlobalStats();
    include __DIR__ . '/../views/admin.php';
});
$router->add('admin_backup', function () use ($db) {
    if (!App\Core\Auth::check() || $_SESSION['role'] !== 'admin') {
        die("Jogosulatlan hozzáférés!"); //
    }

    $backupService = new \App\Services\BackupService(__DIR__ . '/../storage/');
    $path = $backupService->generateFullBackup();

    if ($path && file_exists($path)) {
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        readfile($path);
        unlink($path); 
        exit;
    }
});
$router->dispatch($_GET['url'] ?? 'home');