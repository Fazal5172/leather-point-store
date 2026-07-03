<?php
require_once "includes/header.php";

$error_msg = "";
$success_msg = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add') {
            $name = trim($_POST['name']);
            if (empty($name)) {
                $error_msg = "Category name cannot be empty.";
            } else {
                if ($db_connected) {
                    $productObj->addCategory($name);
                } else {
                    $new_id = count($_SESSION['mock_categories']) + 1;
                    $_SESSION['mock_categories'][$new_id] = ['id' => $new_id, 'name' => $name];
                }
                $success_msg = "Category added successfully!";
            }
        } elseif ($action === 'update') {
            $id = intval($_POST['id']);
            $name = trim($_POST['name']);
            if (empty($name)) {
                $error_msg = "Category name cannot be empty.";
            } else {
                if ($db_connected) {
                    $productObj->updateCategory($id, $name);
                } else {
                    $_SESSION['mock_categories'][$id]['name'] = $name;
                }
                $success_msg = "Category updated successfully!";
            }
        }
    }
}

// Handle Delete (Admin 4)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($db_connected) {
        $productObj->deleteCategory($delete_id);
    } else {
        unset($_SESSION['mock_categories'][$delete_id]);
    }
    $success_msg = "Category deleted successfully!";
}

// Fetch categories
if ($db_connected) {
    $categories = $productObj->getCategories();
} else {
    $categories = $_SESSION['mock_categories'];
}

$edit_cat = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    if ($db_connected) {
        $edit_cat = $productObj->getCategoryById($edit_id);
    } else {
        $edit_cat = $_SESSION['mock_categories'][$edit_id] ?? null;
    }
}
?>

<div class="space-y-8">
    
    <div>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Stock Management</span>
        <h1 class="text-3xl font-black text-slate-800">Item Categories (Admin 4)</h1>
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
                <?php echo $edit_cat ? 'Update Category' : 'Add New Category'; ?>
            </h3>

            <form action="categories.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="<?php echo $edit_cat ? 'update' : 'add'; ?>">
                <?php if ($edit_cat): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_cat['id']; ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">CATEGORY NAME</label>
                    <input type="text" name="name" required value="<?php echo $edit_cat ? htmlspecialchars($edit_cat['name']) : ''; ?>" placeholder="e.g. Premium Bags" class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-grow bg-leather-700 hover:bg-leather-800 text-white font-bold text-xs py-2.5 rounded transition">
                        <?php echo $edit_cat ? 'Update Category' : 'Save Category'; ?>
                    </button>
                    <?php if ($edit_cat): ?>
                        <a href="categories.php" class="bg-slate-100 text-slate-600 font-bold text-xs px-3 py-2.5 rounded hover:bg-slate-200 transition text-center flex items-center">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- List Directory -->
        <div class="md:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 border-b pb-2 mb-4">Active Categories</h3>
            
            <div class="border rounded-xl overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 font-bold text-slate-700 border-b">
                        <tr>
                            <th class="p-4">Category ID</th>
                            <th class="p-4">Category Name</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-600">
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="3" class="p-4 text-center text-slate-400">No categories active yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($categories as $cat): ?>
                                <tr>
                                    <td class="p-4">#CAT-<?php echo str_pad($cat['id'], 3, "0", STR_PAD_LEFT); ?></td>
                                    <td class="p-4 font-bold text-slate-800"><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td class="p-4 text-right flex justify-end gap-3 text-xs">
                                        <a href="categories.php?edit_id=<?php echo $cat['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                        <a href="categories.php?delete_id=<?php echo $cat['id']; ?>" onclick="return confirm('Are you sure you want to delete this category? All related items will be deleted.');" class="text-red-600 hover:underline">Delete</a>
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