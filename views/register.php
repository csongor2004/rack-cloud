<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>RackCloud - Regisztráció</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="min-height: 100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h2 class="text-center mb-4 fw-bold text-primary">Regisztráció</h2>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger small py-2"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?url=register_action">
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">Felhasználónév</label>
                            <input type="text" name="username" class="form-control form-control-lg" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold text-uppercase">Jelszó</label>
                            <input type="password" name="password" class="form-control form-control-lg" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm mb-3">Fiók létrehozása</button>
                        <div class="text-center">
                            <a href="index.php?url=login" class="text-decoration-none small text-secondary">Már van fiókom, belépek</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>