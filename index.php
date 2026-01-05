<?php
require_once "config.php";
$user = User::currentUser();

?><!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
                      <a class="nav-link" href="pages/Winkelwagen.php">Shopping cart</a>
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
                          Hello, <?= htmlspecialchars((string)($user["name"] ?? $user["email"])) ?>
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
        
        <div class="container">
        <main>
            <div class="row g-2" style="margin-top: 5rem;">
              <div class="col-12 col-sm-6 col-lg-3 d-flex flex-column align-items-center text-center">
                <img src="Assets/Icons/Puzzle.svg" alt="About us" style="width: 25%; height: auto;">
                <h6>Quality and Durability</h6>
                <p>Offer high-quality, durable furniture made from premium materials. Skilled craftsmanship and strict quality checks build trust and ensure long-lasting satisfaction.</p>
              </div>
              <div class="col-12 col-sm-6 col-lg-3 d-flex flex-column align-items-center text-center">
                <img src="Assets/Icons/Swatches_Palette.svg" alt="About us" style="width: 25%; height: auto;">
                <h6>Innovative and Customizable Designs</h6>
                <p>Provide unique, stylish, and functional designs with options for customization, allowing customers to personalize furniture to fit their tastes and needs.</p>
              </div>
              <div class="col-12 col-sm-6 col-lg-3 d-flex flex-column align-items-center text-center">
                <img src="Assets/Icons/Leaf.svg" alt="About us" style="width: 25%; height: auto;">
                <h6>Sustainability</h6>
                <p>Use eco-friendly materials and sustainable manufacturing processes. Offering recyclable or reusable furniture appeals to environmentally conscious consumers.</p>
              </div>
              <div class="col-12 col-sm-6 col-lg-3 d-flex flex-column align-items-center text-center">
                <img src="Assets/Icons/Chat_Conversation_Circle.svg" alt="About us" style="width: 25%; height: auto;">
                <h6>Exceptional Customer Service</h6>
                <p>Prioritize excellent customer service with smooth online shopping, reliable delivery, easy returns, and helpful post-purchase support, ensuring customer loyalty and satisfaction.</p>
              </div>
            </div>

            <div class="row g-2" style="margin-top: 5rem;">
              <div class="col-12 col-lg-6 d-flex justify-content-center align-items-center mb-3 mb-lg-0">
                <img src="Assets/Images/pawel-chu-ULh0i2txBCY-unsplash.jpg" alt="About us" style="width: 80%; height: auto; border-radius: 10px">
              </div>
              <div class="col-12 col-lg-6">
                <h2>About us</h2>
                <p>
                  At Chairway, we understand that furniture is an extension of your personality and lifestyle. That’s why our collections are thoughtfully designed to offer a blend of timeless classics and contemporary trends, ensuring there’s something for everyone. From cozy chairs to complete room solutions, we aim to inspire and elevate your living spaces.

                  Sustainability is at the heart of everything we do. We take pride in using responsibly sourced materials and eco-conscious manufacturing practices, creating pieces that are not only beautiful but also kind to the planet. <br><br>
                  
                  Our mission is to make your furniture journey seamless and enjoyable. With customizable options, reliable delivery, and a dedicated customer support team, we’re here to turn your dream spaces into reality.
                  
                  Discover the art of exceptional living with Chairway—your partner in crafting spaces you’ll love for years to come.
                </p>
              </div>
            </div>

            <div id="top-selling-container" class="row" style="margin-top: 5rem;">
            </div>

            <div class="row g-2">
              <div class="col-12 col-lg-4 d-flex justify-content-center align-items-center mb-3 mb-lg-0">
                <img src="Assets/Images/mail.png" class="img-fluid w-50 rounded" alt="Get in contact">
              </div>
              <div class="col-12 col-lg-8">
                <h2>Let's get in contact</h2>
                <p>
                 Do you have a question about our furniture, need styling advice, or want help finding 
                 the perfect piece for your space? We’re here to help every step of the way. Whether you’re 
                 furnishing a single room or redesigning your entire home, our team is happy to think along with you. 
                 Get in touch with us and let’s create a home that feels just right — where quality, comfort, and timeless design come together to reflect your personal style.
                </p>
                <a href="pages/ContactUs.php">
                  <button type="button" class="btn btn-dark">Get in contact</button>
                </a>
              </div>
            </div>
          </div>
        </main>

        <footer>
          <div class="row g-2"></div>
          <div class="text-center p-3" style="margin-top: 5rem;">
            © 2026 Copyright: Chairway
          </div>
        </footer>

        <script src="topSellingItems.js"></script>
    </body>
</html>