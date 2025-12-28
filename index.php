<?php
require_once "config.php";
$user = User::currentUser();

?><!DOCTYPE html>
    <html lang="nl">
    <head>
        <meta charset="UTF-8">
        <title>CHAIRWAY/Home</title>
        <link rel="stylesheet" href="public/css/style.css">
        <script defer src="public/js/script.js"></script>
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </head>
    <body>
      <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
              <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">CHAIRWAY</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                        aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                  <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                      <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="pages/Artikelen.php">Products</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="pages/Winkelwagen.php">Shoppingcart</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="pages/orders.php">Orders</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="pages/ContactUs.php">Contact us</a>
                    </li>
                  </ul>

                  <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if ($user): ?>
                      <li class="nav-item d-flex align-items-center me-2">
                        <span class="navbar-text small">
                          Hallo, <?= htmlspecialchars((string)($user["name"] ?? $user["email"])) ?>
                        </span>
                      </li>

                      <?php if (User::isAdmin()): ?>
                        <li class="nav-item">
                          <a class="nav-link" href="admin_products.php">Changes</a>
                        </li>
                      <?php endif; ?>

                      <li class="nav-item">
                        <a class="nav-link" href="handlers/logout.php">Logout</a>
                      </li>
                    <?php else: ?>
                      <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                      </li>
                    <?php endif; ?>
                  </ul>
                </div>
              </div>
            </nav>
            <div class="row">
              <img src="Assets/Images/dillon-mangum-9489sFfgk4c-unsplash.jpg" alt="Hero Image" style="width: 100%; height: auto;">
            </div>
      </header>
    </body>
</html>