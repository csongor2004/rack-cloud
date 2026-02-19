<?php
require_once __DIR__ . '/../autoloader.php';

use App\Core\{Database, Router, Uploader, Logger, Auth};
use App\Controllers\{FileController, DownloadController, ShareController, AdminController};
use App\Models\File;

session_start();

$db = new Database();
$uploader = new Uploader(__DIR__ . '/../storage/');
$logger = new Logger($db);
$router = new Router();

// --- DASHBOARD (Főoldal) ---
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

// --- AUTH (Be- és kijelentkezés) ---
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

$router->dispatch($_GET['url'] ?? 'home');