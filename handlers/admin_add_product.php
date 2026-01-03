<?php
require_once("../config.php");

if (!User::isAdmin()) {
    http_response_code(403);
    exit("Forbidden");
}

$title       = trim($_POST["title"] ?? "");
$price       = (float)($_POST["price"] ?? 0);
$image       = trim($_POST["image"] ?? "");
$description = trim($_POST["description"] ?? "");
$category_id = !empty($_POST["category_id"]) ? (int)$_POST["category_id"] : null;

if ($title === "" || $price <= 0) {
    header("Location: ../admin_products.php?error=" . urlencode("Titel en prijs zijn verplicht."));
    exit;
}

Product::create([
    "title"       => $title,
    "price"       => $price,
    "image"       => $image,
    "description" => $description,
    "category_id" => $category_id,
]);

header("Location: ../admin_products.php?success=1");
exit;
