<?php 
require_once "config.php"; 
$registerError = $_SESSION["register_error"] ?? null;
$registerSuccess = $_SESSION["register_success"] ?? null;
$old = $_SESSION["register_old"] ?? ["name" => "", "email" => ""];

unset($_SESSION["register_error"], $_SESSION["register_success"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="Assets/Images/logo_chairway.png" type="icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CHAIRWAY/Register</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<div class="container-fluid min-vh-100 d-flex align-items-center">
    <div class="row flex-grow-1 w-100 justify-content-center">
        <div class="col-12 col-md-8 col-lg-5 d-flex align-items-center justify-content-center">
            <div class="w-90 w-md-75 px-3 px-md-0 mx-auto" style="max-width: 480px;">
                <a href="index.php"><img src="Assets/Icons/back-button.png" alt="black back button" style="width:25px;height:25px;"></a>
                <h1 class="mb-3">Create an account 🪑</h1>
                <p class="text-muted mb-4">
                    Register with <strong>Chairway</strong> to start shopping.
                </p>

                <?php if ($registerError): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($registerError) ?>
                    </div>
                <?php endif; ?>

                <?php if ($registerSuccess): ?>
                    <div class="alert alert-success" role="alert">
                        <?= htmlspecialchars($registerSuccess) ?>
                    </div>
                <?php endif; ?>

                <form action="handlers/register.php" method="POST" class="needs-validation w-100" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback">Please enter your name.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">Enter a password.</div>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 py-2">Create account</button>
                </form>
                
                <p class="mt-3 small text-muted">
                    Already have an account?
                    <a href="login.php">Log in here</a>
                </p>
            </div>
        </div>
        <div class="col-lg-6 d-none d-lg-block p-0">
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
