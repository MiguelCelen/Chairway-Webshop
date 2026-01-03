<?php
require_once("../config.php");

if (!User::isAdmin()) {
    http_response_code(403);
    exit("Forbidden");
}

$id          = (int)($_POST["id"] ?? 0);
$title       = trim($_POST["title"] ?? "");
$price       = (float)($_POST["price"] ?? 0);
$image       = trim($_POST["image"] ?? "");
$description = trim($_POST["description"] ?? "");
$category_id = !empty($_POST["category_id"]) ? (int)$_POST["category_id"] : null;

if ($id <= 0 || $title === "" || $price <= 0) {
    header("Location: ../admin_products.php?error=" . urlencode("Ongeldige data."));
    exit;
}

Product::update($id, [
    "title"       => $title,
    "price"       => $price,
    "image"       => $image,
    "description" => $description,
    "category_id" => $category_id,
]);

header("Location: ../admin_products.php?success=1");
exit;
