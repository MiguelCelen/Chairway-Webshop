<?php
require_once "../config.php";

if (!User::isLoggedIn()) {
    header("Location: ../login.php");
    exit;
}

$user = User::currentUser();
$cart = $_SESSION["cart"] ?? [];
if (empty($cart)) {
    header("Location: ../pages/Winkelwagen.php?error=" . urlencode("Je winkelwagen is leeg."));
    exit;
}

$total        = 0.0;
$detailsParts = [];
foreach ($cart as $item) {
    $prijs  = isset($item["price"]) ? (float)$item["price"] : 0.0;
    $aantal = isset($item["quantity"]) ? (int)$item["quantity"] : 0;
    $titel  = isset($item["title"]) ? (string)$item["title"] : "Onbekend product";

    if ($aantal < 1) {
        continue;
    }

    $total += $prijs * $aantal;
    $detailsParts[] = $titel . " (x" . $aantal . ")";
}

if ($total <= 0 || empty($detailsParts)) {
    header("Location: ../pages/Winkelwagen.php?error=" . urlencode("Winkelwagen bevat geen geldige items."));
    exit;
}

$details = implode(", ", $detailsParts);
$pdo = Database::getConnection();
try {
    $pdo->beginTransaction();

    $check = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'balance'");
    $check->execute();
    $hasBalance = (bool)$check->fetch(PDO::FETCH_ASSOC);

    if ($hasBalance) {
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ? FOR UPDATE");
        $stmt->execute([(int)$user["id"]]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("Gebruiker niet gevonden.");
        }
        $balance = (float)$row["balance"];

        if ($balance < $total) {
            throw new Exception("Onvoldoende saldo. Je saldo is " . $balance . " en je totaal is " . $total . ".");
        }

        $newBalance = $balance - $total;

        $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $stmt->execute([$newBalance, (int)$user["id"]]);

        if (!isset($_SESSION["user"]) || !is_array($_SESSION["user"])) {
            $_SESSION["user"] = [];
        }
        $_SESSION["user"]["balance"] = $newBalance;
    }

    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total, details, created_at)
        VALUES (:user_id, :total, :details, NOW())
    ");
    $stmt->execute([
        ":user_id" => (int)$user["id"],
        ":total"   => $total,
        ":details" => $details,
    ]);

    $orderId = (int)$pdo->lastInsertId();

    $pdo->commit();

    $_SESSION["cart"] = [];

    header("Location: ../pages/orders.php?success=1&order_id=" . $orderId);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header("Location: ../pages/Winkelwagen.php?error=" . urlencode($e->getMessage()));
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header("Location: ../pages/Winkelwagen.php?error=" . urlencode("Database fout bij plaatsen order."));
    exit;
}
