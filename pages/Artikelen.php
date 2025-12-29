<?php
require_once "../config.php";
$pdo  = Database::getConnection();
$user = User::currentUser();

$search    = isset($_GET["q"])        ? trim($_GET["q"])        : "";
$category  = isset($_GET["category"]) ? trim($_GET["category"]) : "";
$maxPrice  = isset($_GET["maxPrice"]) ? (float)$_GET["maxPrice"] : 0;

$sql = "SELECT id, title, price, category, image, description 
        FROM products
        WHERE 1 = 1";
$params = [];

if ($search !== "") {
    $sql .= " AND title LIKE ?";
    $params[] = "%".$search."%";
}

if ($category !== "") {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if ($maxPrice > 0) {
    $sql .= " AND price <= ?";
    $params[] = $maxPrice;
}

$sql .= " ORDER BY id ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$catStmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>CHAIRWAY/Products</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

    <div class="row" style="background: #b9b9b9; height: 500px;"></div>
</header>
</body>
</html>