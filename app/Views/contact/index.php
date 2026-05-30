<?php
$setting = $setting ?? [];

// Flash message
$flash = $flashType = null;
if (!empty($_SESSION['flash'])) {
    $flash     = $_SESSION['flash']['message'] ?? '';
    $flashType = $_SESSION['flash']['type']    ?? 'success';
    unset($_SESSION['flash']);
}
?>

<!-- Hero -->
<section class="relative overflow-hidden py-20 px-4 text-center">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-primary/10 rounded-full blur-[120px]"></div>
    </div>
    <div class="relative max-w-2xl mx-auto">
        <div class="inline-flex items-center gap-2 bg-primary/10 border border-primary/30 text-primary text-[11px] font-extrabold uppercase tracking-[0.2em] px-4 py-2 rounded-full mb-6">
            <i class="fa-solid fa-comment-dots"></i> Liên hệ với chúng tôi
        </div>
        <h1 class="text-4xl md:text-5xl font-black text-white mb-4 leading-tight">
            Chúng tôi <span class="text-primary">luôn sẵn sàng</span> hỗ trợ bạn
        </h1>
        <p class="text-gray-400 text-lg">
            Gửi câu hỏi hoặc yêu cầu tư vấn — đội ngũ SportZone sẽ phản hồi trong vòng 24 giờ.
        </p>
    </div>
</section>

<div class="max-w-6xl mx-auto px-4 pb-24">

    <!-- Flash Message -->
    <?php if ($flash): ?>
    <div class="mb-8 flex items-center gap-3 px-5 py-4 rounded-2xl <?= $flashType === 'success' ? 'bg-green-500/10 border border-green-600/30 text-green-400' : 'bg-red-500/10 border border-red-600/30 text-red-400' ?>">
        <i class="fa-solid fa-circle-<?= $flashType === 'success' ? 'check' : 'exclamation' ?> text-xl"></i>
        <span class="font-medium"><?= htmlspecialchars($flash) ?></span>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

        <!-- Contact Form (3 cols) -->
        <div class="lg:col-span-3">
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8 shadow-2xl">
                <h2 class="text-2xl font-black text-white mb-2">Gửi yêu cầu</h2>
                <p class="text-gray-500 text-sm mb-8">Điền đầy đủ thông tin bên dưới, chúng tôi sẽ phản hồi sớm nhất.</p>

                <?php if (!empty($_SESSION['errors'])): ?>
                <div class="bg-red-500/10 border border-red-600/30 text-red-400 rounded-xl p-4 mb-6 text-sm">
                    <?php foreach ($_SESSION['errors'] as $e): ?>
                        <div class="flex items-center gap-2"><i class="fa-solid fa-circle-exclamation"></i><?= htmlspecialchars($e) ?></div>
                    <?php endforeach; unset($_SESSION['errors']); ?>
                </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/index.php?route=contact_submit" method="POST" id="contactForm" novalidate>
                    <div class="space-y-5">

                        <!-- Name + Type -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-400 mb-2">
                                    Họ & Tên <span class="text-red-500">*</span>
                                </label>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <input type="text" name="fullname" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" readonly
                                        class="w-full bg-[#0b0f19] border border-gray-700/50 text-gray-400 rounded-xl px-4 py-3 text-sm cursor-not-allowed">
                                <?php else: ?>
                                    <input type="text" name="fullname" id="cf_name" required maxlength="100"
                                        placeholder="Nguyễn Văn A"
                                        value="<?= htmlspecialchars($_SESSION['old']['fullname'] ?? '') ?>"
                                        class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-colors placeholder-gray-600 text-sm">
                                <?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-400 mb-2">Loại khách hàng</label>
                                <select name="customer_type" class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-colors appearance-none text-sm">
                                    <option value="ca_nhan" <?= (($_SESSION['old']['customer_type'] ?? '') === 'ca_nhan') ? 'selected' : '' ?>>Cá nhân</option>
                                    <option value="doanh_nghiep" <?= (($_SESSION['old']['customer_type'] ?? '') === 'doanh_nghiep') ? 'selected' : '' ?>>Doanh nghiệp</option>
                                    <option value="club" <?= (($_SESSION['old']['customer_type'] ?? '') === 'club') ? 'selected' : '' ?>>CLB / Nhóm thể thao</option>
                                </select>
                            </div>
                        </div>

                        <!-- Company -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-400 mb-2">Tên công ty / CLB <span class="text-gray-600 font-normal">(tuỳ chọn)</span></label>
                            <input type="text" name="company_name" maxlength="200"
                                placeholder="SportClub Hà Nội..."
                                value="<?= htmlspecialchars($_SESSION['old']['company_name'] ?? '') ?>"
                                class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-colors placeholder-gray-600 text-sm">
                        </div>

                        <!-- Contact method -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-400 mb-2">Phương thức liên lạc</label>
                                <select name="method_contact_type" id="cf_method" class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-colors appearance-none text-sm">
                                    <option value="email">📧 Email</option>
                                    <option value="phone">📞 Điện thoại</option>
                                    <option value="zalo">💬 Zalo</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-400 mb-2" id="cf_value_label">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['email'])): ?>
                                    <input type="text" name="contact_value" value="<?= htmlspecialchars($_SESSION['email']) ?>" readonly
                                        class="w-full bg-[#0b0f19] border border-gray-700/50 text-gray-400 rounded-xl px-4 py-3 text-sm cursor-not-allowed">
                                <?php else: ?>
                                    <input type="text" name="contact_value" id="cf_value" required
                                        placeholder="example@gmail.com"
                                        value="<?= htmlspecialchars($_SESSION['old']['contact_value'] ?? '') ?>"
                                        class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-colors placeholder-gray-600 text-sm">
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Message -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-400 mb-2">
                                Nội dung <span class="text-red-500">*</span>
                                <span class="float-right text-gray-600 font-normal text-xs"><span id="msgLen">0</span>/1000</span>
                            </label>
                            <textarea name="contact_message" id="cf_message" rows="5" required maxlength="1000"
                                placeholder="Mô tả chi tiết yêu cầu hoặc câu hỏi của bạn..."
                                class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-colors placeholder-gray-600 text-sm resize-none"><?= htmlspecialchars($_SESSION['old']['contact_message'] ?? '') ?></textarea>
                            <?php unset($_SESSION['old']); ?>
                        </div>

                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white font-extrabold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-0.5 text-sm tracking-wide">
                            <i class="fa-solid fa-paper-plane"></i> Gửi yêu cầu
                        </button>

                        <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center gap-3 bg-[#0b0f19] border border-gray-700/50 rounded-xl px-4 py-3">
                            <i class="fa-solid fa-circle-info text-blue-400 text-sm"></i>
                            <p class="text-xs text-gray-400">
                                <a href="<?= BASE_URL ?>/index.php?route=login" class="text-primary font-bold hover:underline">Đăng nhập</a>
                                hoặc <a href="<?= BASE_URL ?>/index.php?route=register" class="text-primary font-bold hover:underline">đăng ký</a>
                                để điền thông tin tự động và theo dõi phản hồi.
                            </p>
                        </div>
                        <?php else: ?>
                        <div class="flex items-center gap-3 bg-green-500/5 border border-green-600/20 rounded-xl px-4 py-3">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username'] ?? 'U') ?>&background=ff6600&color=fff" class="w-7 h-7 rounded-full flex-shrink-0">
                            <p class="text-xs text-gray-400">
                                Gửi như <span class="text-white font-semibold"><?= htmlspecialchars($_SESSION['username']) ?></span>. Phản hồi sẽ được gửi đến contact của bạn.
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Panel (2 cols) -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Contact Info -->
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-7 shadow-xl">
                <h3 class="text-white font-bold text-lg mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-primary"></i> Thông tin liên hệ
                </h3>
                <div class="space-y-5">
                    <?php
                    $infos = [
                        ['icon'=>'fa-location-dot',  'color'=>'text-red-400',    'bg'=>'bg-red-500/10',    'label'=>'Địa chỉ',     'value'=> $setting['address'] ?? '123 Đường Thể Thao, Quận 1, TP.HCM'],
                        ['icon'=>'fa-phone',          'color'=>'text-green-400',  'bg'=>'bg-green-500/10',  'label'=>'Điện thoại',  'value'=> $setting['phone']   ?? '0909 123 456'],
                        ['icon'=>'fa-envelope',       'color'=>'text-blue-400',   'bg'=>'bg-blue-500/10',   'label'=>'Email',       'value'=> $setting['email']   ?? 'support@sportzone.vn'],
                        ['icon'=>'fa-clock',          'color'=>'text-amber-400',  'bg'=>'bg-amber-500/10',  'label'=>'Giờ làm việc','value'=> 'Thứ 2 – Thứ 7: 8:00 – 20:00'],
                    ];
                    foreach ($infos as $info): ?>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 <?= $info['bg'] ?> rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid <?= $info['icon'] ?> <?= $info['color'] ?> text-base"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-0.5"><?= $info['label'] ?></div>
                            <div class="text-white text-sm font-medium"><?= htmlspecialchars($info['value']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Social Media -->
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-7 shadow-xl">
                <h3 class="text-white font-bold text-base mb-4">Kết nối với chúng tôi</h3>
                <div class="grid grid-cols-3 gap-3">
                    <?php
                    $socials = [
                        ['icon'=>'fa-brands fa-facebook', 'color'=>'text-blue-500',  'bg'=>'bg-blue-500/10',   'border'=>'border-blue-600/20', 'label'=>'Facebook'],
                        ['icon'=>'fa-brands fa-instagram','color'=>'text-pink-500',  'bg'=>'bg-pink-500/10',   'border'=>'border-pink-600/20',  'label'=>'Instagram'],
                        ['icon'=>'fa-brands fa-tiktok',   'color'=>'text-white',     'bg'=>'bg-gray-800',      'border'=>'border-gray-700',     'label'=>'TikTok'],
                    ];
                    foreach ($socials as $s): ?>
                    <button class="<?= $s['bg'] ?> border <?= $s['border'] ?> rounded-2xl p-4 flex flex-col items-center gap-2 hover:scale-105 transition-transform">
                        <i class="<?= $s['icon'] ?> <?= $s['color'] ?> text-xl"></i>
                        <span class="text-xs text-gray-400"><?= $s['label'] ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- FAQ CTA -->
            <div class="bg-gradient-to-br from-primary/15 to-transparent border border-primary/20 rounded-3xl p-7 text-center shadow-xl">
                <i class="fa-solid fa-circle-question text-3xl text-primary mb-3 block"></i>
                <h3 class="text-white font-bold mb-2">Tìm câu trả lời nhanh hơn?</h3>
                <p class="text-gray-500 text-sm mb-4">Tham khảo trang FAQ để giải đáp các câu hỏi thường gặp.</p>
                <a href="<?= BASE_URL ?>/index.php?route=faqs" class="inline-flex items-center gap-2 text-primary font-bold text-sm hover:underline">
                    Xem trang FAQ <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // Đếm ký tự
    var msg = document.getElementById('cf_message');
    var len = document.getElementById('msgLen');
    if (msg && len) {
        msg.addEventListener('input', function () { len.textContent = this.value.length; });
        len.textContent = msg.value.length;
    }

    // Đổi label theo phương thức liên lạc
    var method = document.getElementById('cf_method');
    var label  = document.getElementById('cf_value_label');
    var input  = document.getElementById('cf_value');
    var labels = { email: 'Email', phone: 'Số điện thoại', zalo: 'Số Zalo' };
    var placeholders = { email: 'example@gmail.com', phone: '0909 123 456', zalo: '0909 123 456' };
    if (method) {
        method.addEventListener('change', function () {
            var v = this.value;
            if (label) label.innerHTML = (labels[v] || 'Thông tin liên lạc') + ' <span style="color:#ef4444">*</span>';
            if (input) input.placeholder = placeholders[v] || '';
        });
    }

    // Validate client-side
    var form = document.getElementById('contactForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            var name    = document.getElementById('cf_name');
            var value   = document.getElementById('cf_value');
            var message = document.getElementById('cf_message');
            var errs = [];
            if (name && name.value.trim().length < 2)    errs.push('Vui lòng nhập họ tên.');
            if (value && value.value.trim().length < 5)   errs.push('Vui lòng nhập thông tin liên lạc.');
            if (message && message.value.trim().length < 10) errs.push('Nội dung quá ngắn (ít nhất 10 ký tự).');
            if (errs.length) { e.preventDefault(); alert(errs.join('\n')); }
        });
    }
})();
</script>