<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$expireAfter = 15 * 60;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $expireAfter) {
    session_unset();
    session_destroy();
    header("Location: login.php?message=Session expired");
    exit;
}
$_SESSION['last_activity'] = time();

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products_manage.php?error=invalid_id");
    exit;
}

$id = intval($_GET['id']);

if (deleteProduct($conn, $id)) {
    header("Location: products_manage.php?success=deleted");
} else {
    header("Location: products_manage.php?error=delete_failed");
}
exit;