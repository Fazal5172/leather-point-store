<?php
require_once "includes/header.php";

// If already logged in, redirect them away from the login page immediately
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        $error_msg = "Please provide your email and password.";
    } else {
        $user_row = false;
        
        if ($db_connected) {
            // SQL authenticate
            $user_row = $userObj->login($email, $password);
        } else {
            // Mock Authenticate
            foreach ($_SESSION['mock_users'] as $u) {
                if ($u['email'] === $email && password_verify($password, $u['password'])) {
                    $user_row = $u;
                    break;
                }
            }
        }

        if ($user_row) {
            // Set sessions
            $_SESSION['user_id'] = $user_row['id'];
            $_SESSION['user_name'] = $user_row['name'];
            $_SESSION['user_email'] = $user_row['email'];
            $_SESSION['user_phone'] = $user_row['phone'];
            $_SESSION['user_role'] = $user_row['role'];

            // Role-based redirection logic
            if ($_SESSION['user_role'] === 'admin') {
                $redirect = 'admin/dashboard.php';
            } else {
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
            }

            header("Location: " . $redirect);
            exit;
        } else {
            $error_msg = "Invalid login credentials. Please check details.";
        }
    }
}
?>

<div class="max-w-md mx-auto bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm">
    <h1 class="text-2xl font-black text-gray-800 border-b pb-4 mb-6">🔒 User Login</h1>

    <?php if (!empty($error_msg)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded text-xs mb-4">
            ⚠️ <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <form action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="POST" class="space-y-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">EMAIL ADDRESS</label>
            <input type="email" name="email" required placeholder="name@domain.com" class="w-full text-sm border border-gray-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">PASSWORD</label>
            <input type="password" name="password" required placeholder="••••••••" class="w-full text-sm border border-gray-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
        </div>

        <button type="submit" class="w-full bg-leather-700 hover:bg-leather-800 text-white font-bold py-3 rounded-lg shadow-sm transition">Log In</button>
        <div class="text-xs text-center text-gray-400 mt-4 font-semibold uppercase tracking-wider bg-gray-50 p-3 rounded text-leather-700">
            For Quick Testing: <br>
            <span class="block text-[10px] text-gray-500 lowercase mt-1 font-mono font-normal">user: user@gmail.com / userpassword</span>
            <span class="block text-[10px] text-gray-500 lowercase font-mono font-normal">admin: admin@leatherpoint.com / adminpassword</span>
        </div>
        <div class="text-xs text-center text-gray-400 mt-4">New user? <a href="register.php" class="text-leather-600 font-bold hover:underline">Register Here</a></div>
    </form>
</div>

<?php require_once "includes/footer.php"; ?>