<?php
require_once "includes/header.php";

$error_msg = "";
$success_msg = "";

// Handle form submissions (Admin 2: Stock & Inventory CRUD)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        $category_id = intval($_POST['category_id']);
        $subcategory_id = intval($_POST['subcategory_id']);
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $color = trim($_POST['color']);
        $stock = intval($_POST['stock']);
        $image = trim($_POST['image']);

        if (empty($image)) {
            $image = "https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&q=80&w=600"; // default fallback placeholder
        }

        if (empty($category_id) || empty($subcategory_id) || empty($name) || empty($price) || empty($color)) {
            $error_msg = "Please fill in all required product fields.";
        } else {
            if ($action === 'add') {
                if ($db_connected) {
                    $productObj->addProduct($category_id, $subcategory_id, $name, $description, $price, $color, $stock, $image);
                } else {
                    $new_id = count($_SESSION['mock_products']) + 1;
                    $_SESSION['mock_products'][$new_id] = [
                        'id' => $new_id,
                        'category_id' => $category_id,
                        'subcategory_id' => $subcategory_id,
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'color' => $color,
                        'stock' => $stock,
                        'image' => $image
                    ];
                }
                $success_msg = "Inventory item added successfully!";
            } elseif ($action === 'update') {
                $id = intval($_POST['id']);
                if ($db_connected) {
                    $productObj->updateProduct($id, $category_id, $subcategory_id, $name, $description, $price, $color, $stock, $image);
                } else {
                    $_SESSION['mock_products'][$id] = [
                        'id' => $id,
                        'category_id' => $category_id,
                        'subcategory_id' => $subcategory_id,
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'color' => $color,
                        'stock' => $stock,
                        'image' => $image
                    ];
                }
                $success_msg = "Inventory item updated successfully!";
            }
        }
    }
}

// Handle Delete (Admin 2)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($db_connected) {
        $productObj->deleteProduct($delete_id);
    } else {
        unset($_SESSION['mock_products'][$delete_id]);
    }
    $success_msg = "Inventory item deleted successfully!";
}

// Fetch lists
if ($db_connected) {
    $categories = $productObj->getCategories();
    $subcategories = $productObj->getSubcategories();
    $products = $productObj->getProducts();
} else {
    $categories = $_SESSION['mock_categories'];
    $subcategories = $_SESSION['mock_subcategories'];
    // Merge mock names
    $products = [];
    foreach($_SESSION['mock_products'] as $p) {
        $cat_name = $_SESSION['mock_categories'][$p['category_id']]['name'] ?? 'Leather Goods';
        $sub_name = $_SESSION['mock_subcategories'][$p['subcategory_id']]['name'] ?? 'Accessories';
        $p['category_name'] = $cat_name;
        $p['subcategory_name'] = $sub_name;
        $products[] = $p;
    }
}

$edit_prod = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    if ($db_connected) {
        $edit_prod = $productObj->getProductById($edit_id);
    } else {
        $edit_prod = $_SESSION['mock_products'][$edit_id] ?? null;
    }
}
?>

<div class="space-y-8">
    
    <div class="flex justify-between items-center">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Stock Management</span>
            <h1 class="text-3xl font-black text-slate-800">Leather Inventory (Admin 2)</h1>
        </div>
        <?php if (!$edit_prod): ?>
            <a href="#product-form" class="bg-leather-700 hover:bg-leather-800 text-white text-xs font-bold px-4 py-2.5 rounded shadow-sm">Add Item Form</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($success_msg)): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded-xl text-xs">
            ✨ <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl text-xs">
            ⚠️ <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <!-- List Inventory Table -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <h3 class="font-bold text-slate-800 border-b pb-2 mb-4">Stock Directory</h3>
        
        <table class="w-full text-left text-sm min-w-[700px]">
            <thead class="bg-slate-50 font-bold text-slate-700 border-b">
                <tr>
                    <th class="p-4">Item Details</th>
                    <th class="p-4">Category / Sub</th>
                    <th class="p-4">Color</th>
                    <th class="p-4">Stock Status (Admin 2)</th>
                    <th class="p-4">Price</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y text-slate-600">
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" class="p-4 text-center text-slate-400">Inventory directory empty. Add items below.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($products as $p): ?>
                        <tr>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="product" class="w-10 h-10 object-cover border rounded">
                                    <span class="font-bold text-slate-800 block"><?php echo htmlspecialchars($p['name']); ?></span>
                                </div>
                            </td>
                            <td class="p-4 text-xs">
                                <span class="block font-bold"><?php echo htmlspecialchars($p['category_name']); ?></span>
                                <span class="block text-slate-400"><?php echo htmlspecialchars($p['subcategory_name']); ?></span>
                            </td>
                            <td class="p-4"><?php echo htmlspecialchars($p['color']); ?></td>
                            <td class="p-4">
                                <?php if ($p['stock'] > 5): ?>
                                    <span class="bg-green-100 text-green-800 font-bold text-[10px] px-2.5 py-1 rounded-full uppercase">In Stock (<?php echo $p['stock']; ?>)</span>
                                <?php elseif ($p['stock'] > 0): ?>
                                    <span class="bg-amber-100 text-amber-800 font-bold text-[10px] px-2.5 py-1 rounded-full uppercase">Low Stock (<?php echo $p['stock']; ?>)</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-800 font-bold text-[10px] px-2.5 py-1 rounded-full uppercase">Sold Out</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 font-black text-slate-800">$<?php echo number_format($p['price'], 2); ?></td>
                            <td class="p-4 text-right flex justify-end gap-3 text-xs pt-6">
                                <a href="products.php?edit_id=<?php echo $p['id']; ?>#product-form" class="text-blue-600 hover:underline">Edit</a>
                                <a href="products.php?delete_id=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure you want to delete this inventory item?');" class="text-red-600 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Product Form Block -->
    <div class="bg-white p-6 sm:p-10 rounded-xl border border-slate-200 shadow-sm" id="product-form">
        <h3 class="font-bold text-slate-800 border-b pb-3 mb-6">
            <?php echo $edit_prod ? 'Update Inventory Details' : 'Add New Inventory Product (Admin 2)'; ?>
        </h3>

        <form action="products.php" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <input type="hidden" name="action" value="<?php echo $edit_prod ? 'update' : 'add'; ?>">
            <?php if ($edit_prod): ?>
                <input type="hidden" name="id" value="<?php echo $edit_prod['id']; ?>">
            <?php endif; ?>

            <!-- Category -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">PARENT CATEGORY *</label>
                <select name="category_id" required class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none bg-white">
                    <option value="">-- Choose Category --</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_prod && $edit_prod['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Subcategory -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">SUBCATEGORY *</label>
                <select name="subcategory_id" required class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none bg-white">
                    <option value="">-- Choose Subcategory --</option>
                    <?php foreach($subcategories as $sub): ?>
                        <option value="<?php echo $sub['id']; ?>" <?php echo ($edit_prod && $edit_prod['subcategory_id'] == $sub['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sub['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Item name -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">PRODUCT ITEM NAME *</label>
                <input type="text" name="name" required value="<?php echo $edit_prod ? htmlspecialchars($edit_prod['name']) : ''; ?>" placeholder="e.g. Vintage Briefcase" class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <!-- Price -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">PRICE * ($)</label>
                <input type="number" step="0.01" name="price" required value="<?php echo $edit_prod ? htmlspecialchars($edit_prod['price']) : ''; ?>" placeholder="e.g. 110.00" class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <!-- Color -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">COLOR *</label>
                <input type="text" name="color" required value="<?php echo $edit_prod ? htmlspecialchars($edit_prod['color']) : ''; ?>" placeholder="e.g. Mahogany Brown" class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <!-- Stock (Admin 2) -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">INVENTORY STOCK COUNT * (Admin 2)</label>
                <input type="number" name="stock" required value="<?php echo $edit_prod ? htmlspecialchars($edit_prod['stock']) : '10'; ?>" placeholder="e.g. 25" class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <!-- Image URL -->
            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold text-slate-500 mb-1">PRODUCT IMAGE (Unsplash URL recommendation)</label>
                <input type="text" name="image" value="<?php echo $edit_prod ? htmlspecialchars($edit_prod['image']) : ''; ?>" placeholder="https://images.unsplash.com/..." class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <!-- Description -->
            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold text-slate-500 mb-1">DESCRIPTION</label>
                <textarea name="description" rows="4" placeholder="Brief details about leather durability, liners, hardware..." class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none"><?php echo $edit_prod ? htmlspecialchars($edit_prod['description']) : ''; ?></textarea>
            </div>

            <div class="sm:col-span-3 flex justify-end gap-2">
                <?php if ($edit_prod): ?>
                    <a href="products.php" class="bg-slate-100 text-slate-600 font-bold text-xs px-4 py-3 rounded hover:bg-slate-200 transition text-center flex items-center">Cancel</a>
                <?php endif; ?>
                <button type="submit" class="bg-leather-700 hover:bg-leather-800 text-white font-bold text-xs px-6 py-3 rounded transition shadow-sm">
                    <?php echo $edit_prod ? 'Save Changes' : 'Publish Product'; ?>
                </button>
            </div>
        </form>
    </div>

</div>

<?php require_once "includes/footer.php"; ?>
