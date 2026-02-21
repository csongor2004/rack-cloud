<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>RackCloud - Bejelentkezés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="min-height: 100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h2 class="text-center mb-4 fw-bold text-primary">RackCloud</h2>
                    
                    <?php if (isset($_GET['registered'])): ?>
                        <div class="alert alert-success small py-2 text-center">Sikeres regisztráció! Jelentkezz be!</div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger small py-2 text-center"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">Felhasználónév</label>
                            <input type="text" name="username" class="form-control form-control-lg" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold text-uppercase">Jelszó</label>
                            <input type="password" name="password" class="form-control form-control-lg" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm mb-3">Belépés</button>
                        <div class="text-center">
                            <a href="index.php?url=register" class="text-decoration-none small text-secondary">Nincs még fiókod? Regisztrálj itt!</a>
                        </div>
                    </form>
                    <div class="mt-4 text-center small text-muted border-top pt-3">
                        Rackhost Internship Project 2026
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>