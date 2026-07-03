<?php
require_once "includes/header.php";

// Metrics calculations (Admin 3 metrics)
if ($db_connected) {
    // 1. Registered Users Count
    $u_stmt = $db->query("SELECT COUNT(id) FROM users WHERE role = 'user'");
    $total_users = $u_stmt->fetchColumn();

    // 2. Orders Metrics
    $o_stmt = $db->query("SELECT status, COUNT(id) as count FROM orders GROUP BY status");
    $orders_stats = $o_stmt->fetchAll();
    
    // 3. Category count
    $cat_stmt = $db->query("SELECT COUNT(id) FROM categories");
    $total_categories = $cat_stmt->fetchColumn();

    // 4. Product items inventory count
    $prod_stmt = $db->query("SELECT COUNT(id) FROM products");
    $total_products = $prod_stmt->fetchColumn();

    // 5. General Feedback Responses count
    $f_stmt = $db->query("SELECT COUNT(id) FROM feedbacks");
    $total_feedbacks = $f_stmt->fetchColumn();
} else {
    // Session Mock DB totals
    $total_users = count($_SESSION['mock_users']) - 1; // subtract admin
    $total_categories = count($_SESSION['mock_categories']);
    $total_products = count($_SESSION['mock_products']);
    $total_feedbacks = count($_SESSION['mock_feedbacks']);

    $orders_stats = [
        'pending' => 0,
        'approved' => 0,
        'canceled' => 0
    ];
    foreach($_SESSION['mock_orders'] as $ord) {
        $orders_stats[$ord['status']]++;
    }
}

// Structuring order metrics for display
$pending_orders = $db_connected ? 0 : $orders_stats['pending'];
$approved_orders = $db_connected ? 0 : $orders_stats['approved'];
$canceled_orders = $db_connected ? 0 : $orders_stats['canceled'];

if ($db_connected) {
    foreach($orders_stats as $stat) {
        if ($stat['status'] === 'pending') $pending_orders = $stat['count'];
        if ($stat['status'] === 'approved') $approved_orders = $stat['count'];
        if ($stat['status'] === 'canceled') $canceled_orders = $stat['count'];
    }
}

$total_orders = $pending_orders + $approved_orders + $canceled_orders;
?>

<div class="space-y-8">
    
    <div>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Administrative Dashboard</span>
        <h1 class="text-3xl font-black text-slate-800">Metrics Overview (Admin)</h1>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Registered Users -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Registered Users</span>
                <span class="text-3xl font-black text-slate-800 mt-1 block"><?php echo $total_users; ?></span>
            </div>
            <span class="text-4xl">👥</span>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between border-l-4 border-amber-500">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Pending Bookings</span>
                <span class="text-3xl font-black text-slate-800 mt-1 block text-amber-600"><?php echo $pending_orders; ?></span>
            </div>
            <span class="text-4xl">⏳</span>
        </div>

        <!-- Approved Orders -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between border-l-4 border-green-500">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Approved Orders</span>
                <span class="text-3xl font-black text-slate-800 mt-1 block text-green-600"><?php echo $approved_orders; ?></span>
            </div>
            <span class="text-4xl">✅</span>
        </div>

        <!-- Feedback Messages -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between border-l-4 border-blue-500">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Feedback / Messages</span>
                <span class="text-3xl font-black text-slate-800 mt-1 block text-blue-600"><?php echo $total_feedbacks; ?></span>
            </div>
            <span class="text-4xl">💬</span>
        </div>

    </div>

    <!-- Second Row Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Inventory Details -->
        <div class="md:col-span-1 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <h3 class="font-bold text-gray-800 text-base mb-4">Inventory Summary</h3>
            <div class="space-y-3 text-sm text-slate-600">
                <div class="flex justify-between border-b pb-2">
                    <span>Distinct Categories</span>
                    <span class="font-bold text-slate-800"><?php echo $total_categories; ?></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span>Active Products</span>
                    <span class="font-bold text-slate-800"><?php echo $total_products; ?></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span>Total Orders Logged</span>
                    <span class="font-bold text-slate-800"><?php echo $total_orders; ?></span>
                </div>
            </div>
            <a href="products.php" class="block text-center bg-slate-100 hover:bg-slate-250 text-slate-700 text-xs font-bold py-2.5 rounded mt-6 transition">Manage Inventory</a>
        </div>

        <!-- Admin Quick Credentials Review -->
        <div class="md:col-span-2 bg-slate-900 text-white p-6 rounded-xl shadow-md flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-amber-400 text-base mb-2">🎓 Admin Management System</h3>
                <p class="text-xs text-slate-300 leading-relaxed">This PHP application has been developed using OOP Standards. All SQL updates are secured using PDO parameterized bound queries. Security parameters such as password hashing and HTML escaping have been implemented dynamically.</p>
            </div>
            <div class="bg-slate-800 p-4 rounded text-xs space-y-1 text-slate-200 mt-4">
                <div>✓ <strong>Model-View Architecture:</strong> Core logic sits inside modular reusable Classes.</div>
                <div>✓ <strong>Secure Transactions:</strong> order checkouts leverage DB commits and rollback transaction logic.</div>
                <div>✓ <strong>Responsive CSS:</strong> Tailored cleanly via fully dynamic utility Tailwind structures.</div>
            </div>
           <div class="text-xs text-slate-400 mt-4"> © 2026 Leather Point Store. Developed by Fazal Abbas Shah | PHP • MySQL • Tailwind CSS • OOP </div>
        </div>

    </div>

</div>

<?php require_once "includes/footer.php"; ?>