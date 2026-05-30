<?php
// Flash messages
$flash = $flashType = null;
if (!empty($_SESSION['flash'])) {
    $flash    = $_SESSION['flash']['message'] ?? '';
    $flashType= $_SESSION['flash']['type']    ?? 'success';
    unset($_SESSION['flash']);
}
?>

<div class="max-w-md mx-auto px-4 py-20">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8 justify-center">
        <a href="<?= BASE_URL ?>/index.php" class="hover:text-white transition-colors">Trang chủ</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="<?= BASE_URL ?>/index.php?route=profile" class="hover:text-white transition-colors">Hồ sơ</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-white font-semibold">Đổi mật khẩu</span>
    </nav>

    <!-- Header -->
    <div class="text-center mb-10">
        <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mx-auto mb-4 border border-primary/20">
            <i class="fa-solid fa-lock-open text-2xl"></i>
        </div>
        <h1 class="text-2xl font-black text-white">Thay đổi mật khẩu</h1>
        <p class="text-gray-500 text-sm mt-1">Vui lòng nhập mật khẩu mới để bảo vệ tài khoản.</p>
    </div>

    <!-- Flash message -->
    <?php if ($flash): ?>
    <div class="mb-8 flex items-center gap-3 px-5 py-4 rounded-2xl <?= $flashType === 'success' ? 'bg-green-500/10 border border-green-600/30 text-green-400' : 'bg-red-500/10 border border-red-600/30 text-red-400' ?>">
        <i class="fa-solid fa-circle-<?= $flashType === 'success' ? 'check' : 'exclamation' ?>"></i>
        <span class="text-sm font-medium"><?= htmlspecialchars($flash) ?></span>
    </div>
    <?php endif; ?>

    <!-- Form card -->
    <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8 shadow-xl">
        <form action="<?= BASE_URL ?>/index.php?route=post_change_password" method="POST" class="space-y-6">
            
            <div>
                <label class="block text-sm font-semibold text-gray-400 mb-2">Mật khẩu hiện tại</label>
                <div class="relative">
                    <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-600"></i>
                    <input type="password" name="current_password" required placeholder="••••••••"
                        class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>

            <div class="pt-4 border-t border-gray-800/50">
                <label class="block text-sm font-semibold text-gray-400 mb-2">Mật khẩu mới</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-600"></i>
                    <input type="password" name="new_password" required placeholder="Tối thiểu 8 ký tự"
                        class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
                <p class="text-[10px] text-gray-500 mt-2 italic">
                    Yêu cầu: Tối thiểu 8 ký tự, 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-400 mb-2">Xác nhận mật khẩu mới</label>
                <div class="relative">
                    <i class="fa-solid fa-shield-check absolute left-4 top-1/2 -translate-y-1/2 text-gray-600"></i>
                    <input type="password" name="confirm_password" required placeholder="••••••••"
                        class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-primary hover:bg-primary-dark text-white font-black py-4 rounded-2xl transition-all shadow-lg shadow-primary/30 hover:-translate-y-1 flex items-center justify-center gap-2">
                <i class="fa-solid fa-check-double"></i> Cập nhật mật khẩu
            </button>
            
            <a href="<?= BASE_URL ?>/index.php?route=profile" 
               class="block text-center text-sm text-gray-500 hover:text-white transition-colors">
                Quay lại hồ sơ
            </a>
        </form>
    </div>
</div>
