<?php
$settings = $settings ?? [];
?>

<!-- Hero Section -->
<section class="relative overflow-hidden py-24 px-4 text-center">
    <div class="absolute inset-0 bg-gradient-to-b from-primary/10 via-transparent to-transparent pointer-events-none"></div>
    <div class="relative max-w-4xl mx-auto">
        <div class="inline-flex items-center gap-2 bg-primary/10 border border-primary/30 text-primary text-[11px] font-extrabold uppercase tracking-[0.2em] px-4 py-2 rounded-full mb-6 animate__animated animate__fadeInDown">
            <i class="fa-solid fa-bolt"></i> Câu chuyện thương hiệu
        </div>
        <h1 class="text-5xl md:text-7xl font-black text-white mb-6 leading-tight animate__animated animate__fadeInUp">
            <?= htmlspecialchars($settings['about_title'] ?? 'Về SportZone Vietnam') ?>
        </h1>
        <p class="text-gray-400 text-lg md:text-xl leading-relaxed max-w-2xl mx-auto animate__animated animate__fadeInUp animate__delay-1s">
            <?= nl2br(htmlspecialchars($settings['about_description'] ?? 'Chúng tôi không chỉ bán sản phẩm thể thao, chúng tôi cung cấp giải pháp để bạn chinh phục mọi giới hạn.')) ?>
        </p>
        <!-- Social Links from Settings -->
        <?php if (!empty($settings['social_facebook']) || !empty($settings['social_instagram']) || !empty($settings['social_youtube'])): ?>
        <div class="flex justify-center gap-4 mt-8">
            <?php if (!empty($settings['social_facebook'])): ?>
            <a href="<?= htmlspecialchars($settings['social_facebook']) ?>" target="_blank" rel="noopener"
               class="w-11 h-11 rounded-xl flex items-center justify-center bg-gray-900 border border-gray-800 text-gray-400 hover:bg-primary hover:border-primary hover:text-white transition-all">
                <i class="fa-brands fa-facebook-f"></i>
            </a>
            <?php endif; ?>
            <?php if (!empty($settings['social_instagram'])): ?>
            <a href="<?= htmlspecialchars($settings['social_instagram']) ?>" target="_blank" rel="noopener"
               class="w-11 h-11 rounded-xl flex items-center justify-center bg-gray-900 border border-gray-800 text-gray-400 hover:bg-primary hover:border-primary hover:text-white transition-all">
                <i class="fa-brands fa-instagram"></i>
            </a>
            <?php endif; ?>
            <?php if (!empty($settings['social_youtube'])): ?>
            <a href="<?= htmlspecialchars($settings['social_youtube']) ?>" target="_blank" rel="noopener"
               class="w-11 h-11 rounded-xl flex items-center justify-center bg-gray-900 border border-gray-800 text-gray-400 hover:bg-primary hover:border-primary hover:text-white transition-all">
                <i class="fa-brands fa-youtube"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Mission / Vision / Values -->
<div class="max-w-7xl mx-auto px-4 pb-16">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Mission -->
        <div class="group bg-gray-900 border border-gray-800 rounded-3xl p-8 hover:border-primary/40 transition-all hover:-translate-y-2">
            <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-rocket text-2xl text-primary"></i>
            </div>
            <h3 class="text-2xl font-black text-white mb-4">Sứ mệnh</h3>
            <p class="text-gray-500 leading-relaxed">
                <?= nl2br(htmlspecialchars($settings['about_mission'] ?? 'Mang đến những thiết bị thể thao chất lượng nhất để truyền cảm hứng cho lối sống lành mạnh và năng động.')) ?>
            </p>
        </div>

        <!-- Vision -->
        <div class="group bg-gray-900 border border-gray-800 rounded-3xl p-8 hover:border-primary/40 transition-all hover:-translate-y-2">
            <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-eye text-2xl text-blue-400"></i>
            </div>
            <h3 class="text-2xl font-black text-white mb-4">Tầm nhìn</h3>
            <p class="text-gray-500 leading-relaxed">
                <?= nl2br(htmlspecialchars($settings['about_vision'] ?? 'Trở thành điểm đến số 1 cho cộng đồng yêu thể thao tại Việt Nam với trải nghiệm mua sắm vượt trội.')) ?>
            </p>
        </div>

        <!-- Core Values -->
        <div class="group bg-gray-900 border border-gray-800 rounded-3xl p-8 hover:border-primary/40 transition-all hover:-translate-y-2">
            <div class="w-14 h-14 bg-green-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-gem text-2xl text-green-400"></i>
            </div>
            <h3 class="text-2xl font-black text-white mb-4">Giá trị cốt lõi</h3>
            <p class="text-gray-500 leading-relaxed">
                <?= nl2br(htmlspecialchars($settings['about_values'] ?? 'Chất lượng hàng đầu - Phục vụ tận tâm - Sáng tạo không ngừng - Trách nhiệm cộng đồng.')) ?>
            </p>
        </div>

    </div>
</div>

<!-- Stats / Trust Section -->
<div class="max-w-7xl mx-auto px-4 pb-16">
    <div class="bg-gradient-to-br from-gray-900 to-black border border-gray-800 rounded-[40px] p-12 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-[100px]"></div>
        <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl md:text-5xl font-black text-white mb-2">10k+</div>
                <div class="text-gray-500 text-sm font-bold uppercase tracking-widest">Khách hàng</div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-black text-primary mb-2">500+</div>
                <div class="text-gray-500 text-sm font-bold uppercase tracking-widest">Sản phẩm</div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-black text-white mb-2">15+</div>
                <div class="text-gray-500 text-sm font-bold uppercase tracking-widest">Thương hiệu</div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-black text-primary mb-2">24/7</div>
                <div class="text-gray-500 text-sm font-bold uppercase tracking-widest">Hỗ trợ</div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Info Section (from Settings) -->
<?php if (!empty($settings['store_address']) || !empty($settings['contact_phone']) || !empty($settings['contact_email'])): ?>
<div class="max-w-7xl mx-auto px-4 pb-24">
    <h2 class="text-2xl font-black text-white text-center mb-10">
        Liên hệ với <span class="text-primary"><?= htmlspecialchars($settings['site_name'] ?? 'SportZone Vietnam') ?></span>
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if (!empty($settings['store_address'])): ?>
        <div class="flex flex-col items-center gap-4 bg-gray-900 border border-gray-800 rounded-2xl p-8 text-center">
            <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center">
                <i class="fa-solid fa-location-dot text-2xl text-primary"></i>
            </div>
            <h4 class="text-white font-bold">Địa chỉ</h4>
            <p class="text-gray-500 text-sm"><?= htmlspecialchars($settings['store_address']) ?></p>
        </div>
        <?php endif; ?>
        <?php if (!empty($settings['contact_phone'])): ?>
        <div class="flex flex-col items-center gap-4 bg-gray-900 border border-gray-800 rounded-2xl p-8 text-center">
            <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center">
                <i class="fa-solid fa-phone text-2xl text-primary"></i>
            </div>
            <h4 class="text-white font-bold">Hotline</h4>
            <a href="tel:<?= htmlspecialchars($settings['contact_phone']) ?>"
               class="text-primary font-bold text-lg hover:underline">
                <?= htmlspecialchars($settings['contact_phone']) ?>
            </a>
        </div>
        <?php endif; ?>
        <?php if (!empty($settings['contact_email'])): ?>
        <div class="flex flex-col items-center gap-4 bg-gray-900 border border-gray-800 rounded-2xl p-8 text-center">
            <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center">
                <i class="fa-solid fa-envelope text-2xl text-primary"></i>
            </div>
            <h4 class="text-white font-bold">Email hỗ trợ</h4>
            <a href="mailto:<?= htmlspecialchars($settings['contact_email']) ?>"
               class="text-primary text-sm hover:underline break-all">
                <?= htmlspecialchars($settings['contact_email']) ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>