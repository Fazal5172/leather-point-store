<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../classes/User.php";
require_once __DIR__ . "/../classes/Product.php";
require_once __DIR__ . "/../classes/Order.php";
require_once __DIR__ . "/../classes/Review.php";

$database = new Database();
$db = $database->getConnection();
$db_connected = ($db !== null);

// Initialize Session Mock DB Fallback if DB is not connected
if (!$db_connected && !isset($_SESSION['mock_db_initialized'])) {
    $_SESSION['mock_db_initialized'] = true;
    $_SESSION['mock_categories'] = [
        1 => ['id' => 1, 'name' => 'Bags'],
        2 => ['id' => 2, 'name' => 'Jackets'],
        3 => ['id' => 3, 'name' => 'Wallets'],
        4 => ['id' => 4, 'name' => 'Belts']
    ];
    $_SESSION['mock_subcategories'] = [
        1 => ['id' => 1, 'category_id' => 1, 'name' => 'Backpacks'],
        2 => ['id' => 2, 'category_id' => 1, 'name' => 'Messenger Bags'],
        3 => ['id' => 3, 'category_id' => 1, 'name' => 'Travel Bags'],
        4 => ['id' => 4, 'category_id' => 2, 'name' => 'Bomber Jackets'],
        5 => ['id' => 5, 'category_id' => 2, 'name' => 'Biker Jackets'],
        6 => ['id' => 6, 'category_id' => 3, 'name' => 'Bi-Fold Wallets'],
        7 => ['id' => 7, 'category_id' => 3, 'name' => 'Cardholders'],
        8 => ['id' => 8, 'category_id' => 4, 'name' => 'Formal Belts'],
        9 => ['id' => 9, 'category_id' => 4, 'name' => 'Casual Belts']
    ];
    $_SESSION['mock_products'] = [
        1 => [
            'id' => 1, 'category_id' => 1, 'subcategory_id' => 1, 
            'name' => 'Classic Leather Backpack', 
            'description' => 'Handcrafted from full-grain brown leather, featuring durable straps and multiple zip compartments. Perfect for daily commutes or traveling.', 
            'price' => 149.99, 'color' => 'Brown', 'stock' => 15, 
            'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&q=80&w=600'
        ],
        2 => [
            'id' => 2, 'category_id' => 1, 'subcategory_id' => 2, 
            'name' => 'Vintage Leather Messenger Bag', 
            'description' => 'A premium black leather messenger bag featuring an adjustable shoulder strap and padded laptop compartment.', 
            'price' => 129.50, 'color' => 'Black', 'stock' => 8, 
            'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&q=80&w=600'
        ],
        3 => [
            'id' => 3, 'category_id' => 2, 'subcategory_id' => 4, 
            'name' => 'Classic Tan Bomber Jacket', 
            'description' => 'Crafted from authentic high-quality leather, this tan bomber jacket comes with ribbed cuffs and comfortable inner lining.', 
            'price' => 249.99, 'color' => 'Tan', 'stock' => 12, 
            'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&q=80&w=600'
        ],
        4 => [
            'id' => 4, 'category_id' => 2, 'subcategory_id' => 5, 
            'name' => 'Urban Black Biker Jacket', 
            'description' => 'An edgy black biker jacket with premium metal zippers, double lining, and water-resistant leather styling.', 
            'price' => 289.00, 'color' => 'Black', 'stock' => 6, 
            'image' => 'https://images.unsplash.com/photo-1521223890158-f9f7c3d5bab3?auto=format&fit=crop&q=80&w=600'
        ],
        5 => [
            'id' => 5, 'category_id' => 3, 'subcategory_id' => 6, 
            'name' => 'Luxury Slim Wallet', 
            'description' => 'Elegant mahogany bi-fold wallet. Features RFID blocking technology and holds up to 8 credit cards and cash.', 
            'price' => 45.00, 'color' => 'Brown', 'stock' => 25, 
            'image' => 'https://images.unsplash.com/photo-1627123424574-724758594e93?auto=format&fit=crop&q=80&w=600'
        ],
        6 => [
            'id' => 6, 'category_id' => 4, 'subcategory_id' => 8, 
            'name' => 'Italian Full-Grain Belt', 
            'description' => 'Genuine mahogany Italian leather belt with a brushed nickel buckle. Highly durable and versatile.', 
            'price' => 35.00, 'color' => 'Mahogany', 'stock' => 30, 
            'image' => 'https://images.unsplash.com/photo-1624224971170-2f84fed5eb5e?auto=format&fit=crop&q=80&w=600'
        ]
    ];
    $_SESSION['mock_users'] = [
        1 => ['id' => 1, 'name' => 'John Doe', 'email' => 'user@gmail.com', 'password' => password_hash('userpassword', PASSWORD_BCRYPT), 'phone' => '+923129876543', 'role' => 'user', 'created_at' => date('Y-m-d H:i:s')],
        2 => ['id' => 2, 'name' => 'Admin Fazal', 'email' => 'admin@leatherpoint.com', 'password' => password_hash('adminpassword', PASSWORD_BCRYPT), 'phone' => '+923001234567', 'role' => 'admin', 'created_at' => date('Y-m-d H:i:s')]
    ];
    $_SESSION['mock_orders'] = [];
    $_SESSION['mock_reviews'] = [
        1 => ['id' => 1, 'user_id' => 1, 'user_name' => 'John Doe', 'product_id' => 1, 'rating' => 5, 'review_text' => 'Absolutely superb quality leather. The stitching is top notch!', 'created_at' => date('Y-m-d H:i:s')]
    ];
    $_SESSION['mock_feedbacks'] = [];
}

// Instantiate core classes
$userObj = new User($db);
$productObj = new Product($db);
$orderObj = new Order($db);
$reviewObj = new Review($db);

// Helper cart count
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leather Point Store</title>
    <!-- Tailwind CSS with custom Amber-Tan Leather style Configuration -->
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
<body class="bg-gray-50 flex flex-col min-h-screen">

    <!-- Top Info Bar -->
    <div class="bg-leather-950 text-white text-xs py-2 px-4 flex justify-between items-center">
        <div>✨ Welcome to Leather Point Store - Genuine, Handcrafted Products</div>
        <div class="flex gap-4">
            <?php if (!$db_connected): ?>
                <span class="text-amber-400 font-semibold animate-pulse">⚠️ MOCK DEMO MODE (No DB)</span>
            <?php else: ?>
                <span class="text-green-400">⚡ MySQL DB Connected</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            
            <!-- Logo -->
            <a href="index.php" class="flex items-center gap-2">
                <span class="text-2xl font-black uppercase tracking-wider text-leather-800">
                    👜 Leather Point <span class="text-leather-500 font-light">Store</span>
                </span>
            </a>

            <!-- Nav Items -->
            <nav class="flex items-center gap-6 text-sm font-semibold text-gray-700">
                <a href="index.php" class="hover:text-leather-600 transition">Shop</a>
                <a href="feedback.php" class="hover:text-leather-600 transition">Feedback</a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="order-status.php" class="hover:text-leather-600 transition">My Orders</a>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="admin/dashboard.php" class="bg-red-50 text-red-700 px-3 py-1 rounded-full border border-red-200 hover:bg-red-100 transition">Admin Panel</a>
                    <?php endif; ?>
                    <div class="flex items-center gap-2 border-l pl-4 border-gray-200">
                        <span class="text-gray-500 font-normal">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="logout.php" class="text-leather-600 hover:text-leather-800 hover:underline">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="hover:text-leather-600 transition">Login</a>
                    <a href="register.php" class="bg-leather-700 text-white px-4 py-2 rounded hover:bg-leather-800 transition shadow-sm">Register</a>
                <?php endif; ?>

                <!-- Cart Button -->
                <a href="cart.php" class="relative p-2 text-gray-700 hover:text-leather-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <?php if ($cart_count > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-leather-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center border border-white">
                            <?php echo $cart_count; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </header>

    <!-- Error notice for recruiters if database is down -->
    <?php if (!$db_connected && !isset($_COOKIE['hide_db_alert'])): ?>
        <div class="bg-amber-50 border-b border-amber-200 text-amber-800 px-4 py-3 text-sm flex justify-between items-center">
            <div class="container mx-auto flex items-center gap-2">
                <span>⚡ <strong>Recruiter Notice:</strong> The MySQL Database is not configured. The app is running seamlessly in <strong>OOP-based Mock Session Database Fallback Mode</strong> so you can interact with all features immediately! Importing `schema.sql` and setting db credentials in `config/Database.php` enables real MySQL database functionality.</span>
            </div>
        </div>
    <?php endif; ?>

    <main class="flex-grow container mx-auto px-4 py-8">
