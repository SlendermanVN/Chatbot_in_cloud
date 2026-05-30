<?php
$pageTitle = 'Đăng nhập';
require_once __DIR__ . '/partials/guest_header.php';

// Flash message
$flash = $flashType = null;
if (!empty($_SESSION['flash'])) {
    $flash     = $_SESSION['flash']['message'] ?? '';
    $flashType = $_SESSION['flash']['type']    ?? 'error';
    unset($_SESSION['flash']);
}
?>

<div class="flex-1 flex items-center justify-center py-16 px-4 relative overflow-hidden">
    <!-- Ambient glow -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-primary/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-md relative z-10">

        <!-- Header text -->
        <div class="text-center mb-8">
            <a href="<?= BASE_URL ?>/index.php" class="inline-block text-3xl font-black italic tracking-widest text-primary mb-6">
                SportZone
            </a>
            <h1 class="text-3xl font-black text-white">Chào mừng trở lại!</h1>
            <p class="text-gray-500 mt-2 text-sm">
                Chưa có tài khoản?
                <a href="<?= BASE_URL ?>/index.php?route=register" class="text-primary font-bold hover:underline">Đăng ký miễn phí</a>
            </p>
        </div>

        <!-- Flash message -->
        <?php if ($flash): ?>
        <div class="mb-5 flex items-center gap-3 px-4 py-3.5 rounded-xl <?= $flashType === 'success' ? 'bg-green-500/10 border border-green-600/30 text-green-400' : 'bg-red-500/10 border border-red-600/30 text-red-400' ?>">
            <i class="fa-solid fa-circle-<?= $flashType === 'success' ? 'check' : 'exclamation' ?>"></i>
            <span class="text-sm"><?= htmlspecialchars($flash) ?></span>
        </div>
        <?php endif; ?>

        <!-- Error (legacy $error var) -->
        <?php if (!empty($error)): ?>
        <div class="mb-5 flex items-center gap-3 px-4 py-3.5 rounded-xl bg-red-500/10 border border-red-600/30 text-red-400">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span class="text-sm"><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <!-- Form card -->
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 shadow-2xl">
            <form method="POST" action="<?= BASE_URL ?>/index.php?route=post_login" id="loginForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-400 mb-1.5">
                            Địa chỉ Email
                        </label>
                        <div class="relative">
                            <i class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                            <input id="email" name="email" type="email" required autocomplete="email"
                                placeholder="example@gmail.com"
                                class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-primary transition-colors placeholder-gray-600">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-400 mb-1.5">
                            Mật khẩu
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                            <input id="password" name="password" type="password" required minlength="8" autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl pl-11 pr-12 py-3 text-sm focus:outline-none focus:border-primary transition-colors placeholder-gray-600">
                            <button type="button" id="togglePwd"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                                <i class="fa-solid fa-eye text-sm" id="pwdIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember_me"
                                class="w-4 h-4 rounded border-gray-600 bg-[#0b0f19] text-primary focus:ring-primary focus:ring-offset-0">
                            <span class="text-sm text-gray-400">Ghi nhớ đăng nhập</span>
                        </label>
                        <a href="<?= BASE_URL ?>/index.php?route=forgot_password"
                           class="text-sm text-gray-500 hover:text-primary transition-colors">Quên mật khẩu?</a>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white font-extrabold py-3.5 rounded-xl transition-all shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-0.5">
                        <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="flex items-center gap-4 my-6">
                <div class="flex-1 h-px bg-gray-800"></div>
                <span class="text-xs text-gray-600 uppercase tracking-widest">hoặc</span>
                <div class="flex-1 h-px bg-gray-800"></div>
            </div>

            <a href="<?= BASE_URL ?>/index.php?route=register"
               class="w-full flex items-center justify-center gap-2 bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 rounded-xl border border-gray-700 transition-all text-sm">
                <i class="fa-solid fa-user-plus text-gray-400"></i> Tạo tài khoản mới
            </a>
        </div>
    </div>
</div>

<script>
(function () {
    var btn  = document.getElementById('togglePwd');
    var inp  = document.getElementById('password');
    var icon = document.getElementById('pwdIcon');
    if (btn) {
        btn.addEventListener('click', function () {
            var isText = inp.type === 'text';
            inp.type   = isText ? 'password' : 'text';
            icon.className = isText ? 'fa-solid fa-eye text-sm' : 'fa-solid fa-eye-slash text-sm';
        });
    }
})();
</script>

<?php require_once __DIR__ . '/partials/guest_footer.php'; ?>
