<?php
require_once "includes/header.php";

// Checkout verification
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    echo "<div class='max-w-xl mx-auto bg-amber-50 border border-amber-200 text-amber-800 p-8 rounded-2xl shadow-sm text-center my-12'>";
    echo "<span class='text-4xl block mb-4'>🔒</span>";
    echo "<h2 class='text-lg font-bold text-amber-900 mb-2'>Admin Access Restricted</h2>";
    echo "<p class='text-sm mb-4 leading-relaxed'>You are currently logged in as an <strong>Administrator</strong>. Administrator accounts are restricted from placing shopping orders to maintain auditing integrity and keep business metrics accurate.</p>";
    echo "<p class='text-xs text-amber-600 mb-6'>Please logout and sign in with a standard customer account (e.g., <code>user@gmail.com / userpassword</code>) to place orders.</p>";
    echo "<div class='flex justify-center gap-3'><a href='logout.php' class='bg-leather-700 hover:bg-leather-800 text-white text-xs font-bold px-4 py-2 rounded shadow-sm transition'>Logout & Test User</a><a href='index.php' class='bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-4 py-2 rounded shadow-sm transition'>Return Shop</a></div>";
    echo "</div>";
    require_once "includes/footer.php";
    exit;
}

$cart_items = $_SESSION['cart'] ?? [];
if (empty($cart_items)) {
    header("Location: index.php");
    exit;
}

$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

$error_msg = "";

// Handle checkout form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (empty($payment_method) || empty($address) || empty($phone) || empty($email)) {
        $error_msg = "Please complete all fields to process your transaction.";
    } elseif ($payment_method !== 'credit_card' && $payment_method !== 'cod') {
        $error_msg = "Invalid payment channel chosen.";
    } else {
        // Build transaction
        if ($db_connected) {
            // Call Order class OOP transaction workflow
            $order_id = $orderObj->create($_SESSION['user_id'], $total_amount, $payment_method, $address, $phone, $email, $cart_items);
        } else {
            // Simulated Transaction Flow
            $order_id = count($_SESSION['mock_orders']) + 1;
            
            // Log confirmation logs (FR8 is simulated on mock too!)
            $log_dir = __DIR__ . "/logs/";
            if (!file_exists($log_dir)) {
                mkdir($log_dir, 0777, true);
            }
            $log_file = $log_dir . "receipt_notifications.log";
            $date = date("Y-m-d H:i:s");

            $receipt = "=========================================================\n";
            $receipt .= "TRANSACTION CONFIRMATION RECEIPT (Simulated Mock) - " . $date . "\n";
            $receipt .= "Order Reference: #LPS-" . str_pad($order_id, 6, "0", STR_PAD_LEFT) . "\n";
            $receipt .= "User Email: " . $email . " | Contact: " . $phone . "\n";
            $receipt .= "Total Amount Charged: $" . number_format($total_amount, 2) . "\n";
            $receipt .= "Purchased Products:\n";
            foreach ($cart_items as $p_id => $item) {
                $receipt .= "  - " . $item['name'] . " (Qty: " . $item['quantity'] . " | Price: $" . number_format($item['price'], 2) . ")\n";
                // Deduct stock from mock session
                $_SESSION['mock_products'][$p_id]['stock'] -= $item['quantity'];
            }
            $receipt .= "---------------------------------------------------------\n";
            $receipt .= "[SIMULATED SMS SENT TO " . $phone . "]\n";
            $receipt .= "SMS Msg: \"Thank you! Your order #LPS-" . str_pad($order_id, 6, "0", STR_PAD_LEFT) . " has been received successfully. Total amount: $" . number_format($total_amount, 2) . ". The shipment status is currently PENDING.\"\n";
            $receipt .= "\n[SIMULATED EMAIL INVOICE SENT TO " . $email . "]\n";
            $receipt .= "Email Msg: \"Dear Leather Point customer, we have successfully processed your payment. Your receipt and shopping confirmation invoice for order #LPS-" . str_pad($order_id, 6, "0", STR_PAD_LEFT) . " are attached here as PDFs.\"\n";
            $receipt .= "=========================================================\n\n";

            file_put_contents($log_file, $receipt, FILE_APPEND);

            // Save order inside mock collection
            $_SESSION['mock_orders'][$order_id] = [
                'id' => $order_id,
                'user_id' => $_SESSION['user_id'],
                'user_name' => $_SESSION['user_name'],
                'total_amount' => $total_amount,
                'payment_method' => $payment_method,
                'status' => 'pending',
                'shipping_address' => $address,
                'phone' => $phone,
                'email' => $email,
                'items' => $cart_items,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        if ($order_id) {
            // Empty Cart
            $_SESSION['cart'] = [];
            
            // Redirect to success receipt order dashboard (FR5/FR8)
            header("Location: order-status.php?id=" . $order_id . "&success=1");
            exit;
        } else {
            $error_msg = "Critical SQL exception inside Database transactions. Try again.";
        }
    }
}
?>

<div class="bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm">
    <h1 class="text-2xl font-black text-gray-800 border-b pb-4 mb-6">🔒 Secure Checkout</h1>

    <?php if (!empty($error_msg)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded text-xs mb-4">
            ⚠️ <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Forms details (FR4 Payment & Contact) -->
        <div class="lg:col-span-2">
            <form action="checkout.php" method="POST" class="space-y-6">
                <div>
                    <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">1. Delivery Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">PHONE NUMBER</label>
                            <input type="text" name="phone" required value="<?php echo htmlspecialchars($_SESSION['user_phone'] ?? ''); ?>" placeholder="+923000000000" class="w-full text-sm border border-gray-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">EMAIL ADDRESS</label>
                            <input type="email" name="email" required value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" placeholder="name@domain.com" class="w-full text-sm border border-gray-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">SHIPPING STREET ADDRESS</label>
                            <textarea name="address" rows="3" required placeholder="House No, Street, Neighborhood, City, Country" class="w-full text-sm border border-gray-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- FR4: Select Payment Option -->
                <div>
                    <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">2. Payment Methods (FR4)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="border p-4 rounded-xl flex items-center gap-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="cod" checked class="text-leather-600 focus:ring-leather-500">
                            <div>
                                <span class="block font-bold text-gray-800 text-sm">Cash On Delivery (COD)</span>
                                <span class="text-xs text-gray-400">Pay inside raw cash at doorstep shipment delivery.</span>
                            </div>
                        </label>
                        <label class="border p-4 rounded-xl flex items-center gap-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="credit_card" class="text-leather-600 focus:ring-leather-500">
                            <div>
                                <span class="block font-bold text-gray-800 text-sm">Credit / Debit Card</span>
                                <span class="text-xs text-gray-400">Pay securely via standard simulated billing processor.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-leather-700 hover:bg-leather-800 text-white font-bold py-3 rounded-lg shadow-sm transition">Finalize Order & Checkout</button>
            </form>
        </div>

        <!-- Order details sidebar -->
        <div class="lg:col-span-1 bg-gray-50/50 p-6 rounded-xl border border-gray-100 self-start space-y-4">
            <h3 class="font-bold text-gray-800 border-b pb-2">Order Review</h3>
            <div class="divide-y max-h-60 overflow-y-auto">
                <?php foreach($cart_items as $id => $item): ?>
                    <div class="py-2.5 flex justify-between items-center text-xs">
                        <div>
                            <span class="font-bold text-gray-800"><?php echo htmlspecialchars($item['name']); ?></span>
                            <span class="text-gray-400 block">Qty: <?php echo $item['quantity']; ?></span>
                        </div>
                        <span class="font-bold text-gray-700">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="border-t pt-3 flex justify-between font-bold text-gray-800 text-base">
                <span>Total Due</span>
                <span class="text-leather-700">$<?php echo number_format($total_amount, 2); ?></span>
            </div>
        </div>

    </div>
</div>

<?php require_once "includes/footer.php"; ?>