<?php
require_once("../config.php");

User::logout();
header("Location: ../index.php");
exit;
