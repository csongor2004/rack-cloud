<?php
require_once __DIR__ . '/../autoloader.php';

use App\Core\Database;
use App\Core\Uploader;
use App\Core\Auth;
use App\Core\FileController;

session_start();
$db = new Database();
$uploader = new Uploader(__DIR__ . '/../storage/');
$fileController = new FileController($db, $uploader);

// MINTA: Ha nincs user, csinálunk egy tesztet (Ezt később regisztrációval váltjuk ki)
// $password = password_hash('csongor123', PASSWORD_DEFAULT);
// $db->getConnection()->query("INSERT IGNORE INTO users (username, password) VALUES ('Csongor', '$password')");

echo "<h1>RackCloud Pro v2.0</h1>";

if (!Auth::check()) {
    // Itt jöhetne a login form
    Auth::login('Csongor', 'csongor123', $db);
    echo "<p>Rendszer: Automatikus teszt belépés...</p>";
}

echo "<p>Üdvözöllek, <strong>" . $_SESSION['username'] . "</strong>!</p>";

// Feltöltés kezelése
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cloud_file'])) {
    if ($fileController->handleUpload($_FILES['cloud_file'], Auth::id())) {
        echo "<b style='color:green'>Sikeres mentés a felhőbe!</b>";
    }
}

// Fájlok listázása
$myFiles = $fileController->getUserFiles(Auth::id());
?>

<h3>Fájljaid a felhőben:</h3>
<table border="1" cellpadding="10">
    <tr><th>Eredeti név</th><th>Méret</th><th>Dátum</th><th>Művelet</th></tr>
    <?php foreach ($myFiles as $f): ?>
    <tr>
        <td><?= htmlspecialchars($f['original_name']) ?></td>
        <td><?= round($f['file_size'] / 1024, 2) ?> KB</td>
        <td><?= $f['uploaded_at'] ?></td>
        <td><a href="#">Letöltés</a></td>
    </tr>
    <?php endforeach; ?>
</table>

<form method="POST" enctype="multipart/form-data" style="margin-top:20px">
    <input type="file" name="cloud_file">
    <button type="submit">Új fájl feltöltése</button>
</form>