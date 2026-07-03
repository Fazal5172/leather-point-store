<?php
require_once "includes/header.php";

$error_msg = "";
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error_msg = "Please fill in all registration fields.";
    } else {
        if ($db_connected) {
            // Register using OOP database User class
            $result = $userObj->register($name, $email, $password, $phone);
            if ($result === true) {
                $success_msg = "Your account has been created successfully! You can now log in.";
            } else {
                $error_msg = $result;
            }
        } else {
            // Mock Registration inside Session
            $exists = false;
            foreach ($_SESSION['mock_users'] as $u) {
                if ($u['email'] === $email) {
                    $exists = true;
                    break;
                }
            }
            if ($exists) {
                $error_msg = "Email is already registered under Mock Session DB!";
            } else {
                $new_id = count($_SESSION['mock_users']) + 1;
                $_SESSION['mock_users'][$new_id] = [
                    'id' => $new_id,
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_BCRYPT),
                    'phone' => $phone,
                    'role' => 'user',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $success_msg = "Your account has been created successfully inside Mock session DB! Use credentials to log in.";
            }
        }
    }
}
?>

<div class="max-w-md mx-auto bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm">
    <h1 class="text-2xl font-black text-gray-800 border-b pb-4 mb-6">👤 User Registration </h1>

    <?php if (!empty($success_msg)): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl text-center shadow-sm">
            <span class="text-4xl block mb-2">✨</span>
            <p class="font-bold"><?php echo $success_msg; ?></p>
            <a href="login.php" class="inline-block bg-leather-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg mt-4 shadow-sm hover:bg-leather-800 transition">Go to Login</a>
        </div>
    <?php else: ?>
        
        <?php if (!empty($error_msg)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded text-xs mb-4">
                ⚠️ <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">FULL NAME</label>
                <input type="text" name="name" required placeholder="John Doe" class="w-full text-sm border border-gray-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">EMAIL ADDRESS</label>
                <input type="email" name="email" required placeholder="name@domain.com" class="w-full text-sm border border-gray-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">PHONE NUMBER</label>
                <input type="text" name="phone" required placeholder="+923001234567" class="w-full text-sm border border-gray-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">PASSWORD</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full text-sm border border-gray-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <button type="submit" class="w-full bg-leather-700 hover:bg-leather-800 text-white font-bold py-3 rounded-lg shadow-sm transition">Register Account</button>
            <div class="text-xs text-center text-gray-400 mt-4">Already have an account? <a href="login.php" class="text-leather-600 font-bold hover:underline">Log In</a></div>
        </form>

    <?php endif; ?>
</div>

<?php require_once "includes/footer.php"; ?>