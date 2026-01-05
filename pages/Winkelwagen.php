<?php
require_once "../config.php";

$user = User::currentUser();
$cart = $_SESSION["cart"] ?? [];

if (isset($_GET["clear"]) && $_GET["clear"] === "1") {
    $_SESSION["cart"] = [];
    header("Location: Winkelwagen.php");
    exit;
}

$total = 0;
foreach ($cart as $item) {
    $total += $item["price"] * $item["quantity"];
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CHAIRWAY/Winkelwagen</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

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
    <div class="container my-5">
        <h1 class="mb-4">Winkelwagen</h1>
        <?php if (!empty($_GET["error"])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars((string)$_GET["error"]) ?></div>
        <?php endif; ?>

        <?php if (empty($cart)): ?>
            <p>Je winkelwagen is leeg.</p>
            <a href="Artikelen.php" class="btn btn-dark">Verder winkelen</a>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle table-sm text-nowrap">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th class="d-none d-sm-table-cell">Prijs</th>
                        <th class="text-center">Aantal</th>
                        <th class="text-end">Subtotaal</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td class="d-flex align-items-center gap-2 text-truncate"">
                                <?php if (!empty($item["image"])): ?>
                                    <img src="<?= htmlspecialchars((string)$item["image"]) ?>" alt=""
                                        class="img-fluid" style="height:40px; max-width:40px;">
                                <?php endif; ?>
                                <?= htmlspecialchars((string)$item["title"]) ?>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                € <?= number_format((float)$item["price"], 2, ",", ".") ?>
                            </td>
                            <td class="text-center"><?= (int)$item["quantity"] ?></td>
                            <td class="text-end">€ <?= number_format($item["price"] * $item["quantity"], 2, ",", ".") ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <h4 class="mt-3">Totaal: € <?= number_format($total, 2, ",", ".") ?></h4>
            <div class="mt-4 d-flex flex-column flex-md-row align-items-stretch gap-3">
                <div class="d-flex flex-column flex-md-row gap-2">
                    <a href="Artikelen.php" class="btn btn-secondary">Verder winkelen</a>
                    <a href="Winkelwagen.php?clear=1" class="btn btn-outline-danger"
                    onclick="return confirm('Winkelwagen leegmaken?');">
                        Leeg winkelwagen
                    </a>
                </div>
                <?php if ($user): ?>
                    <form method="post" action="../handlers/place_order.php" class="ms-md-auto w-100 w-md-auto d-flex">
                        <button type="submit" class="btn btn-success px-4 py-2 w-100">
                            Bestel nu
                        </button>
                    </form>
                <?php else: ?>
                    <a href="../login.php" class="btn btn-success ms-auto">Log in om te bestellen</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
<footer class="text-center p-3 mt-5">
    © 2025 Copyright: Chairway
</footer>
</body>
</html>
