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
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
<main class="container my-5" style="margin-top: 6rem;">
    <h1 class="mb-4">Products</h1>

    <?php if (!empty($_GET["cart"]) && $_GET["cart"] === "added"): ?>
        <div class="alert alert-success">Product toegevoegd aan Shoppingcart.</div>
    <?php endif; ?>

    <?php if (!empty($_GET["error"])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string)$_GET["error"]) ?></div>
    <?php endif; ?>

    <form class="row g-3 mb-4" method="get" action="Artikelen.php">
        <div class="col-12 col-md-3">
            <label class="form-label">Product-name</label>
            <input type="text" name="q" class="form-control"
                   placeholder="Typ a product-name"
                   value="<?= htmlspecialchars($search) ?>">
        </div>

        <div class="col-12 col-md-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
                <option value="">Alle categorieën</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"
                        <?= $cat === $category ? "selected" : "" ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 col-md-3">
            <label class="form-label">Max. prijs (€)</label>
            <input type="number" name="maxPrice" class="form-control"
                   value="<?= $maxPrice > 0 ? htmlspecialchars((string)$maxPrice) : "" ?>"
                   min="0" step="1">
        </div>

        <div class="col-12 col-md-3 d-flex align-items-end">
            <button class="btn btn-dark w-100" type="submit">Filter</button>
        </div>
    </form>

    <div class="row">
        <?php if (empty($products)): ?>
            <p>Er zijn geen producten gevonden met deze filter.</p>
        <?php else: ?>
            <?php foreach ($products as $p): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($p["image"])): ?>
                            <img src="<?= htmlspecialchars((string)$p["image"]) ?>" class="card-img-top img-fluid"
                                 alt="<?= htmlspecialchars((string)$p["title"]) ?>">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars((string)$p["title"]) ?></h5>
                            <p class="card-text">
                                Categorie: <?= htmlspecialchars((string)$p["category"]) ?><br>
                                <strong>€ <?= number_format((float)$p["price"], 2, ",", ".") ?></strong><br>
                                <?= nl2br(htmlspecialchars((string)$p["description"])) ?>
                            </p>
                            <div class="mt-auto">
                                <a href="ProductDetail.php?id=<?= $p["id"] ?>" class="btn btn-outline-dark w-100 mb-2">
                                    View product
                                </a>

                                <form method="post" action="../handlers/add_to_cart.php" class="d-flex flex-wrap gap-2">
                                    <input type="hidden" name="product_id" value="<?= $p["id"] ?>">
                                    <input type="number" name="quantity" value="1" min="1"
                                           class="form-control form-control-sm me-2" style="max-width: 80px;">
                                    <button type="submit" class="btn btn-dark btn-sm w-100">
                                        Add to cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
<footer>
    <div class="text-center p-3" style="margin-top: 5rem;">
        © 2025 Copyright: Chairway
    </div>
</footer>
</body>
</html>