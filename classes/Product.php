<?php
class Product {
    public static function all(?int $categoryId = null, ?string $search = null): array
    {
        $pdo = Database::getConnection();

        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE 1";
        $params = [];

        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        if ($search) {
            $sql .= " AND p.title LIKE ?";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        return $product ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO products (title, price, image, description, category_id)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data["title"],
            $data["price"],
            $data["image"] ?? null,
            $data["description"] ?? null,
            $data["category_id"] ?: null,
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE products
            SET title = ?, price = ?, image = ?, description = ?, category_id = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data["title"],
            $data["price"],
            $data["image"] ?? null,
            $data["description"] ?? null,
            $data["category_id"] ?: null,
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function categories(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
