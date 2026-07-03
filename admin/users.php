<?php
require_once "includes/header.php";

$error_msg = "";
$success_msg = "";

// Handle Delete (Admin 7)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    if ($db_connected) {
        $userObj->deleteUser($delete_id);
    } else {
        unset($_SESSION['mock_users'][$delete_id]);
    }
    $success_msg = "User removed from directory successfully!";
}

// Handle Update User Info (Admin 8)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_user') {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if (empty($name) || empty($email) || empty($phone)) {
        $error_msg = "Please fill in all directory details.";
    } else {
        if ($db_connected) {
            $userObj->updateUserInfo($id, $name, $email, $phone);
        } else {
            $_SESSION['mock_users'][$id]['name'] = $name;
            $_SESSION['mock_users'][$id]['email'] = $email;
            $_SESSION['mock_users'][$id]['phone'] = $phone;
        }
        $success_msg = "User information updated successfully!";
    }
}

// Fetch users list (Admin 6)
if ($db_connected) {
    $users = $userObj->getAllUsers();
} else {
    $users = [];
    foreach ($_SESSION['mock_users'] as $u) {
        if ($u['role'] === 'user') {
            $users[] = $u;
        }
    }
}

// Fetch user for edit form (Admin 8)
$edit_user = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    if ($db_connected) {
        $edit_user = $userObj->getById($edit_id);
    } else {
        $edit_user = $_SESSION['mock_users'][$edit_id] ?? null;
    }
}
?>

<div class="space-y-8">
    
    <div>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">User Directory</span>
        <h1 class="text-3xl font-black text-slate-800">Customers Database (Admin 6)</h1>
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
        
        <!-- List Customers Table (Admin 6) -->
        <div class="md:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
            <h3 class="font-bold text-slate-800 border-b pb-2 mb-4">Active Customers List</h3>
            
            <table class="w-full text-left text-sm min-w-[500px]">
                <thead class="bg-slate-50 font-bold text-slate-700 border-b">
                    <tr>
                        <th class="p-4">Customer ID</th>
                        <th class="p-4">Name</th>
                        <th class="p-4">Contact Detail</th>
                        <th class="p-4 text-right">Directory Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-slate-600">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="4" class="p-4 text-center text-slate-400">No customers registered yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($users as $u): ?>
                            <tr>
                                <td class="p-4">#USR-<?php echo str_pad($u['id'], 4, "0", STR_PAD_LEFT); ?></td>
                                <td class="p-4 font-bold text-slate-800"><?php echo htmlspecialchars($u['name']); ?></td>
                                <td class="p-4 text-xs">
                                    <span class="block">📧 <?php echo htmlspecialchars($u['email']); ?></span>
                                    <span class="block text-slate-400 mt-1">📞 <?php echo htmlspecialchars($u['phone']); ?></span>
                                </td>
                                <td class="p-4 text-right flex justify-end gap-3 text-xs pt-6">
                                    <a href="users.php?edit_id=<?php echo $u['id']; ?>#edit-form" class="text-blue-600 hover:underline">Update (Admin 8)</a>
                                    <a href="users.php?delete_id=<?php echo $u['id']; ?>" onclick="return confirm('Are you sure you want to delete this customer account? This cannot be undone.');" class="text-red-600 hover:underline">Delete (Admin 7)</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Form Box (Admin 8) -->
        <div class="md:col-span-1 bg-white p-6 rounded-xl border border-slate-200 shadow-sm self-start" id="edit-form">
            <h3 class="font-bold text-slate-800 border-b pb-2 mb-4">Update Profile Directory</h3>

            <?php if (!$edit_user): ?>
                <div class="text-slate-400 text-xs text-center py-12">
                    Click <strong>Update (Admin 8)</strong> next to a user to edit their profile information dynamically.
                </div>
            <?php else: ?>
                <form action="users.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="id" value="<?php echo $edit_user['id']; ?>">

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">CUSTOMER NAME</label>
                        <input type="text" name="name" required value="<?php echo htmlspecialchars($edit_user['name']); ?>" class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">EMAIL ADDRESS</label>
                        <input type="email" name="email" required value="<?php echo htmlspecialchars($edit_user['email']); ?>" class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">PHONE NUMBER</label>
                        <input type="text" name="phone" required value="<?php echo htmlspecialchars($edit_user['phone']); ?>" class="w-full text-sm border border-slate-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-grow bg-leather-700 hover:bg-leather-800 text-white font-bold text-xs py-2.5 rounded transition">
                            Save Changes
                        </button>
                        <a href="users.php" class="bg-slate-100 text-slate-600 font-bold text-xs px-3 py-2.5 rounded hover:bg-slate-200 transition text-center flex items-center">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once "includes/footer.php"; ?>