<?php
require_once("../config.php");
try {
    User::register($_POST["email"], $_POST["password"], $_POST["name"]);
    echo "Account aangemaakt! Je kan nu inloggen.";
} catch (Exception $e) {
    echo "Registratie fout: " . $e->getMessage();
}
