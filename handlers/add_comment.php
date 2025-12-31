<?php
require_once "../config.php";

header("Content-Type: application/json; charset=UTF-8");

if (!User::isLoggedIn()) {
    echo json_encode(["success" => false, "error" => "Je moet ingelogd zijn om een comment te plaatsen."]);
    exit;
}

$user = User::currentUser();
$pdo  = Database::getConnection();

$productId = isset($_POST["product_id"]) ? (int)$_POST["product_id"] : 0;
$rating    = isset($_POST["rating"]) ? (int)$_POST["rating"] : 5;
$message   = trim($_POST["message"] ?? "");

if ($productId <= 0) {
    echo json_encode(["success" => false, "error" => "Ongeldig product."]);
    exit;
}

if ($rating < 1 || $rating > 5) {
    $rating = 5;
}

if ($message === "") {
    echo json_encode(["success" => false, "error" => "Comment mag niet leeg zijn."]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO comments (user_id, product_id, message, rating, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$user["id"], $productId, $message, $rating]);

    $author = (string)($user["name"] ?? $user["email"] ?? "Gebruiker");
    $createdAt = date("Y-m-d H:i:s");

    echo json_encode([
        "success" => true,
        "comment" => [
            "author" => $author,
            "rating" => $rating,
            "message" => $message,
            "created_at" => $createdAt
        ]
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "DB-fout: " . $e->getMessage()]);
    exit;
}
