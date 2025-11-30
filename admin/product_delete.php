<?php
require_once "../includes/db_connect.php";
require_once "../includes/functions.php";

$id = $_GET['id'];

deleteProduct($conn, $id);

header("Location: products_manage.php");
exit;
