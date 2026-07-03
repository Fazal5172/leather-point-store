<?php
require_once "includes/header.php";

// Fetch general feedbacks (Admin 3 feed reader)
if ($db_connected) {
    $feedbacks = $reviewObj->getAllFeedbacks();
} else {
    $feedbacks = $_SESSION['mock_feedbacks'] ?? [];
    $feedbacks = array_reverse($feedbacks);
}
?>

<div class="space-y-8">
    
    <div>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Communications Desk</span>
        <h1 class="text-3xl font-black text-slate-800">Customer Feedback Logs (Admin 3)</h1>
    </div>

    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-800 border-b pb-2 mb-6">Website Feedback Inbox</h3>
        
        <?php if (empty($feedbacks)): ?>
            <div class="text-slate-400 text-center py-12 text-sm">Feedback inbox is currently empty.</div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach($feedbacks as $fb): ?>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 flex flex-col justify-between hover:shadow-sm transition">
                        <p class="text-sm text-slate-600 leading-relaxed italic mb-4">"<?php echo htmlspecialchars($fb['message']); ?>"</p>
                        
                        <div class="border-t border-slate-200 pt-3 flex justify-between items-center text-xs text-slate-400 font-bold">
                            <div>
                                <span class="text-slate-700 block"><?php echo htmlspecialchars($fb['name']); ?></span>
                                <span class="font-normal block text-slate-400 mt-0.5"><?php echo htmlspecialchars($fb['email']); ?></span>
                            </div>
                            <span><?php echo date("M j, Y", strtotime($fb['created_at'])); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once "includes/footer.php"; ?>