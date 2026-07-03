<?php

session_start();

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
    header("Location: admin/dashboard.php");
    exit;
}

require_once "includes/header.php";


// Get categories & subcategories
if ($db_connected) {
    $categories = $productObj->getCategories();
    $subcategories = $productObj->getSubcategories();
} else {
    $categories = $_SESSION['mock_categories'];
    $subcategories = $_SESSION['mock_subcategories'];
}

// Search queries (FR3: Searching box - name, price, color)
$searchName = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$searchPrice = isset($_GET['search_price']) ? trim($_GET['search_price']) : '';
$searchColor = isset($_GET['search_color']) ? trim($_GET['search_color']) : '';
$selectedCategory = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$selectedSubcategory = isset($_GET['subcategory_id']) ? $_GET['subcategory_id'] : '';

// Fetch products based on search (FR3)
if ($db_connected) {
    $products = $productObj->getProducts($searchName, $searchPrice, $searchColor, $selectedCategory, $selectedSubcategory);
} else {
    // Implement search filtering over Mock Session data manually to demonstrate PHP filtering skills!
    $products = [];
    foreach ($_SESSION['mock_products'] as $p) {
        if (!empty($searchName) && stripos($p['name'], $searchName) === false) continue;
        if (!empty($searchPrice) && $p['price'] > floatval($searchPrice)) continue;
        if (!empty($searchColor) && stripos($p['color'], $searchColor) === false) continue;
        if (!empty($selectedCategory) && $p['category_id'] != $selectedCategory) continue;
        if (!empty($selectedSubcategory) && $p['subcategory_id'] != $selectedSubcategory) continue;
        $products[] = $p;
    }
}
?>

<!-- Hero Banner -->
<div class="relative bg-leather-900 text-white rounded-2xl overflow-hidden shadow-lg mb-12 py-20 px-8 sm:px-16" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1473186578172-c141e6798cf4?auto=format&fit=crop&q=80&w=1200'); background-size: cover; background-position: center;">
    <div class="max-w-xl relative z-10">
        <span class="bg-leather-500 text-xs uppercase font-extrabold tracking-widest px-3 py-1 rounded">100% Genuine Craftsmanship</span>
        <h1 class="text-4xl sm:text-5xl font-black uppercase tracking-tight mt-4 leading-none">Leather Point <span class="text-leather-300">Store</span></h1>
        <p class="text-gray-200 mt-4 text-base">Explore premium backpacks, jackets, slim bi-fold wallets, and professional formal belts tailored from the highest caliber of full-grain leather.</p>
        <a href="#store-products" class="inline-block bg-white text-leather-900 font-bold px-6 py-3 rounded-lg shadow-md mt-6 hover:bg-leather-100 transition">Shop Collection</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8" id="store-products">
    
    <!-- Sidebar Filters -->
    <div class="lg:col-span-1 bg-white p-6 rounded-xl border border-gray-100 shadow-sm self-start">
        <h3 class="font-bold text-gray-800 text-lg border-b pb-3 mb-4">Category Filters</h3>
        
        <!-- Category List -->
        <div class="space-y-2">
            <a href="index.php#store-products" class="block text-sm p-2 rounded <?php echo empty($selectedCategory) ? 'bg-leather-50 text-leather-700 font-semibold' : 'text-gray-600 hover:bg-gray-50'; ?>">All Categories</a>
            <?php foreach($categories as $cat): ?>
                <a href="index.php?category_id=<?php echo $cat['id']; ?>#store-products" class="block text-sm p-2 rounded <?php echo $selectedCategory == $cat['id'] ? 'bg-leather-50 text-leather-700 font-semibold' : 'text-gray-600 hover:bg-gray-50'; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
                
                <!-- If Category selected, show subcategories beneath it -->
                <?php if ($selectedCategory == $cat['id']): ?>
                    <div class="pl-4 space-y-1 mt-1 border-l-2 border-leather-200 ml-2">
                        <?php foreach($subcategories as $sub): ?>
                            <?php if ($sub['category_id'] == $cat['id']): ?>
                                <a href="index.php?category_id=<?php echo $cat['id']; ?>&subcategory_id=<?php echo $sub['id']; ?>#store-products" class="block text-xs py-1 rounded <?php echo $selectedSubcategory == $sub['id'] ? 'text-leather-600 font-bold' : 'text-gray-500 hover:text-leather-600'; ?>">
                                    • <?php echo htmlspecialchars($sub['name']); ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Main Catalog Area -->
    <div class="lg:col-span-3 flex flex-col gap-6">
        
        <!-- FR3: Search Form Box -->
        <form action="index.php" method="GET" class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($selectedCategory); ?>">
            <input type="hidden" name="subcategory_id" value="<?php echo htmlspecialchars($selectedSubcategory); ?>">
            
            <!-- Search Name -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Search Item Name</label>
                <input type="text" name="search_name" value="<?php echo htmlspecialchars($searchName); ?>" placeholder="e.g. Backpack, Jacket" class="w-full text-sm border border-gray-200 rounded p-2 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <!-- Search Price -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Max Price ($)</label>
                <input type="number" step="0.01" name="search_price" value="<?php echo htmlspecialchars($searchPrice); ?>" placeholder="e.g. 150" class="w-full text-sm border border-gray-200 rounded p-2 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <!-- Search Color -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Search by Color</label>
                <input type="text" name="search_color" value="<?php echo htmlspecialchars($searchColor); ?>" placeholder="e.g. Black, Brown" class="w-full text-sm border border-gray-200 rounded p-2 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="flex-grow bg-leather-700 text-white text-sm font-bold p-2.5 rounded hover:bg-leather-800 transition">Search</button>
                <a href="index.php" class="bg-gray-100 text-gray-600 text-sm font-bold px-3 py-2.5 rounded hover:bg-gray-200 transition text-center flex items-center justify-center">Clear</a>
            </div>
        </form>

        <!-- Product Grid List -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">
                    <?php if (!empty($searchName) || !empty($searchPrice) || !empty($searchColor)): ?>
                        Search Results
                    <?php else: ?>
                        Our Premium Inventory
                    <?php endif; ?>
                </h2>
                <span class="text-xs text-gray-400 font-semibold"><?php echo count($products); ?> Items Found</span>
            </div>

            <?php if (empty($products)): ?>
                <div class="bg-white p-12 text-center rounded-xl border border-gray-100 shadow-sm text-gray-500">
                    👜 No premium leather items match your search. Try broadening your keywords.
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <?php foreach($products as $product): ?>
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition duration-300 flex flex-col overflow-hidden group">
                            <!-- Image Container -->
                            <div class="relative bg-gray-100 h-48 overflow-hidden">
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                <span class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm text-xs font-bold px-2 py-1 rounded text-leather-800 border border-gray-100">
                                    <?php echo htmlspecialchars($product['color']); ?>
                                </span>
                            </div>
                            <!-- Content Details -->
                            <div class="p-4 flex-grow flex flex-col justify-between">
                                <div>
                                    <div class="text-[10px] uppercase font-bold text-leather-500 mb-1">
                                        <?php echo htmlspecialchars($product['category_name'] ?? 'Leather Goods'); ?>
                                    </div>
                                    <h3 class="font-bold text-gray-800 text-sm hover:text-leather-600 transition leading-snug">
                                        <a href="product-details.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                    </h3>
                                    
                                    <!-- Stock alerts -->
                                    <div class="mt-2 flex items-center gap-1.5">
                                        <?php if ($product['stock'] > 5): ?>
                                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                            <span class="text-xs text-green-600 font-semibold">In Stock (<?php echo $product['stock']; ?>)</span>
                                        <?php elseif ($product['stock'] > 0): ?>
                                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                            <span class="text-xs text-amber-600 font-semibold">Low Stock (<?php echo $product['stock']; ?> left!)</span>
                                        <?php else: ?>
                                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                            <span class="text-xs text-red-600 font-semibold">Out of Stock</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-gray-50 flex justify-between items-center">
                                    <span class="text-base font-black text-gray-900">$<?php echo number_format($product['price'], 2); ?></span>
                                    <a href="product-details.php?id=<?php echo $product['id']; ?>" class="bg-leather-700 text-white text-xs font-bold px-3 py-2 rounded hover:bg-leather-800 transition shadow-sm">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once "includes/footer.php"; ?>