<?php
require_once "includes/header.php";

$success = false;
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($name) || empty($email) || empty($message)) {
        $error_msg = "Please provide your name, email, and message.";
    } else {
        if ($db_connected) {
            $reviewObj->addWebsiteFeedback($name, $email, $message);
        } else {
            // Mock Feedback Session
            $_SESSION['mock_feedbacks'][] = [
                'name' => $name,
                'email' => $email,
                'message' => $message,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        $success = true;
    }
}
?>

<div class="max-w-xl mx-auto bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm">
    <h1 class="text-2xl font-black text-gray-800 border-b pb-4 mb-6">💬 Submit General Website Feedback</h1>

    <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl text-center shadow-sm">
            <span class="text-4xl block mb-2">✨</span>
            <p class="font-bold">Thank you for your valuable feedback!</p>
            <p class="text-xs text-green-600 mt-1">We read every submission to improve our Leather Point Store services.</p>
            <a href="index.php" class="inline-block bg-leather-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg mt-4 shadow-sm hover:bg-leather-800 transition">Return Shop</a>
        </div>
    <?php else: ?>
        
        <?php if (!empty($error_msg)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded text-xs mb-4">
                ⚠️ <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form action="feedback.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">YOUR NAME</label>
                <input type="text" name="name" required placeholder="John Doe" class="w-full text-sm border border-gray-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">YOUR EMAIL</label>
                <input type="email" name="email" required placeholder="john@domain.com" class="w-full text-sm border border-gray-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">FEEDBACK MESSAGE</label>
                <textarea name="message" rows="5" required placeholder="Let us know how we are doing..." class="w-full text-sm border border-gray-200 rounded p-2.5 focus:ring-1 focus:ring-leather-500 outline-none"></textarea>
            </div>

            <button type="submit" class="w-full bg-leather-700 text-white font-bold py-3 rounded-lg shadow-sm hover:bg-leather-800 transition">Submit Feedback</button>
        </form>

    <?php endif; ?>
</div>

<?php require_once "includes/footer.php"; ?>