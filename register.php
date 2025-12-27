<?php require_once "config.php"; ?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registreren - Chairway</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<div class="container-fluid min-vh-100 d-flex align-items-center">
    <div class="row flex-grow-1">
        <div class="col-md-5 d-flex align-items-center justify-content-center">
            <div class="w-75">
                <a href="index.php"><img src="Assets/Icons/back-button.png" alt="black back button" style="width:25px;height:25px;"></a>
                <h1 class="mb-3">Maak een account aan 🪑</h1>
                <p class="text-muted mb-4">
                    Registreer je bij <strong>Chairway</strong> om te starten met shoppen.
                </p>

                <form action="handlers/register.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Naam</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback">Vul je naam in.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mailadres</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Vul een geldig e-mailadres in.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Wachtwoord</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">Vul een wachtwoord in.</div>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Account aanmaken</button>
                </form>
                
                <p class="mt-3 small text-muted">
                    Al een account?
                    <a href="login.php">Log hier in</a>
                </p>
            </div>
        </div>
        <div class="col-md-6 d-none d-md-block p-0">
            <div class="h-100"
                 style="
                    background-image: url('Assets/Images/pawel-chu-ULh0i2txBCY-unsplash.jpg');
                    background-size: cover;
                    background-position: center;
                 ">
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

</body>
</html>
