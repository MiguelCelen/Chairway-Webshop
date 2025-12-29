<?php
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/Artikelen.php");
    exit;
}

$productId = isset($_POST["product_id"]) ? (int)$_POST["product_id"] : 0;
$quantity  = isset($_POST["quantity"]) ? (int)$_POST["quantity"] : 1;

if ($productId <= 0 || $quantity <= 0) {
    header("Location: ../pages/Artikelen.php?error=" . urlencode("Ongeldige productgegevens."));
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

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

if (isset($_SESSION["cart"][$productId])) {
    $_SESSION["cart"][$productId]["quantity"] += $quantity;
} else {
    $_SESSION["cart"][$productId] = [
        "id"       => $product["id"],
        "title"    => $product["title"],
        "price"    => (float)$product["price"],
        "image"    => $product["image"],
        "quantity" => $quantity,
    ];
}

header("Location: ../pages/Artikelen.php?cart=added");
exit;
