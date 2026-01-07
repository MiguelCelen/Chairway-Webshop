<?php
require_once("../config.php");

$name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? (string)$_POST["password"] : "";

$_SESSION["register_old"] = [
    "name" => $name,
    "email" => $email,
];

try {
    if ($email === "" || $password === "") {
        throw new Exception("Please enter your email address and password.");
    }

    User::register($email, $password, $name);
    unset($_SESSION["register_old"]);
    $_SESSION["register_success"] = "Account created! You can now log in.";
    header("Location: ../login.php");
    exit;

} catch (Exception $e) {
    $_SESSION["register_error"] = $e->getMessage();
    header("Location: ../register.php");
    exit;
}
