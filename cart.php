<?php
require_once "includes/header.php";

// Initialize mock DB products if needed
if ($db_connected) {
    // Standard connection
} else {
    $products = $_SESSION['mock_products'];
}

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $p_id = intval($_POST['product_id']);
        
        if ($_POST['action'] === 'update_qty') {
            $new_qty = intval($_POST['quantity']);
            
            // Check product stock limit
            if ($db_connected) {
                $p = $productObj->getProductById($p_id);
            } else {
                $p = $_SESSION['mock_products'][$p_id] ?? null;
            }
            
            if ($p && $new_qty > $p['stock']) {
                $error_msg = "Only " . $p['stock'] . " items currently in stock for " . htmlspecialchars($p['name']);
            } elseif ($new_qty > 0) {
                $_SESSION['cart'][$p_id]['quantity'] = $new_qty;
            }
        } elseif ($_POST['action'] === 'remove') {
            unset($_SESSION['cart'][$p_id]);
        }
    }
}

$cart_items = $_SESSION['cart'] ?? [];
$subtotal = 0;
?>

<div class="bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm">
    <h1 class="text-2xl font-black text-gray-800 border-b pb-4 mb-6">🛒 Your Shopping Cart</h1>

    <?php if (isset($error_msg)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded text-xs mb-4">
            ⚠️ <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <div class="text-center py-12 text-gray-500">
            <span class="text-5xl block mb-4">👜</span>
            <p class="mb-4">Your shopping cart is currently empty.</p>
            <a href="index.php" class="inline-block bg-leather-700 text-white font-bold text-sm px-5 py-2.5 rounded hover:bg-leather-800 transition shadow-sm">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Items Table -->
            <div class="lg:col-span-2">
                <div class="border rounded-xl overflow-hidden divide-y">
                    <?php foreach($cart_items as $product_id => $item): 
                        $item_total = $item['price'] * $item['quantity'];
                        $subtotal += $item_total;
                    ?>
                        <div class="p-4 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white hover:bg-gray-50/50 transition">
                            <div class="flex items-center gap-4">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-16 h-16 object-cover rounded border">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-sm hover:text-leather-600"><a href="product-details.php?id=<?php echo $product_id; ?>"><?php echo htmlspecialchars($item['name']); ?></a></h3>
                                    <span class="text-xs font-semibold text-leather-700">$<?php echo number_format($item['price'], 2); ?></span>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-start">
                                <form action="cart.php" method="POST" class="flex items-center gap-2">
                                    <input type="hidden" name="action" value="update_qty">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="w-16 text-center border rounded p-1 text-sm focus:ring-1 focus:ring-leather-500 outline-none">
                                    <button type="submit" class="text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1.5 rounded transition">Update</button>
                                </form>

                                <span class="font-bold text-gray-800 text-sm block min-w-[70px] text-right">$<?php echo number_format($item_total, 2); ?></span>

                                <form action="cart.php" method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Remove item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1 bg-gray-50/50 p-6 rounded-xl border border-gray-100 self-start">
                <h3 class="font-bold text-gray-800 text-lg border-b pb-3 mb-4">Summary</h3>
                <div class="space-y-3 text-sm text-gray-600 mb-6">
                    <div class="flex justify-between">
                        <span>Items Subtotal</span>
                        <span class="font-semibold text-gray-800">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Shipping Delivery</span>
                        <span class="text-green-600 font-semibold">FREE</span>
                    </div>
                    <div class="flex justify-between border-t pt-3 font-bold text-gray-800 text-base">
                        <span>Total Cost</span>
                        <span class="text-leather-700">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                </div>

                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <button disabled class="w-full text-center bg-gray-200 text-gray-400 font-bold py-3 rounded-lg cursor-not-allowed">Proceed to Checkout (Disabled)</button>
                    <p class="text-[10px] text-amber-600 mt-2 text-center font-semibold">🔒 Administrative accounts are restricted from ordering.</p>
                <?php else: ?>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <button disabled class="w-full text-center bg-gray-200 text-gray-400 font-bold py-3 rounded-lg cursor-not-allowed">Proceed to Checkout (Disabled)</button>
                    <p class="text-[10px] text-amber-600 mt-2 text-center font-semibold">🔒 Administrative accounts are restricted from ordering.</p>
                <?php else: ?>
                    <a href="checkout.php" class="block w-full text-center bg-leather-700 hover:bg-leather-800 text-white font-bold py-3 rounded-lg shadow-sm transition">Proceed to Checkout</a>
                <?php endif; ?>
                <?php endif; ?>
            </div>

        </div>
    <?php endif; ?>
</div>

<?php require_once "includes/footer.php"; ?>