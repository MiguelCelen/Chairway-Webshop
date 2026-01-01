<?php
require_once "../config.php";

if (!User::isLoggedIn()) {
    $productId = (int)($_POST["product_id"] ?? 0);
    header("Location: ../login.php?redirect=" . urlencode("../pages/ProductDetail.php?id=" . $productId));
    exit;
}

$productId = (int)($_POST["product_id"] ?? 0);
$quantity  = (int)($_POST["quantity"] ?? 1);
$quantity  = max(1, $quantity);

if ($productId <= 0) {
    header("Location: ../pages/Artikelen.php?error=" . urlencode("Ongeldig product."));
    exit;
}

$pdo = Database::getConnection();
$stmt = $pdo->prepare("SELECT id, title, price, image FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: ../pages/Artikelen.php?error=" . urlencode("Product niet gevonden."));
    exit;
}

$_SESSION["cart"] = [
    $productId => [
        "id"       => (int)$product["id"],
        "title"    => (string)$product["title"],
        "price"    => (float)$product["price"],
        "image"    => (string)($product["image"] ?? ""),
        "quantity" => $quantity,
    ]
];

header("Location: ../handlers/place_order.php");
exit;
