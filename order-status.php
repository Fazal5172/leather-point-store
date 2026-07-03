<?php
require_once "includes/header.php";

// Authorization Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$order = null;
$order_items = [];

if ($id > 0) {
    // Get specific order details (FR5 Status lookup)
    if ($db_connected) {
        $order = $orderObj->getOrderDetails($id);
        if ($order && $order['user_id'] == $_SESSION['user_id']) {
            $order_items = $orderObj->getOrderItems($id);
        } else {
            $order = null; // Unauthorized or not found
        }
    } else {
        $order = $_SESSION['mock_orders'][$id] ?? null;
        if ($order && $order['user_id'] == $_SESSION['user_id']) {
            $order_items = $order['items'];
        } else {
            $order = null;
        }
    }
}

// Fetch complete order history of current user (FR6 Order History)
$order_history = [];
if ($db_connected) {
    $order_history = $orderObj->getUserOrders($_SESSION['user_id']);
} else {
    foreach ($_SESSION['mock_orders'] as $ord) {
        if ($ord['user_id'] == $_SESSION['user_id']) {
            $order_history[] = $ord;
        }
    }
}
?>

<div class="space-y-12">
    
    <!-- IF SUCCESS REDIRECT SHOW TRANSACTIONS CONFIRMATION (FR8) -->
    <?php if (isset($_GET['success']) && $order): ?>
        <div class="bg-green-50 border border-green-200 rounded-2xl p-6 sm:p-10 text-center shadow-sm">
            <span class="text-5xl">🎉</span>
            <h1 class="text-2xl sm:text-3xl font-black text-green-800 mt-4">Order Placed Successfully!</h1>
            <p class="text-green-600 mt-2 text-sm max-w-lg mx-auto">Thank you for purchasing from Leather Point Store. The transaction was verified, and copy receipts have been triggered.</p>
            
            <!-- MOCK FR8 TRACES -->
            <div class="mt-8 bg-gray-950 p-6 rounded-xl text-left font-mono text-xs text-green-400 max-w-2xl mx-auto shadow-inner overflow-x-auto border border-gray-800">
                <div class="flex justify-between items-center text-[10px] text-gray-500 border-b border-gray-800 pb-2 mb-4">
                    <span>📡 SIMULATED LOG TRAFFIC TRACE </span>
                    <span class="bg-green-900 text-green-300 px-2 py-0.5 rounded uppercase font-bold">Trace Success</span>
                </div>
                <?php 
                $logs = "";
                if ($db_connected) {
                    $logs = $orderObj->getNotificationLogs();
                } else {
                    $log_file = __DIR__ . "/logs/receipt_notifications.log";
                    if (file_exists($log_file)) {
                        $logs = file_get_contents($log_file);
                    }
                }
                
                // Print only the last log trace to avoid massive screens
                $split_logs = explode("=========================================================", $logs);
                $last_trace = end($split_logs);
                if (empty(trim($last_trace))) {
                    $last_trace = prev($split_logs);
                }
                echo nl2br(htmlspecialchars($last_trace));
                ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- 1. DISPLAY SPECIFIC ORDER DETAILS (FR5: Order Status) -->
    <?php if ($order): ?>
        <div class="bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b pb-4">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">ORDER TRACKER</span>
                    <h2 class="text-xl font-bold text-gray-800">Order Reference #LPS-<?php echo str_pad($order['id'], 6, "0", STR_PAD_LEFT); ?></h2>
                </div>
                
                <!-- FR5: Status Badges -->
                <div>
                    <?php if ($order['status'] === 'approved'): ?>
                        <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-xs font-bold border border-green-200 uppercase tracking-wider">Approved / Dispatched</span>
                    <?php elseif ($order['status'] === 'canceled'): ?>
                        <span class="bg-red-100 text-red-700 px-4 py-2 rounded-full text-xs font-bold border border-red-200 uppercase tracking-wider">Canceled</span>
                    <?php else: ?>
                        <span class="bg-amber-100 text-amber-700 px-4 py-2 rounded-full text-xs font-bold border border-amber-200 uppercase tracking-wider">Pending Approval</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Receipt layout details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-600">
                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                    <h4 class="font-bold text-gray-800 mb-2">Shipping Information</h4>
                    <p class="leading-relaxed"><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                </div>
                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                    <h4 class="font-bold text-gray-800 mb-2">Billing Method</h4>
                    <p class="capitalize">Channel: <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    <p class="text-xs text-gray-400 mt-1">Shipping Cost: FREE</p>
                </div>
                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                    <h4 class="font-bold text-gray-800 mb-2">Contact Context</h4>
                    <p>Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
                    <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
                </div>
            </div>

            <!-- Item summary list -->
            <div class="border rounded-xl overflow-hidden mt-6">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 font-bold text-gray-700 border-b">
                        <tr>
                            <th class="p-4">Item Details</th>
                            <th class="p-4 text-center">Quantity</th>
                            <th class="p-4 text-right">Price</th>
                            <th class="p-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-gray-600">
                        <?php foreach($order_items as $item): ?>
                            <tr>
                                <td class="p-4 font-bold text-gray-800"><?php echo htmlspecialchars($item['name'] ?? $item['product_name']); ?></td>
                                <td class="p-4 text-center"><?php echo $item['quantity']; ?></td>
                                <td class="p-4 text-right">$<?php echo number_format($item['price'], 2); ?></td>
                                <td class="p-4 text-right font-semibold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-gray-50/50 font-black text-gray-800 text-base">
                            <td colspan="3" class="p-4 text-right border-t">Invoice Total</td>
                            <td class="p-4 text-right text-leather-700 border-t">$<?php echo number_format($order['total_amount'], 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="flex justify-end gap-3 mt-4">
                <button onclick="window.print();" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-4 py-2.5 rounded transition shadow-sm">🖨️ Print Invoice Receipt</button>
                <a href="order-status.php" class="bg-leather-700 hover:bg-leather-800 text-white text-xs font-bold px-4 py-2.5 rounded transition shadow-sm">My Order History</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- 2. USER ORDER HISTORY (FR6) -->
    <div class="bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm">
        <h2 class="text-xl font-bold text-gray-800 border-b pb-4 mb-6">📜 Your Order History</h2>
        
        <?php if (empty($order_history)): ?>
            <div class="text-center py-8 text-gray-500 text-sm">
                No orders found under this account. Explore our leather items and place an order!
            </div>
        <?php else: ?>
            <div class="border rounded-xl overflow-hidden divide-y">
                <?php foreach($order_history as $ord): ?>
                    <div class="p-4 flex flex-col sm:flex-row justify-between items-center gap-4 hover:bg-gray-50 transition">
                        <div>
                            <span class="block font-bold text-gray-800 text-sm">Order Ref: #LPS-<?php echo str_pad($ord['id'], 6, "0", STR_PAD_LEFT); ?></span>
                            <span class="text-xs text-gray-400">Placed on: <?php echo date("F j, Y, g:i a", strtotime($ord['created_at'])); ?></span>
                        </div>
                        
                        <div class="flex items-center gap-6">
                            <span class="font-black text-gray-800 text-sm">$<?php echo number_format($ord['total_amount'], 2); ?></span>
                            
                            <!-- Status Badges -->
                            <div>
                                <?php if ($ord['status'] === 'approved'): ?>
                                    <span class="bg-green-50 text-green-700 px-2.5 py-1 rounded-full text-xs font-semibold border border-green-100 uppercase tracking-wider">Approved</span>
                                <?php elseif ($ord['status'] === 'canceled'): ?>
                                    <span class="bg-red-50 text-red-700 px-2.5 py-1 rounded-full text-xs font-semibold border border-red-100 uppercase tracking-wider">Canceled</span>
                                <?php else: ?>
                                    <span class="bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full text-xs font-semibold border border-amber-100 uppercase tracking-wider">Pending</span>
                                <?php endif; ?>
                            </div>

                            <a href="order-status.php?id=<?php echo $ord['id']; ?>" class="text-xs font-bold text-leather-600 hover:underline">Invoice & Status</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once "includes/footer.php"; ?>