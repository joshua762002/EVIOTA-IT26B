<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "thegamezone");
$id = $_GET['id'];
$conn->query("UPDATE addgames SET deleted_at = NULL WHERE id = $id");
header("Location: read.php");
