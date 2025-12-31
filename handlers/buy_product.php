<?php
require_once("../config.php");

if (!User::isLoggedIn()) {
    header("Location: ../login.php");
    exit;
}

$user    = User::currentUser();
$userId  = $user["id"];
$productId = (int)($_POST["product_id"] ?? 0);
$quantity  = (int)($_POST["quantity"] ?? 1);

try {
    $orderId = Order::purchaseProduct($userId, $productId, $quantity);
    header("Location: ../pages/orders.php?order_id=" . $orderId);
    exit;
} catch (Exception $e) {
    header("Location: ../pages/ProductDetail.php?id=" . $productId . "&error=" . urlencode($e->getMessage()));
    exit;
}
