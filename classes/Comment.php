<?php

class Comment {
    public static function add(int $userId, int $productId, int $rating, string $body): array
    {
        $pdo = Database::getConnection();

        $rating = max(1, min(5, $rating));
        $body = trim($body);

        if ($body === "") {
            throw new Exception("Comment mag niet leeg zijn.");
        }

        $stmt = $pdo->prepare("
            INSERT INTO comments (user_id, product_id, rating, body)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $productId, $rating, $body]);

        $id = (int)$pdo->lastInsertId();

        $stmt = $pdo->prepare("
            SELECT c.*, u.name, u.email
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function forProduct(int $productId): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT c.*, u.name, u.email
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.product_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$productId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
