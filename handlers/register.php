<?php
require_once("../config.php");
try {
    User::register($_POST["email"], $_POST["password"], $_POST["name"]);
    header("Location: ../index.php");
    exit;
} catch (Exception $e) {
    echo "Registratie fout: " . $e->getMessage();
}
