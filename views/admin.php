<?php
$predicter = new \App\Services\PredictiveService();
$history = (new \App\Models\File(new \App\Core\Database()))->getStorageGrowthHistory();
$daysLeft = $predicter->estimateDaysUntilFull($history, \App\Core\Config::get('app.storage_limit'));
?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>RackCloud Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php?url=home"><i class="bi bi-shield-lock"></i> RackCloud Admin Panel</a>
        <a href="index.php?url=home" class="btn btn-outline-light btn-sm">Vissza a Dashboardra</a>
    </div>
</nav>

<div class="container">
    <div class="row mb-4 text-center">
        <div class="col-md-6">
            <div class="card bg-primary text-white shadow-sm border-0 p-4">
                <h2 class="display-4 fw-bold"><?= $stats['total_files'] ?></h2>
                <p class="mb-0">Összes feltöltött fájl</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white shadow-sm border-0 p-4">
                <h2 class="display-4 fw-bold"><?= round($stats['total_size'] / 1024 / 1024, 2) ?> MB</h2>
                <p class="mb-0">Összesített tárhely-használat</p>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Top 5 Tárhely-használó</h5>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Felhasználó</th><th>Foglalt terület</th><th>Kihasználtság (Max: 50MB)</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['top_users'] as $u): ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= round($u['used'] / 1024 / 1024, 2) ?> MB</td>
                        <td width="40%">
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-info" style="width: <?= ($u['used'] / 52428800) * 100 ?>"></div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
    <div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Fájltípus Megoszlás</h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <?php foreach ($stats['type_stats'] as $type): ?>
            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded bg-white">
                    <h6 class="text-muted small text-uppercase fw-bold"><?= $type['file_type'] ?: 'Egyéb' ?></h6>
                    <h4 class="mb-0"><?= $type['count'] ?> db</h4>
                    <small class="text-info"><?= round($type['total_size'] / 1024 / 1024, 2) ?> MB</small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</div>

    <div class="row mt-4">
    <div class="col-md-12">
        <div class="card bg-dark text-warning shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1"><i class="bi bi-cpu-fill"></i> AI Tárhely Előrejelzés</h5>
                    <p class="mb-0">A jelenlegi növekedés alapján a szerver betelik:</p>
                </div>
                <div class="text-end">
                    <h2 class="display-6 fw-bold mb-0"><?= $daysLeft ?> nap</h2>
                    <small>múlva</small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>