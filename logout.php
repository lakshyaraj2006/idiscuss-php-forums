<?php
require_once "constants.php";

if (!isset($_GET["path"])) {
    $path = "/forum/";
} else {
    $path = $_GET["path"];
}

// This script will handle logout
session_start();

// Check if the user is already loggedin
if (!isset($_SESSION["user"])) {
    header("Location: " . $path);
    exit;
} else {
    $_SESSION = array();
    unset($_SESSION["user"]);
    $_SESSION["loggedin"] = false;
    $_SESSION["alert"] = [
        "message" => "Logged out successfully",
        "type" => ALERT_SUCCESS
    ];
    header("Location: " . $path);
    exit;
}
?>