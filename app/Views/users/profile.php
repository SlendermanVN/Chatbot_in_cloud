<?php
$user = $user ?? [];

// Flash messages
$flash = $flashType = null;
if (!empty($_SESSION['flash'])) {
    $flash    = $_SESSION['flash']['message'] ?? '';
    $flashType= $_SESSION['flash']['type']    ?? 'success';
    unset($_SESSION['flash']);
}
?>

<div class="max-w-4xl mx-auto px-4 py-12">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8">
        <a href="<?= BASE_URL ?>/index.php" class="hover:text-white transition-colors">Trang chủ</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-white font-semibold">Hồ sơ cá nhân</span>
    </nav>

    <!-- Header -->
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-black text-white">Quản lý tài khoản</h1>
            <p class="text-gray-500 text-sm mt-1">Cập nhật thông tin cá nhân và cài đặt tài khoản của bạn.</p>
        </div>
        <div class="hidden sm:block">
            <span class="px-4 py-2 rounded-xl bg-primary/10 border border-primary/20 text-primary text-xs font-bold uppercase tracking-widest">
                <?= htmlspecialchars($user['role'] ?? 'Member') ?>
            </span>
        </div>
    </div>

    <!-- Flash message -->
    <?php if ($flash): ?>
    <div class="mb-8 flex items-center gap-3 px-5 py-4 rounded-2xl <?= $flashType === 'success' ? 'bg-green-500/10 border border-green-600/30 text-green-400' : 'bg-red-500/10 border border-red-600/30 text-red-400' ?>">
        <i class="fa-solid fa-circle-<?= $flashType === 'success' ? 'check' : 'exclamation' ?>"></i>
        <span class="text-sm font-medium"><?= htmlspecialchars($flash) ?></span>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Avatar & Quick Info -->
        <div class="space-y-6">
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8 text-center shadow-xl">
                <div class="relative inline-block group mb-6">
                    <?php 
                        $avatar = $user['avatar'] ?? '';
                        $displayName = $user['full_name'] ?? $user['username'] ?? 'User';
                        $avatarUrl = !empty($avatar) && $avatar !== 'uploads/avatars/default.png'
                            ? BASE_URL . '/' . htmlspecialchars($avatar)
                            : "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&background=ff6600&color=fff&size=128&bold=true";
                    ?>
                    <img src="<?= $avatarUrl ?>" 
                         class="w-32 h-32 rounded-full border-4 border-gray-800 object-cover shadow-2xl transition-transform group-hover:scale-105"
                         id="avatar-preview">
                    <label for="avatar-input" class="absolute bottom-1 right-1 w-10 h-10 bg-primary hover:bg-primary-dark text-white rounded-full flex items-center justify-center cursor-pointer shadow-lg transition-all border-4 border-gray-900">
                        <i class="fa-solid fa-camera text-sm"></i>
                    </label>
                </div>
                <h2 class="text-xl font-black text-white"><?= htmlspecialchars($user['full_name'] ?? $user['username'] ?? '') ?></h2>
                <p class="text-gray-500 text-sm mb-6"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                
                <div class="pt-6 border-t border-gray-800 space-y-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Ngày gia nhập</span>
                        <span class="text-white font-medium"><?= isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : '—' ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Trạng thái</span>
                        <span class="text-green-400 font-bold flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span> Hoạt động
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stats link -->
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-6 shadow-xl">
                <a href="<?= BASE_URL ?>/index.php?route=orders" class="flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                            <i class="fa-solid fa-box text-sm"></i>
                        </div>
                        <span class="text-white font-bold text-sm">Đơn hàng của tôi</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-700 group-hover:text-primary transition-colors"></i>
                </a>
            </div>
        </div>

        <!-- Right: Edit Form -->
        <div class="lg:col-span-2">
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8 shadow-xl">
                <form action="<?= BASE_URL ?>/index.php?route=profile_update" method="POST" enctype="multipart/form-data" id="profile-form">
                    <!-- Hidden avatar input -->
                    <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/*">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-400 mb-2">Họ và tên</label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required
                                class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-400 mb-2">Tên đăng nhập</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required
                                class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-400 mb-2">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required
                                class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary transition-colors">
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-8">
                        <a href="<?= BASE_URL ?>/index.php?route=change_password" class="text-sm font-bold text-gray-500 hover:text-primary transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-lock"></i> Đổi mật khẩu
                        </a>
                        <div class="flex items-center gap-4">
                            <button type="reset" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-white transition-colors">
                                Hủy
                            </button>
                            <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white font-extrabold px-8 py-3.5 rounded-2xl transition-all shadow-lg shadow-primary/30 hover:-translate-y-0.5">
                                <i class="fa-solid fa-check"></i> Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    // Avatar preview
    document.getElementById('avatar-input').onchange = evt => {
        const [file] = document.getElementById('avatar-input').files
        if (file) {
            document.getElementById('avatar-preview').src = URL.createObjectURL(file)
        }
    }
</script>
