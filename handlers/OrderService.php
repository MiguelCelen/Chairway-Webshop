<?php

class OrderService
{
    private PDO $pdo;
    private static ?bool $hasBalanceColumnCache = null;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    public function placeOrderFromCart(int $userId, array $cart): array
    {
        [$total, $details] = $this->buildTotalAndDetails($cart);

        $newBalance = null;

        $this->pdo->beginTransaction();
        try {
            if ($this->usersHasBalanceColumn()) {
                $stmt = $this->pdo->prepare("SELECT balance FROM users WHERE id = ? FOR UPDATE");
                $stmt->execute([$userId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$row) {
                    throw new Exception("Gebruiker niet gevonden.");
                }

                $balance = (float)$row["balance"];

                if ($balance < $total) {
                    throw new Exception("Onvoldoende saldo. Je saldo is {$balance} en je totaal is {$total}.");
                }

                $newBalance = $balance - $total;

                $stmt = $this->pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
                $stmt->execute([$newBalance, $userId]);
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO orders (user_id, total, details, created_at)
                VALUES (:user_id, :total, :details, NOW())
            ");
            $stmt->execute([
                ":user_id" => $userId,
                ":total"   => $total,
                ":details" => $details,
            ]);

            $orderId = (int)$this->pdo->lastInsertId();

            $this->pdo->commit();

            return [
                "orderId"    => $orderId,
                "total"      => $total,
                "details"    => $details,
                "newBalance" => $newBalance,
            ];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function buildTotalAndDetails(array $cart): array
    {
        if (empty($cart)) {
            throw new Exception("Je winkelwagen is leeg.");
        }

        $total = 0.0;
        $parts = [];

        foreach ($cart as $item) {
            $prijs  = isset($item["price"]) ? (float)$item["price"] : 0.0;
            $aantal = isset($item["quantity"]) ? (int)$item["quantity"] : 0;
            $titel  = isset($item["title"]) ? (string)$item["title"] : "Onbekend product";

            if ($aantal < 1) {
                continue;
            }

            $total += $prijs * $aantal;
            $parts[] = $titel . " (x" . $aantal . ")";
        }

        if ($total <= 0 || empty($parts)) {
            throw new Exception("Winkelwagen bevat geen geldige items.");
        }

        return [$total, implode(", ", $parts)];
    }

    private function usersHasBalanceColumn(): bool
    {
        if (self::$hasBalanceColumnCache !== null) {
            return self::$hasBalanceColumnCache;
        }

        $stmt = $this->pdo->prepare("SHOW COLUMNS FROM users LIKE 'balance'");
        $stmt->execute();
        self::$hasBalanceColumnCache = (bool)$stmt->fetch(PDO::FETCH_ASSOC);

        return self::$hasBalanceColumnCache;
    }
}
