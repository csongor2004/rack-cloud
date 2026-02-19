<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>RackCloud - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>RackCloud Storage</h1>
        <span class="badge bg-primary">Bejelentkezve: <?= $_SESSION['username'] ?></span>
    </div>

    <?php
    $limit = App\Core\Config::get('app.storage_limit');
    $percent = ($usedSpace / $limit) * 100;
    $color = $percent > 80 ? 'bg-danger' : ($percent > 50 ? 'bg-warning' : 'bg-success');
    ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Tárhely felhasználás (Max: 50 MB)</h5>
            <div class="progress" style="height: 25px;">
                <div class="progress-bar <?= $color ?> progress-bar-striped progress-bar-animated" 
                     role="progressbar" style="width: <?= $percent ?>%">
                     <?= round($percent, 1) ?>%
                </div>
            </div>
            <small class="text-muted"><?= round($usedSpace / 1024 / 1024, 2) ?> MB felhasználva</small>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Fájljaid</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Név</th><th>Méret</th><th>Műveletek</th></tr></thead>
                        <tbody>
                            <?php foreach ($files as $f): ?>
                            <tr>
                                <td><?= htmlspecialchars($f['original_name']) ?></td>
                                <td><?= round($f['file_size'] / 1024, 1) ?> KB</td>
                                <td>
                                    <a href="index.php?url=download&id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary">Letöltés</a>
                                    <a href="index.php?url=share&id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-info">Megosztás</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Feltöltés</div>
                <div class="card-body">
                    <form action="index.php?url=upload" method="POST" enctype="multipart/form-data">
                        <input type="file" name="cloud_file" class="form-control mb-3" required>
                        <button type="submit" class="btn btn-primary w-100">Feltöltés a felhőbe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>