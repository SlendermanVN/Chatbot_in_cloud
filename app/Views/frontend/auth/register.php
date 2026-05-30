<?php
$pageTitle = 'Đăng ký tài khoản';
require_once __DIR__ . '/partials/guest_header.php';

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
            <h1 class="text-3xl font-black text-white">Tạo tài khoản miễn phí</h1>
            <p class="text-gray-500 mt-2 text-sm">
                Đã có tài khoản?
                <a href="<?= BASE_URL ?>/index.php?route=login" class="text-primary font-bold hover:underline">Đăng nhập ngay</a>
            </p>
        </div>

        <!-- Flash message -->
        <?php if ($flash): ?>
        <div class="mb-5 flex items-center gap-3 px-4 py-3.5 rounded-xl <?= $flashType === 'success' ? 'bg-green-500/10 border border-green-600/30 text-green-400' : 'bg-red-500/10 border border-red-600/30 text-red-400' ?>">
            <i class="fa-solid fa-circle-<?= $flashType === 'success' ? 'check' : 'exclamation' ?>"></i>
            <span class="text-sm"><?= htmlspecialchars($flash) ?></span>
        </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
        <div class="mb-5 flex items-center gap-3 px-4 py-3.5 rounded-xl bg-red-500/10 border border-red-600/30 text-red-400">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span class="text-sm"><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <!-- Form card -->
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 shadow-2xl">
            <form method="POST" action="<?= BASE_URL ?>/index.php?route=post_register" id="registerForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="space-y-4">

                    <!-- Full name -->
                    <div>
                        <label for="full_name" class="block text-sm font-semibold text-gray-400 mb-1.5">
                            Họ và tên <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                            <input id="full_name" name="full_name" type="text" required autocomplete="name"
                                placeholder="Nguyễn Văn A"
                                value="<?= htmlspecialchars($_SESSION['old']['full_name'] ?? '') ?>"
                                class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-primary transition-colors placeholder-gray-600">
                        </div>
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-semibold text-gray-400 mb-1.5">
                            Tên đăng nhập <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-at absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                            <input id="username" name="username" type="text" required autocomplete="username"
                                placeholder="nguyenvana123"
                                value="<?= htmlspecialchars($_SESSION['old']['username'] ?? '') ?>"
                                class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-primary transition-colors placeholder-gray-600">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-400 mb-1.5">
                            Địa chỉ Email <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                            <input id="email" name="email" type="email" required autocomplete="email"
                                placeholder="example@gmail.com"
                                value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>"
                                class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-primary transition-colors placeholder-gray-600">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-400 mb-1.5">
                            Mật khẩu <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                            <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"
                                placeholder="Ít nhất 8 ký tự"
                                class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl pl-11 pr-12 py-3 text-sm focus:outline-none focus:border-primary transition-colors placeholder-gray-600">
                            <button type="button" id="togglePwd"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                                <i class="fa-solid fa-eye text-sm" id="pwdIcon"></i>
                            </button>
                        </div>
                        <!-- Strength bar -->
                        <div class="mt-2 h-1 bg-gray-800 rounded-full overflow-hidden">
                            <div id="strengthBar" class="h-full rounded-full transition-all duration-300" style="width:0%"></div>
                        </div>
                        <div id="strengthLabel" class="text-xs text-gray-600 mt-1"></div>
                        <p class="text-[10px] text-gray-500 mt-2 italic leading-relaxed">
                            Mật khẩu phải từ 8 ký tự trở lên, bao gồm ít nhất một chữ hoa, một chữ thường, một con số và một ký tự đặc biệt (VD: @, #, $, !).
                        </p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-400 mb-1.5">
                            Nhập lại mật khẩu <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                placeholder="••••••••"
                                class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-primary transition-colors placeholder-gray-600">
                        </div>
                        <div id="matchHint" class="text-xs mt-1 hidden"></div>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start gap-3 pt-1">
                        <input id="terms" name="terms" type="checkbox" required
                            class="w-4 h-4 mt-0.5 rounded border-gray-600 bg-[#0b0f19] text-primary focus:ring-primary focus:ring-offset-0 flex-shrink-0 cursor-pointer">
                        <label for="terms" class="text-sm text-gray-500 cursor-pointer leading-relaxed">
                            Tôi đồng ý với
                            <a href="#" class="text-primary hover:underline font-medium">Điều khoản dịch vụ</a>
                            và
                            <a href="#" class="text-primary hover:underline font-medium">Chính sách bảo mật</a>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white font-extrabold py-3.5 rounded-xl transition-all shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-0.5 mt-2">
                        <i class="fa-solid fa-rocket"></i> Tạo tài khoản
                    </button>
                </div>
            </form>

            <?php unset($_SESSION['old']); ?>

            <div class="flex items-center gap-4 my-5">
                <div class="flex-1 h-px bg-gray-800"></div>
                <span class="text-xs text-gray-600 uppercase tracking-widest">hoặc</span>
                <div class="flex-1 h-px bg-gray-800"></div>
            </div>

            <a href="<?= BASE_URL ?>/index.php?route=login"
               class="w-full flex items-center justify-center gap-2 bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 rounded-xl border border-gray-700 transition-all text-sm">
                <i class="fa-solid fa-right-to-bracket text-gray-400"></i> Đăng nhập tài khoản cũ
            </a>
        </div>
    </div>
</div>

<script>
(function () {
    // Toggle password visibility
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

    // Password strength
    var bar   = document.getElementById('strengthBar');
    var lbl   = document.getElementById('strengthLabel');
    var conf  = document.getElementById('password_confirmation');
    var match = document.getElementById('matchHint');

    var levels = [
        { pct: 20,  bg: '#ef4444', text: 'Rất yếu'  },
        { pct: 40,  bg: '#f97316', text: 'Yếu'       },
        { pct: 60,  bg: '#eab308', text: 'Trung bình' },
        { pct: 80,  bg: '#22c55e', text: 'Mạnh'      },
        { pct: 100, bg: '#16a34a', text: 'Rất mạnh'  },
    ];

    if (inp && bar) {
        inp.addEventListener('input', function () {
            var v = this.value;
            var score = 0;
            if (v.length >= 8)                   score++;
            if (/[a-z]/.test(v) && /[A-Z]/.test(v)) score++;
            if (/\d/.test(v))                    score++;
            if (/[^a-zA-Z0-9]/.test(v))          score++;
            if (v.length >= 12)                  score++;
            var lv = levels[Math.min(score, 4)];
            bar.style.width      = lv.pct + '%';
            bar.style.background = lv.bg;
            if (lbl) { lbl.textContent = v.length ? lv.text : ''; lbl.style.color = lv.bg; }
            checkMatch();
        });
    }

    function checkMatch() {
        if (!conf || !match) return;
        if (!conf.value) { match.classList.add('hidden'); return; }
        var ok = inp.value === conf.value;
        match.classList.remove('hidden');
        match.textContent = ok ? '✓ Mật khẩu khớp' : '✗ Mật khẩu không khớp';
        match.style.color = ok ? '#22c55e' : '#ef4444';
    }

    if (conf) conf.addEventListener('input', checkMatch);
})();
</script>

<?php require_once __DIR__ . '/partials/guest_footer.php'; ?>
