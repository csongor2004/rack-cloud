<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RackCloud - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-cloud-check"></i> RackCloud Storage</h1>
            <p class="text-muted small">Biztonságos fájlkezelő rendszer</p>
        </div>
        <div class="text-end">
            <span class="badge bg-primary px-3 py-2 mb-2">Bejelentkezve: <?= htmlspecialchars($_SESSION['username']) ?></span><br>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="index.php?url=admin" class="btn btn-sm btn-dark"><i class="bi bi-shield-lock"></i> Admin Panel</a>
            <?php endif; ?>
            <a href="index.php?url=logout" class="btn btn-sm btn-outline-danger">Kijelentkezés</a>
        </div>
    </div>

    <?php
    $limit = App\Core\Config::get('app.storage_limit'); // 50MB
    $percent = ($usedSpace / $limit) * 100;
    $color = $percent > 85 ? 'bg-danger' : ($percent > 60 ? 'bg-warning' : 'bg-success');
    ?>
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <h6 class="card-title fw-bold mb-0">Tárhely felhasználás</h6>
                <span class="fw-bold"><?= round($percent, 1) ?>%</span>
            </div>
           <div class="progress mb-2" style="height: 12px; border-radius: 10px;">
    <div class="progress-bar <?= $color ?> progress-bar-striped progress-bar-animated" 
         role="progressbar" 
         style="width: <?= (int) $percent ?>"></div>
</div>
            <div class="d-flex justify-content-between">
                <small class="text-muted"><?= round($usedSpace / 1024 / 1024, 2) ?> MB felhasználva</small>
                <small class="text-muted">Összesen: 50 MB</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Fájljaid</h5>
                </div>
                
                <form id="batchActionForm" action="index.php?url=batch_action" method="POST">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                    <th>Fájlnév</th>
                                    <th>Méret</th>
                                    <th class="text-end">Műveletek</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($files)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted italic">Még nincsenek feltöltött fájljaid.</td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($files as $f): ?>
                                    <tr>
                                        <td><input type="checkbox" name="files[]" value="<?= $f['id'] ?>" class="form-check-input file-checkbox"></td>
                                        <td>
                                            <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                            <?= htmlspecialchars($f['original_name']) ?>
                                        </td>
                                        <td><small class="text-muted"><?= round($f['file_size'] / 1024, 1) ?> KB</small></td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="index.php?url=download&id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary" title="Letöltés"><i class="bi bi-download"></i></a>
                                                <a href="index.php?url=share&id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-info" title="Megosztás"><i class="bi bi-share"></i></a>
                                                <a href="index.php?url=delete&id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Biztosan törlöd?')" title="Törlés"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-white border-top-0 py-3">
                        <button type="submit" name="action" value="zip" class="btn btn-sm btn-secondary me-2">
                            <i class="bi bi-file-zip"></i> Kijelöltek letöltése (.zip)
                        </button>
                        <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger" onclick="return confirm('Biztosan törlöd a kijelölt fájlokat?')">
                            <i class="bi bi-trash"></i> Kijelöltek törlése
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-upload"></i> Feltöltés</h5>
                </div>
                <div class="card-body">
                    <form action="index.php?url=upload" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Válassz fájlt...</label>
                            <input type="file" name="cloud_file" class="form-control" required>
                            <div class="form-text small">Engedélyezett: jpg, png, pdf, zip, docx</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            Feltöltés indítása
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm border-0 mt-4 bg-info text-white">
                <div class="card-body p-3">
                    <small><i class="bi bi-info-circle-fill me-1"></i> A megosztott linkek 24 óráig érvényesek.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.file-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
</body>
</html>