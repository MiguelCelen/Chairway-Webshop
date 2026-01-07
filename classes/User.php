<?php

class User
{
    
    public static function register(string $email, string $password, ?string $name = null): void
    {
        $pdo = Database::getConnection();

        $email = trim($email);
        $password = trim($password);
        $name = $name !== null ? trim($name) : null;

        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            throw new Exception("Er bestaat al een account met dit e-mailadres.");
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            "INSERT INTO users (email, password, name) VALUES (?, ?, ?)"
        );
        $stmt->execute([$email, $hashed, $name]);
    }

    public static function login(string $email, string $password): void
    {
        $pdo = Database::getConnection();

        $email = trim($email);
        $password = trim($password);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Geen account gevonden met dit e-mailadres. Maak eerst een account aan.");
        }

        if (!password_verify($password, $user["password"])) {
            throw new Exception("Wachtwoord is onjuist.");
        }

        unset($user["password"]);
        $_SESSION["user"] = $user;
    }

    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION["user"]);
    }

    public static function currentUser(): ?array
    {
        return self::isLoggedIn() ? $_SESSION["user"] : null;
    }

    public static function isAdmin(): bool
    {
        return self::isLoggedIn() && !empty($_SESSION["user"]["is_admin"]);
    }

    public static function logout(): void
    {
        unset($_SESSION["user"]);
    }

    public static function changePasswordByEmail(string $email, string $currentPassword, string $newPassword): void
{
    $pdo = Database::getConnection();

    $email = trim($email);
    $currentPassword = trim($currentPassword);
    $newPassword = trim($newPassword);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Please provide a valid email address.");
    }

    if (strlen($newPassword) < 6) {
        throw new Exception("New password must contain at least 6 characters.");
    }

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !password_verify($currentPassword, $row["password"])) {
        throw new Exception("Email address or current password is incorrect.");
    }

    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $upd->execute([$hashed, (int)$row["id"]]);
}

}
