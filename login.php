<?php require_once "config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CHAIRWAY/Sing in</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<div class="container-fluid min-vh-100 d-flex align-items-center">
    <div class="row flex-grow-1 justify-content-center">
        <div class="col-12 col-md-5 d-flex align-items-center justify-content-center">
            <div class="w-90 w-md-75 px-3 px-md-0 mx-auto">
                <a href="index.php"><img src="Assets/Icons/back-button.png" alt="black back button" style="width:25px;height:25px;"></a>
                <h1 class="mb-3 text-center text-md-start">Welcome back 👋</h1>
                <p class="text-muted mb-4">
                    Please log in to continue shopping at <strong>Chairway</strong>.
                </p>

                <?php if (!empty($_SESSION["login_error"])): ?>
                    <div class="alert alert-danger">
                        <?php
                        echo htmlspecialchars($_SESSION["login_error"]);
                        unset($_SESSION["login_error"]);
                        ?>
                    </div>
                <?php endif; ?>

                <form action="handlers/login.php" method="POST" class="needs-validation w-100" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-2">Log in</button>
                </form>

                <p class="mt-3 small text-muted">
                    Don't have an account yet?
                    <a href="register.php">Register here</a>
                </p>
            </div>
        </div>
        <div class="col-md-6 d-none d-md-block p-0">
            <div class="h-100"
                 style="
                    background-image: url('Assets/Images/dillon-mangum-9489sFfgk4c-unsplash.jpg');
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
