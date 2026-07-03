<?php
require_once "includes/header.php";

$success_msg = "";

// Handle status updates (Admin 10: Approve/Cancel orders)
if (isset($_GET['status_id']) && isset($_GET['status'])) {
    $order_id = intval($_GET['status_id']);
    $status = $_GET['status'];

    if ($status === 'approved' || $status === 'canceled') {
        if ($db_connected) {
            $orderObj->updateStatus($order_id, $status);
        } else {
            $_SESSION['mock_orders'][$order_id]['status'] = $status;
        }
        $success_msg = "Order status updated successfully to " . strtoupper($status) . "!";
    }
}

// Fetch complete orders history (Admin 9)
if ($db_connected) {
    $orders = $orderObj->getAllOrders();
} else {
    $orders = array_reverse(array_values($_SESSION['mock_orders'])); // Reverse to show latest first
}
?>

<div class="space-y-8">
    
    <div>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Orders Tracker</span>
        <h1 class="text-3xl font-black text-slate-800">Booking Orders History (Admin 9)</h1>
    </div>

    <?php if (!empty($success_msg)): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded-xl text-xs">
            ✨ <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <h3 class="font-bold text-slate-800 border-b pb-2 mb-4 font-mono uppercase text-xs tracking-wider">Historical Log</h3>
        
        <table class="w-full text-left text-sm min-w-[700px]">
            <thead class="bg-slate-50 font-bold text-slate-700 border-b">
                <tr>
                    <th class="p-4">Order Ref</th>
                    <th class="p-4">Customer Name</th>
                    <th class="p-4">Delivery Contacts</th>
                    <th class="p-4">Amount Paid</th>
                    <th class="p-4">Shipment Status</th>
                    <th class="p-4 text-right">Approve/Cancel (Admin 10)</th>
                </tr>
            </thead>
            <tbody class="divide-y text-slate-600">
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" class="p-4 text-center text-slate-400">No customer orders recorded yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($orders as $o): ?>
                        <tr>
                            <td class="p-4 font-mono text-xs font-bold">#LPS-<?php echo str_pad($o['id'], 6, "0", STR_PAD_LEFT); ?></td>
                            <td class="p-4 font-bold text-slate-800"><?php echo htmlspecialchars($o['user_name']); ?></td>
                            <td class="p-4 text-xs">
                                <span class="block">📧 <?php echo htmlspecialchars($o['email']); ?></span>
                                <span class="block text-slate-400 mt-1">📞 <?php echo htmlspecialchars($o['phone']); ?></span>
                                <span class="block text-slate-400 mt-1">📍 <?php echo htmlspecialchars($o['shipping_address']); ?></span>
                            </td>
                            <td class="p-4 font-black text-slate-800">$<?php echo number_format($o['total_amount'], 2); ?></td>
                            <td class="p-4">
                                <?php if ($o['status'] === 'approved'): ?>
                                    <span class="bg-green-100 text-green-800 font-bold text-[10px] px-2.5 py-1 rounded-full uppercase">Approved</span>
                                <?php elseif ($o['status'] === 'canceled'): ?>
                                    <span class="bg-red-100 text-red-800 font-bold text-[10px] px-2.5 py-1 rounded-full uppercase">Canceled</span>
                                <?php else: ?>
                                    <span class="bg-amber-100 text-amber-800 font-bold text-[10px] px-2.5 py-1 rounded-full uppercase animate-pulse">Pending Approval</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-right text-xs pt-6">
                                <div class="flex justify-end gap-2">
                                    <?php if ($o['status'] === 'pending'): ?>
                                        <a href="orders.php?status_id=<?php echo $o['id']; ?>&status=approved" class="bg-green-50 hover:bg-green-100 text-green-700 font-bold px-2 py-1 rounded border border-green-200">Approve</a>
                                        <a href="orders.php?status_id=<?php echo $o['id']; ?>&status=canceled" onclick="return confirm('Cancel this order?');" class="bg-red-50 hover:bg-red-100 text-red-700 font-bold px-2 py-1 rounded border border-red-200">Cancel</a>
                                    <?php else: ?>
                                        <span class="text-slate-400 font-normal italic">Decision Locked</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once "includes/footer.php"; ?>