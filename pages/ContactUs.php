<?php
require_once "../config.php";
$user = User::currentUser();

?><!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CHAIRWAY/Contact</title>
        <link rel="stylesheet" href="../public/css/style.css">
        <script defer src="../public/js/script.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
              <div class="container">
                <a class="navbar-brand fw-bold" href="../index.php">CHAIRWAY</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                        aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                  <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                      <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="Artikelen.php">Products</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="Winkelwagen.php">Shoppingcart</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="orders.php">Orders</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="ContactUs.php">Contact us</a>
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
                          <a class="nav-link" href="../admin_products.php">Changes</a>
                        </li>
                      <?php endif; ?>

                      <li class="nav-item">
                        <a class="nav-link" href="../handlers/logout.php">Logout</a>
                      </li>
                    <?php else: ?>
                      <li class="nav-item">
                        <a class="nav-link" href="../login.php">Login</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="../register.php">Register</a>
                      </li>
                    <?php endif; ?>
                  </ul>
                </div>
              </div>
            </nav>
      </header>
      <main>
          <div class="container">
            <div class="row g-2" style="margin-top: 1rem; margin-bottom: 5rem;">
              <div class="col-12 col-md-8 col-lg-6 mx-md-auto d-flex justify-content-center align-items-center mb-4 mb-lg-0">
                <img src="../Assets/Images/pawel-chu-ULh0i2txBCY-unsplash.jpg" alt="Contact us" class="img-fluid rounded" style="max-width: 400px;">
              </div>
              <div class="col-12 col-md-8 col-lg-6 mx-md-auto">
                <h2>Contact Us</h2>
                <form action="https://formspree.io/f/movqzwjy" method="POST">
                  <div class="mb-3">
                    <label for="fullName" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Enter your full name" required>
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                  </div>
                  <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Write your message" required></textarea>
                  </div>
                  <div class="d-grid">
                  <button type="submit" class="btn btn-dark" style="max-width: 500px;">Send</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </main>
        <footer>
          <div class="row g-2"></div>
          <div class="text-center p-3" style="margin-top: 5rem;">
            © 2026 Copyright: Chairway
        </footer>
    </body>
    </html>