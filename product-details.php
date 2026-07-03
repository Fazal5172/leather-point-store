<?php
require_once "includes/header.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;

// Retrieve product details
if ($db_connected) {
    $product = $productObj->getProductById($id);
} else {
    $product = $_SESSION['mock_products'][$id] ?? null;
}

if (!$product) {
    echo "<div class='bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl shadow-sm text-center'>⚠️ Product not found. <a href='index.php' class='underline font-bold'>Back to shop</a></div>";
    require_once "includes/footer.php";
    exit;
}

// Handle Add to Cart submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        $error_msg = "Administrators are restricted from placing shopping orders to maintain ledger and metric integrity.";
    } else {
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if ($quantity > $product['stock']) {
        $error_msg = "Cannot add more items than currently in stock! Only " . $product['stock'] . " left.";
    } else {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];
        }
        $success_msg = "Successfully added " . htmlspecialchars($product['name']) . " to your cart! <a href='cart.php' class='underline font-bold'>View Cart</a>";
    }
  }
}

// Handle Review Submission (FR7)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    if (!isset($_SESSION['user_id'])) {
        $review_error = "You must be logged in to submit a product review.";
    } else {
        $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
        $review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';
        
        if (empty($review_text)) {
            $review_error = "Please fill in your review text.";
        } else {
            if ($db_connected) {
                $reviewObj->addProductReview($_SESSION['user_id'], $id, $rating, $review_text);
            } else {
                // Mock review entry
                $new_review_id = count($_SESSION['mock_reviews']) + 1;
                $_SESSION['mock_reviews'][$new_review_id] = [
                    'id' => $new_review_id,
                    'user_id' => $_SESSION['user_id'],
                    'user_name' => $_SESSION['user_name'],
                    'product_id' => $id,
                    'rating' => $rating,
                    'review_text' => $review_text,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            $review_success = "Thank you! Your product review has been submitted successfully.";
        }
    }
}

// Fetch reviews for product
$reviews = [];
if ($db_connected) {
    $reviews = $reviewObj->getProductReviews($id);
} else {
    foreach ($_SESSION['mock_reviews'] as $rev) {
        if ($rev['product_id'] == $id) {
            $reviews[] = $rev;
        }
    }
}
?>

<div class="bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-10">
    
    <!-- Product Image -->
    <div class="bg-gray-50 rounded-xl overflow-hidden h-96 border border-gray-100 flex items-center justify-center">
        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover">
    </div>

    <!-- Product Info -->
    <div class="flex flex-col justify-between">
        <div>
            <!-- Breadcrumbs -->
            <div class="text-xs font-bold text-leather-500 uppercase tracking-widest mb-2">
                <?php echo htmlspecialchars($product['category_name'] ?? 'Leather Goods'); ?> &rsaquo; <?php echo htmlspecialchars($product['subcategory_name'] ?? 'Accessories'); ?>
            </div>
            
            <h1 class="text-2xl sm:text-3xl font-black text-gray-800 leading-tight mb-2">
                <?php echo htmlspecialchars($product['name']); ?>
            </h1>

            <span class="text-2xl font-black text-leather-700 block mb-4">
                $<?php echo number_format($product['price'], 2); ?>
            </span>

            <div class="space-y-3 text-sm text-gray-600 border-t border-b py-4 my-4">
                <p><strong>Color:</strong> <?php echo htmlspecialchars($product['color']); ?></p>
                <p><strong>Availability:</strong> 
                    <?php if ($product['stock'] > 0): ?>
                        <span class="text-green-600 font-semibold">In Stock (<?php echo $product['stock']; ?> available)</span>
                    <?php else: ?>
                        <span class="text-red-600 font-semibold">Out of Stock</span>
                    <?php endif; ?>
                </p>
                <p class="leading-relaxed"><?php echo htmlspecialchars($product['description']); ?></p>
            </div>
        </div>

        <!-- Form and Action responses -->
        <div>
            <?php if (isset($success_msg)): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded text-xs mb-3">
                    ✨ <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error_msg)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded text-xs mb-3">
                    ⚠️ <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-xl text-center text-xs">
                    🔒 <strong>Admin Mode:</strong> Administrative roles are restricted from placing orders to protect audit trails. Please <a href="logout.php" class="underline font-bold text-amber-950 hover:text-leather-600">Logout</a> and log in as a customer to test checkout.
                </div>
            <?php elseif ($product['stock'] > 0): ?>
                <form action="product-details.php?id=<?php echo $id; ?>" method="POST" class="flex gap-4">
                    <input type="hidden" name="action" value="add_to_cart">
                    <div class="w-20">
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="w-full text-center border border-gray-200 p-2.5 rounded focus:ring-1 focus:ring-leather-500 outline-none">
                    </div>
                    <button type="submit" class="flex-grow bg-leather-700 text-white font-bold px-6 py-2.5 rounded hover:bg-leather-800 transition shadow-sm">Add To Cart</button>
                </form>
            <?php else: ?>
                <button disabled class="w-full bg-gray-200 text-gray-400 font-bold py-2.5 rounded cursor-not-allowed">Product Out of Stock</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Product Reviews Section (FR7) -->
<div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-10">
    
    <!-- Left: Submissions Form (FR7) -->
    <div class="md:col-span-1 bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <h3 class="font-bold text-gray-800 text-lg border-b pb-3 mb-4">Submit a Review</h3>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded text-xs text-center">
                🔒 You must be logged in to submit feedback or reviews. <br><a href="login.php" class="underline font-bold text-amber-900">Login Here</a>
            </div>
        <?php else: ?>
            <?php if (isset($review_success)): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded text-xs mb-3">
                    <?php echo $review_success; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($review_error)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded text-xs mb-3">
                    <?php echo $review_error; ?>
                </div>
            <?php endif; ?>

            <form action="product-details.php?id=<?php echo $id; ?>" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="submit_review">
                
                <!-- Rating selection -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">PRODUCT RATING</label>
                    <select name="rating" class="w-full text-sm border border-gray-200 rounded p-2 focus:ring-1 focus:ring-leather-500 outline-none">
                        <option value="5">⭐⭐⭐⭐⭐ 5 Stars</option>
                        <option value="4">⭐⭐⭐⭐ 4 Stars</option>
                        <option value="3">⭐⭐⭐ 3 Stars</option>
                        <option value="2">⭐⭐ 2 Stars</option>
                        <option value="1">⭐ 1 Star</option>
                    </select>
                </div>

                <!-- Text field -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">YOUR REVIEW</label>
                    <textarea name="review_text" rows="4" placeholder="Share details about stitching, leather grade, zip smoothness..." class="w-full text-sm border border-gray-200 rounded p-2 focus:ring-1 focus:ring-leather-500 outline-none"></textarea>
                </div>

                <button type="submit" class="w-full bg-leather-700 text-white font-bold text-sm py-2 rounded hover:bg-leather-800 transition">Submit Review</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Right: Review List Display (FR7) -->
    <div class="md:col-span-2 bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <h3 class="font-bold text-gray-800 text-lg border-b pb-3 mb-4">Customer Reviews</h3>
        
        <?php if (empty($reviews)): ?>
            <div class="text-gray-400 text-sm py-8 text-center">No reviews submitted for this item yet. Be the first to leave one!</div>
        <?php else: ?>
            <div class="divide-y space-y-4">
                <?php foreach($reviews as $rev): ?>
                    <div class="pt-4 first:pt-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-bold text-gray-800 text-sm"><?php echo htmlspecialchars($rev['user_name']); ?></span>
                                <span class="text-xs text-gray-400 ml-2"><?php echo date("F j, Y", strtotime($rev['created_at'])); ?></span>
                            </div>
                            <span class="text-sm text-yellow-500 font-bold">
                                <?php echo str_repeat("★", $rev['rating']) . str_repeat("☆", 5 - $rev['rating']); ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2 leading-relaxed"><?php echo htmlspecialchars($rev['review_text']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once "includes/footer.php"; ?>