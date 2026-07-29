<?php

require_once __DIR__ . "/../config/bootstrap.php";
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
