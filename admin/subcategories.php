<?php
require_once "includes/header.php";

$error_msg = "";
$success_msg = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $category_id = intval($_POST['category_id']);
        $name = trim($_POST['name']);

        if (empty($category_id) || empty($name)) {
            $error_msg = "Please select category and specify subcategory name.";
        } else {
            if ($action === 'add') {
                if ($db_connected) {
                    $productObj->addSubcategory($category_id, $name);
                } else {
                    $new_id = count($_SESSION['mock_subcategories']) + 1;
                    $_SESSION['mock_subcategories'][$new_id] = ['id' => $new_id, 'category_id' => $category_id, 'name' => $name];
                }
                $success_msg = "Subcategory added successfully!";
            } elseif ($action === 'update') {
                $id = intval($_POST['id']);
                if ($db_connected) {
                    $productObj->updateSubcategory($id, $category_id, $name);
                } else {
                    $_SESSION['mock_subcategories'][$id]['category_id'] = $category_id;
                    $_SESSION['mock_subcategories'][$id]['name'] = $name;
                }
                $success_msg = "Subcategory updated successfully!";
            }
        }
    }
}

// Handle Delete (Admin 5)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($db_connected) {
        $productObj->deleteSubcategory($delete_id);
    } else {
        unset($_SESSION['mock_subcategories'][$delete_id]);
    }
    $success_msg = "Subcategory deleted successfully!";
}

// Fetch lists
if ($db_connected) {
    $categories = $productObj->getCategories();
    $subcategories = $productObj->getSubcategories();
} else {
    $categories = $_SESSION['mock_categories'];
    // Merge mock names
    $subcategories = [];
    foreach($_SESSION['mock_subcategories'] as $sub) {
        $cat_name = $_SESSION['mock_categories'][$sub['category_id']]['name'] ?? 'Leather Goods';
        $sub['category_name'] = $cat_name;
        $subcategories[] = $sub;
    }
}

$edit_sub = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    if ($db_connected) {
        $edit_sub = $productObj->getSubcategoryById($edit_id);
    } else {
        $edit_sub = $_SESSION['mock_subcategories'][$edit_id] ?? null;
    }
}
?>

<div class="space-y-8">
    
    <div>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Stock Management</span>
        <h1 class="text-3xl font-black text-slate-800">Subcategories (Admin 5)</h1>
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Add / Update Form -->
        <div class="md:col-span-1 bg-white p-6 rounded-xl border border-slate-200 shadow-sm self-start">
            <h3 class="font-bold text-slate-800 border-b pb-2 mb-4">
                <?php echo $edit_sub ? 'Update Subcategory' : 'Add Subcategory'; ?>
            </h3>

            <form action="subcategories.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="<?php echo $edit_sub ? 'update' : 'add'; ?>">
                <?php if ($edit_sub): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_sub['id']; ?>">
                <?php endif; ?>

                <!-- Parent Category Select -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">PARENT CATEGORY</label>
                    <select name="category_id" required class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
                        <option value="">-- Choose Parent --</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_sub && $edit_sub['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Subcategory Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">SUBCATEGORY NAME</label>
                    <input type="text" name="name" required value="<?php echo $edit_sub ? htmlspecialchars($edit_sub['name']) : ''; ?>" placeholder="e.g. Cardholders" class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-grow bg-leather-700 hover:bg-leather-800 text-white font-bold text-xs py-2.5 rounded transition">
                        <?php echo $edit_sub ? 'Update Subcategory' : 'Save Subcategory'; ?>
                    </button>
                    <?php if ($edit_sub): ?>
                        <a href="subcategories.php" class="bg-slate-100 text-slate-600 font-bold text-xs px-3 py-2.5 rounded hover:bg-slate-200 transition text-center flex items-center">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- List Directory -->
        <div class="md:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 border-b pb-2 mb-4">Active Subcategories</h3>
            
            <div class="border rounded-xl overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 font-bold text-slate-700 border-b">
                        <tr>
                            <th class="p-4">Subcategory ID</th>
                            <th class="p-4">Parent Category</th>
                            <th class="p-4">Subcategory Name</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-600">
                        <?php if (empty($subcategories)): ?>
                            <tr>
                                <td colspan="4" class="p-4 text-center text-slate-400">No subcategories active.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($subcategories as $sub): ?>
                                <tr>
                                    <td class="p-4">#SUB-<?php echo str_pad($sub['id'], 3, "0", STR_PAD_LEFT); ?></td>
                                    <td class="p-4 font-semibold text-slate-500"><?php echo htmlspecialchars($sub['category_name']); ?></td>
                                    <td class="p-4 font-bold text-slate-800"><?php echo htmlspecialchars($sub['name']); ?></td>
                                    <td class="p-4 text-right flex justify-end gap-3 text-xs">
                                        <a href="subcategories.php?edit_id=<?php echo $sub['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                        <a href="subcategories.php?delete_id=<?php echo $sub['id']; ?>" onclick="return confirm('Are you sure you want to delete this subcategory?');" class="text-red-600 hover:underline">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require_once "includes/footer.php"; ?>