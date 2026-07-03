<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../config/Database.php";
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LPS - Admin Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        leather: {
                            50: '#fbf8f5',
                            100: '#f5ebe1',
                            200: '#ebd8c3',
                            300: '#dbbe9e',
                            400: '#c59b73',
                            500: '#b48154',
                            600: '#a57048',
                            700: '#895a3a',
                            800: '#6f4931',
                            900: '#5a3d2b',
                            950: '#301f15',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col font-sans">

    <!-- Top Admin Banner -->
    <div class="bg-slate-900 text-white text-xs px-6 py-2.5 flex justify-between items-center shadow-md">
        <div class="flex items-center gap-2">
            <span class="bg-red-600 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded">Security Admin</span>
            <span>Leather Point Store Management Desk</span>
        </div>
        <div class="flex gap-4 items-center">
            <?php if (!$db_connected): ?>
                <span class="bg-amber-600 px-2 py-0.5 rounded text-[10px] font-black uppercase animate-pulse">Mock Sandbox Mode</span>
            <?php else: ?>
                <span class="bg-green-600 px-2 py-0.5 rounded text-[10px] font-black uppercase">Live SQL Connected</span>
            <?php endif; ?>
            <a href="../index.php" class="text-slate-300 hover:text-white hover:underline">&lsaquo; Back to Storefront</a>
        </div>
    </div>

    <!-- Layout Grid -->
    <div class="flex-grow flex flex-col md:flex-row">
        
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-64 bg-slate-850 text-slate-800 bg-white border-r border-slate-200 p-6 space-y-6">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Administrative Control</span>
                <span class="text-xl font-black text-slate-800 tracking-tight">LPS Control Desk</span>
            </div>

            <nav class="space-y-1 flex flex-col text-sm font-semibold">
                <a href="dashboard.php" class="p-2.5 rounded hover:bg-slate-50 hover:text-leather-700 transition flex items-center gap-2">📊 Dashboard Metrics</a>
                <a href="categories.php" class="p-2.5 rounded hover:bg-slate-50 hover:text-leather-700 transition flex items-center gap-2">📁 Categories CRUD</a>
                <a href="subcategories.php" class="p-2.5 rounded hover:bg-slate-50 hover:text-leather-700 transition flex items-center gap-2">🗂️ Subcategories CRUD</a>
                <a href="products.php" class="p-2.5 rounded hover:bg-slate-50 hover:text-leather-700 transition flex items-center gap-2">👜 Leather Items Inventory</a>
                <a href="users.php" class="p-2.5 rounded hover:bg-slate-50 hover:text-leather-700 transition flex items-center gap-2">👥 Customer Directory</a>
                <a href="orders.php" class="p-2.5 rounded hover:bg-slate-50 hover:text-leather-700 transition flex items-center gap-2">📦 Booking Orders</a>
                <a href="feedbacks.php" class="p-2.5 rounded hover:bg-slate-50 hover:text-leather-700 transition flex items-center gap-2">💬 Customer Feedback</a>
                <a href="logout.php" class="p-2.5 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition flex items-center gap-2 mt-12 border-t pt-4">🔒 Admin Logout</a>
            </nav>
        </aside>

        <!-- Main Workspace -->
        <main class="flex-grow p-6 sm:p-10">
