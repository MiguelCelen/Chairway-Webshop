<?php
require_once("../config.php");

try {
    User::login($_POST["email"], $_POST["password"]);
    header("Location: ../index.php");
    exit;
} catch (Exception $e) {
    $_SESSION["login_error"] = $e->getMessage();
    header("Location: ../login.php");
    exit;
}
