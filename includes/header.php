<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leather Point Store</title>
    <link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/contact-widget.css">

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
                <span class="text-2xl font-black uppercase tracking-wider text-leather-800 whitespace-nowrap">
                    👜 Leather Point <span class="text-leather-500 font-light whitespace-nowrap">Store</span>
                </span>
            </a>

            <!-- Nav Items -->
            <nav class="flex items-center gap-6 text-sm font-semibold text-gray-700 whitespace-nowrap">
                <a href="index.php" class="hover:text-leather-600 transition">Shop</a>
                <a href="feedback.php" class="hover:text-leather-600 transition">Feedback</a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="order-status.php" class="hover:text-leather-600 transition whitespace-nowrap">My Orders</a>
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
