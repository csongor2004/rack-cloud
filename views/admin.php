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
</div>
</body>
</html>