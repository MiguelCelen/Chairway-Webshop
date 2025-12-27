<?php

class Order {
    
    public static function purchaseProduct(int $userId, int $productId, int $quantity = 1): int
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("Gebruiker niet gevonden.");
            }

            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception("Product niet gevonden.");
            }

            $quantity = max(1, $quantity);
            $total = $product["price"] * $quantity;

            if ($user["balance"] < $total) {
                throw new Exception("Onvoldoende saldo.");
            }

            $newBalance = $user["balance"] - $total;
            $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->execute([$newBalance, $userId]);

            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
            $stmt->execute([$userId, $total]);
            $orderId = (int)$pdo->lastInsertId();

            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price_each)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$orderId, $productId, $quantity, $product["price"]]);

            $pdo->commit();

            $_SESSION["user"]["balance"] = $newBalance;

            return $orderId;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function ordersForUser(int $userId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
