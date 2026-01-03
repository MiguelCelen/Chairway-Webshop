<?php
require_once("../config.php");

if (!User::isAdmin()) {
    http_response_code(403);
    exit("Forbidden");
}

$id = (int)($_GET["id"] ?? 0);

if ($id > 0) {
    Product::delete($id);
}

header("Location: ../admin_products.php");
exit;
