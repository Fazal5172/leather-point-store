<?php

require_once __DIR__ . "/../../config/bootstrap.php";
require_once __DIR__ . "/../../classes/User.php";
require_once __DIR__ . "/../../classes/Product.php";
require_once __DIR__ . "/../../classes/Order.php";
require_once __DIR__ . "/../../classes/Review.php";

// Authorization Security check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php?redirect=admin/dashboard.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$db_connected = ($db !== null);

// Class references
$userObj = new User($db);
$productObj = new Product($db);
$orderObj = new Order($db);
$reviewObj = new Review($db);
?>
