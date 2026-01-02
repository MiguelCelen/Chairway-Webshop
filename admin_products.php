<?php
require_once "config.php";

if (!User::isLoggedIn() || !User::isAdmin()) {
    header("Location: login.php");
    exit;
}

$pdo = Database::getConnection();
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "add") {
        $title       = trim($_POST["title"] ?? "");
        $price       = (float)($_POST["price"] ?? 0);
        $category    = trim($_POST["category"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $image       = "";

        if ($title === "" || $price <= 0) {
            $error = "Titel en prijs zijn verplicht.";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO products (title, price, category, image, description)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $price, $category, $image, $description]);
            $success = "Product toegevoegd.";
        }

    } 
    elseif ($action === "update") {
        $id          = (int)($_POST["id"] ?? 0);
        $title       = trim($_POST["title"] ?? "");
        $price       = (float)($_POST["price"] ?? 0);
        $category    = trim($_POST["category"] ?? "");
        $description = trim($_POST["description"] ?? "");

        if ($id <= 0 || $title === "" || $price <= 0) {
            $error = "Ongeldige productgegevens.";
        } else {
            $stmt = $pdo->prepare("
                UPDATE products
                SET title = ?, price = ?, category = ?, description = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $price, $category, $description, $id]);
            $success = "Product bijgewerkt.";
        }

    } 
    elseif ($action === "delete") {
        $id = (int)($_POST["id"] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Product verwijderd.";
        } else {
            $error = "Ongeldig product.";
        }
    }
}

$editProduct = null;
if (isset($_GET["edit"])) {
    $editId = (int)$_GET["edit"];
    if ($editId > 0) {
        $stmt = $pdo->prepare("
            SELECT id, title, price, category, description
            FROM products
            WHERE id = ?
        ");
        $stmt->execute([$editId]);
        $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$stmt = $pdo->query("
    SELECT id, title, price, category
    FROM products
    ORDER BY id ASC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user = User::currentUser();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Producten beheren</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
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
        <li class="nav-item"><a class="nav-link" href="pages/Artikelen.php">Products</a></li>
        <li class="nav-item"><a class="nav-link active" href="admin_products.php">Admin producten</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_product_images.php">Product afbeeldingen</a></li>
      </ul>
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item d-flex align-items-center me-2">
            <span class="navbar-text small">
                <?= htmlspecialchars((string)("Hallo, " . $user["name"] ?? "")) ?>
            </span>
        </li>
        <li class="nav-item"><a class="nav-link" href="handlers/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
</body>
</html>