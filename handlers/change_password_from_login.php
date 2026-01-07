<?php
require_once("../config.php");

try {
    $email   = $_POST["email"] ?? "";
    $current = $_POST["current_password"] ?? "";
    $new     = $_POST["new_password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if (trim($new) !== trim($confirm)) {
        throw new Exception("Nieuwe wachtwoorden komen niet overeen.");
    }

    User::changePasswordByEmail($email, $current, $new);

    $_SESSION["login_success"] = "Wachtwoord gewijzigd. Je kan nu inloggen.";
    header("Location: ../login.php");
    exit;

} catch (Exception $e) {
    $_SESSION["password_error"] = $e->getMessage();
    header("Location: ../change_password.php");
    exit;
}
