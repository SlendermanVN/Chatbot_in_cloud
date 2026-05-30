<?php
$pageTitle = 'Built for Champions';
$ls = $landingSettings ?? []; // shorthand for landing settings
require_once __DIR__ . '/../frontend/auth/partials/guest_header.php';
?>

<style>
    @keyframes marquee {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(-25%);
        }
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-12px);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hero-animate {
        animation: fadeInUp 0.8s ease forwards;
    }

    .hero-animate-2 {
        animation: fadeInUp 0.8s ease 0.15s both;
    }

    .hero-animate-3 {
        animation: fadeInUp 0.8s ease 0.3s both;
    }

    .float-badge {
        animation: float 4s ease-in-out infinite;
    }

    .float-badge-2 {
        animation: float 4s ease-in-out 1.5s infinite;
    }

    .hover-lift {
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(0, 0, 0, .35);
    }

    body {
        overflow-x: hidden;
    }
</style>

<!-- ===== HERO ===== -->
<section class="relative min-h-screen flex items-center pt-16 overflow-hidden">
    <!-- Ambient -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-60 -left-60 w-[800px] h-[800px] bg-primary/15 rounded-full blur-[150px]"></div>
        <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-blue-900/20 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 w-full relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <!-- Text -->
            <div>
                <div
                    class="hero-animate inline-flex items-center gap-2 bg-primary/10 border border-primary/40 text-primary text-[11px] font-extrabold uppercase tracking-[0.25em] px-5 py-2.5 rounded-full mb-8 shadow-lg shadow-primary/20">
                    <span class="w-1.5 h-1.5 bg-primary rounded-full animate-pulse"></span>
                    Vietnam's #1 Sports Performance Store
                </div>
                <h1 class="hero-animate-2 text-6xl md:text-7xl font-black text-white leading-[1] tracking-tighter mb-6">
                    <?= htmlspecialchars($ls['about_title'] ?? 'Built for') ?>
                </h1>
                <p class="hero-animate-3 text-gray-400 text-lg max-w-md mb-10 leading-relaxed">
                    <?= htmlspecialchars($ls['about_description'] ?? 'Trang bị thiết bị thể thao cao cấp — chính hãng, giao nhanh, bảo hành toàn diện.') ?><br>
                    Hơn <strong class="text-white">10,000</strong> vận động viên Việt Nam đang tin tưởng
                    <?= htmlspecialchars($ls['site_name'] ?? 'SportZone') ?>.
                </p>
                <div class="hero-animate-3 flex flex-wrap gap-4 mb-12">
                    <a href="<?= BASE_URL ?>/index.php?route=register"
                        class="group inline-flex items-center gap-2.5 bg-primary hover:bg-primary-dark text-white font-extrabold text-base px-9 py-4 rounded-2xl transition-all shadow-2xl shadow-primary/40 hover:-translate-y-1">
                        <i class="fa-solid fa-rocket group-hover:rotate-12 transition-transform"></i>
                        Bắt đầu ngay — Miễn phí
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?route=login"
                        class="inline-flex items-center gap-2.5 bg-gray-900 hover:bg-gray-800 text-white font-bold text-base px-9 py-4 rounded-2xl border border-gray-700 hover:border-primary/50 transition-all">
                        <i class="fa-solid fa-right-to-bracket text-gray-400"></i>
                        Đăng nhập
                    </a>
                </div>
                <!-- Trust -->
                <div class="hero-animate-3 flex flex-wrap gap-5 text-sm text-gray-500">
                    <div class="flex items-center gap-2"><i class="fa-solid fa-shield-halved text-green-400"></i> Chính
                        hãng 100%</div>
                    <div class="flex items-center gap-2"><i class="fa-solid fa-truck-fast text-blue-400"></i> Giao nhanh
                        24h</div>
                    <div class="flex items-center gap-2"><i class="fa-solid fa-arrows-rotate text-amber-400"></i> Đổi
                        trả 30 ngày</div>
                </div>
            </div>

            <!-- Visual Panel -->
            <div class="relative hidden lg:flex items-center justify-center">
                <div class="relative w-full max-w-md aspect-square">
                    <!-- Main card -->
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-primary/20 via-gray-900 to-gray-900 rounded-[40px] border border-primary/20 shadow-2xl overflow-hidden">
                        <div class="absolute inset-0 flex items-center justify-center flex-col gap-2">
                            <i class="fa-solid fa-bolt text-[120px] text-primary/20"></i>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                            <i class="fa-solid fa-bolt text-7xl text-primary mb-3"
                                style="filter: drop-shadow(0 0 40px rgba(255,102,0,0.6))"></i>
                            <div class="text-primary font-black italic tracking-widest">
                                <?= htmlspecialchars($ls['site_name'] ?? 'SportZone') ?></div>
                            <span class="text-gray-600 text-xs tracking-[0.3em] uppercase mt-1">High Performance</span>
                        </div>
                    </div>
                    <!-- Floating badge 1 -->
                    <div
                        class="float-badge absolute -top-4 -right-4 bg-gray-900 border border-gray-700 rounded-2xl px-4 py-3 shadow-2xl">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-white text-xs font-bold">Giao hàng hôm nay</span>
                        </div>
                    </div>
                    <!-- Floating badge 2 -->
                    <div
                        class="float-badge-2 absolute -bottom-4 -left-4 bg-gray-900 border border-gray-700 rounded-2xl px-4 py-3 shadow-2xl">
                        <div class="text-xs text-gray-500 mb-0.5">Đã bán tuần này</div>
                        <div class="text-white font-black text-xl">1,247 <span
                                class="text-primary text-sm font-bold">↑23%</span></div>
                    </div>
                    <!-- Floating badge 3 -->
                    <div
                        class="float-badge absolute bottom-16 -right-8 bg-yellow-400/10 border border-yellow-400/30 rounded-2xl px-3 py-2 shadow-xl">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                            <span class="text-yellow-400 text-xs font-bold">4.9 / 5.0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div
        class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-gray-600 animate-bounce">
        <span class="text-[10px] uppercase tracking-widest">Cuộn xuống</span>
        <i class="fa-solid fa-chevron-down text-xs"></i>
    </div>
</section>

<!-- ===== BRAND STRIP ===== -->
<div class="border-y border-gray-800/60 bg-gray-900/40 py-4 overflow-hidden">
    <div class="flex gap-16 items-center whitespace-nowrap"
        style="animation: marquee 22s linear infinite; width: max-content;">
        <?php for ($i = 0; $i < 4; $i++): ?>
            <span class="text-gray-700 text-xs font-black uppercase tracking-[0.3em]">NIKE</span><span
                class="text-primary/50 mx-4">✦</span>
            <span class="text-gray-700 text-xs font-black uppercase tracking-[0.3em]">ADIDAS</span><span
                class="text-primary/50 mx-4">✦</span>
            <span class="text-gray-700 text-xs font-black uppercase tracking-[0.3em]">PUMA</span><span
                class="text-primary/50 mx-4">✦</span>
            <span class="text-gray-700 text-xs font-black uppercase tracking-[0.3em]">UNDER ARMOUR</span><span
                class="text-primary/50 mx-4">✦</span>
            <span class="text-gray-700 text-xs font-black uppercase tracking-[0.3em]">REEBOK</span><span
                class="text-primary/50 mx-4">✦</span>
            <span class="text-gray-700 text-xs font-black uppercase tracking-[0.3em]">NEW BALANCE</span><span
                class="text-primary/50 mx-4">✦</span>
        <?php endfor; ?>
    </div>
</div>

<!-- ===== FEATURES ===== -->
<section class="py-28 px-4" id="features">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
            <div class="text-xs text-primary font-extrabold uppercase tracking-[0.25em] mb-3">Tại sao chọn chúng tôi
            </div>
            <h2 class="text-4xl font-black text-white">Cam kết của
                <?= htmlspecialchars($ls['site_name'] ?? 'SportZone Vietnam') ?></h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <?php
            $features = [
                ['icon' => 'fa-shield-halved', 'color' => 'text-green-400', 'bg' => 'bg-green-500/10', 'border' => 'border-green-600/20', 'title' => 'Chính hãng 100%', 'desc' => 'Mọi sản phẩm đều có tem xác thực chính hãng, hoàn tiền nếu hàng giả.'],
                ['icon' => 'fa-truck-fast', 'color' => 'text-blue-400', 'bg' => 'bg-blue-500/10', 'border' => 'border-blue-600/20', 'title' => 'Giao hàng nhanh', 'desc' => '24h tại TP.HCM, 2-3 ngày toàn quốc. Miễn phí cho đơn hàng trên 500K.'],
                ['icon' => 'fa-arrows-rotate', 'color' => 'text-amber-400', 'bg' => 'bg-amber-500/10', 'border' => 'border-amber-600/20', 'title' => 'Đổi trả 30 ngày', 'desc' => 'Hoàn toàn miễn phí đổi/trả trong 30 ngày nếu có lỗi từ nhà sản xuất.'],
                ['icon' => 'fa-headset', 'color' => 'text-purple-400', 'bg' => 'bg-purple-500/10', 'border' => 'border-purple-600/20', 'title' => 'Hỗ trợ 24/7', 'desc' => 'Tư vấn qua Zalo, điện thoại và chat trực tuyến bất cứ lúc nào.'],
            ];
            foreach ($features as $f): ?>
                <div
                    class="group hover-lift <?= $f['bg'] ?> border <?= $f['border'] ?> rounded-3xl p-7 shadow-xl overflow-hidden relative">
                    <div
                        class="absolute top-0 right-0 w-24 h-24 <?= $f['bg'] ?> rounded-full -translate-y-8 translate-x-8 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 <?= $f['bg'] ?> rounded-2xl flex items-center justify-center mb-5">
                            <i class="fa-solid <?= $f['icon'] ?> text-2xl <?= $f['color'] ?>"></i>
                        </div>
                        <h3 class="font-extrabold text-white text-lg mb-2"><?= $f['title'] ?></h3>
                        <p class="text-gray-500 text-sm leading-relaxed"><?= $f['desc'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== STATS ===== -->
<section class="py-16 px-4">
    <div class="max-w-5xl mx-auto">
        <div
            class="bg-gradient-to-br from-gray-900 via-[#0c1220] to-gray-900 border border-gray-800 rounded-3xl p-12 grid grid-cols-2 md:grid-cols-4 gap-8 text-center shadow-2xl">
            <?php foreach ([['500+', 'Sản phẩm', 'đang bán'], ['10K+', 'Khách hàng', 'tin tưởng'], ['4.9★', 'Đánh giá', 'trung bình'], ['24h', 'Giao hàng', 'TP.HCM']] as $s): ?>
                <div>
                    <div
                        class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-b from-white to-primary mb-2">
                        <?= $s[0] ?></div>
                    <div class="text-white font-bold"><?= $s[1] ?></div>
                    <div class="text-gray-600 text-xs uppercase tracking-widest mt-0.5"><?= $s[2] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== FEATURED PRODUCTS ===== -->
<?php if (!empty($latestProducts)): ?>
<section class="py-24 px-4 bg-[#0b0f19]">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <div class="text-xs text-primary font-extrabold uppercase tracking-[0.25em] mb-3">Explore Gear</div>
                <h2 class="text-4xl font-black text-white">Sản phẩm nổi bật</h2>
            </div>
            <a href="<?= BASE_URL ?>/index.php?route=products" class="group inline-flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-white transition-colors">
                Xem tất cả cửa hàng <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($latestProducts as $p): ?>
                <a href="<?= BASE_URL ?>/index.php?route=product_detail&id=<?= $p['id'] ?>" class="group hover-lift bg-gray-900/50 border border-gray-800 rounded-[2rem] overflow-hidden flex flex-col">
                    <div class="aspect-[4/5] bg-gray-800 relative overflow-hidden">
                        <?php if(!empty($p['primary_image'])): ?>
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($p['primary_image']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="<?= htmlspecialchars($p['name']) ?>">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center"><i class="fa-solid fa-image text-4xl text-gray-700"></i></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($p['sale_price']) && $p['sale_price'] < $p['price']): ?>
                            <div class="absolute top-4 left-4 bg-primary text-white text-[10px] font-black px-3 py-1 rounded-full shadow-lg">SALE</div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <div class="text-[10px] text-gray-500 uppercase tracking-widest mb-1 font-bold"><?= htmlspecialchars($p['category_name'] ?? 'Sport Gear') ?></div>
                        <h3 class="text-white font-bold group-hover:text-primary transition-colors line-clamp-1 mb-3"><?= htmlspecialchars($p['name']) ?></h3>
                        <div class="flex items-center justify-between mt-auto">
                            <div class="flex flex-col">
                                <?php if (!empty($p['sale_price']) && $p['sale_price'] < $p['price']): ?>
                                    <span class="text-primary font-black"><?= number_format($p['sale_price'], 0, ',', '.') ?>đ</span>
                                    <span class="text-gray-600 text-[10px] line-through"><?= number_format($p['price'], 0, ',', '.') ?>đ</span>
                                <?php else: ?>
                                    <span class="text-white font-black"><?= number_format($p['price'], 0, ',', '.') ?>đ</span>
                                <?php endif; ?>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-gray-400 group-hover:bg-primary group-hover:text-white transition-all">
                                <i class="fa-solid fa-cart-plus"></i>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== HOW IT WORKS ===== -->
<section class="py-28 px-4" id="how">
    <div class="max-w-4xl mx-auto text-center">
        <div class="text-xs text-primary font-extrabold uppercase tracking-[0.25em] mb-3">Đơn giản & nhanh chóng</div>
        <h2 class="text-4xl font-black text-white mb-16">Bắt đầu chỉ trong 3 bước</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
            <div
                class="hidden md:block absolute top-10 left-1/6 right-1/6 h-px bg-gradient-to-r from-transparent via-gray-700 to-transparent">
            </div>
            <?php
            $steps = [
                ['step' => '01', 'icon' => 'fa-user-plus', 'color' => 'text-primary', 'bg' => 'bg-primary/10', 'title' => 'Tạo tài khoản', 'desc' => 'Đăng ký nhanh trong 30 giây. Miễn phí hoàn toàn.'],
                ['step' => '02', 'icon' => 'fa-bag-shopping', 'color' => 'text-blue-400', 'bg' => 'bg-blue-500/10', 'title' => 'Chọn sản phẩm', 'desc' => 'Duyệt qua 500+ sản phẩm thể thao cao cấp.'],
                ['step' => '03', 'icon' => 'fa-truck-fast', 'color' => 'text-green-400', 'bg' => 'bg-green-500/10', 'title' => 'Nhận hàng tận nơi', 'desc' => 'Giao toàn quốc, đóng gói cẩn thận, theo dõi đơn hàng thời gian thực.'],
            ];
            foreach ($steps as $s): ?>
                <div class="flex flex-col items-center text-center">
                    <div
                        class="relative w-20 h-20 <?= $s['bg'] ?> rounded-3xl flex items-center justify-center mb-5 shadow-lg">
                        <i class="fa-solid <?= $s['icon'] ?> text-2xl <?= $s['color'] ?>"></i>
                        <span
                            class="absolute -top-2 -right-2 w-6 h-6 bg-primary text-white text-[10px] font-black rounded-full flex items-center justify-center"><?= $s['step'] ?></span>
                    </div>
                    <h3 class="font-extrabold text-white text-lg mb-2"><?= $s['title'] ?></h3>
                    <p class="text-gray-500 text-sm leading-relaxed"><?= $s['desc'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== TESTIMONIALS ===== -->
<section class="py-20 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-14">
            <div class="text-xs text-primary font-extrabold uppercase tracking-[0.25em] mb-3">Cộng đồng nói gì</div>
            <h2 class="text-3xl font-black text-white">Khách hàng tin tưởng chúng tôi</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <?php foreach ([
                ['Nguyễn Minh Tuấn', 'Vận động viên bóng rổ', 'Sản phẩm vượt mong đợi! Giao hàng đúng hẹn, đóng gói cẩn thận. Sẽ tiếp tục ủng hộ SportZone.'],
                ['Trần Thu Hà', 'Gym & Fitness', 'Mua giày tập tại đây, chính hãng 100%, giá tốt hơn cửa hàng bình thường. Hỗ trợ rất nhiệt tình!'],
                ['Lê Văn Khoa', 'CLB Bóng đá Quận 3', 'Đặt đồng phục cho cả CLB, tư vấn tận tình và giao đúng tiến độ. Rất hài lòng!'],
            ] as $r): ?>
                <div
                    class="hover-lift bg-gray-900 border border-gray-800 rounded-3xl p-6 hover:border-primary/30 transition-colors">
                    <div class="flex gap-0.5 mb-4">
                        <?php for ($i = 0; $i < 5; $i++): ?><i
                                class="fa-solid fa-star text-yellow-400 text-xs"></i><?php endfor; ?>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-5">"<?= $r[2] ?>"</p>
                    <div class="flex items-center gap-3 border-t border-gray-800 pt-4">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($r[0]) ?>&background=1f2937&color=ff6600"
                            class="w-9 h-9 rounded-full">
                        <div>
                            <div class="text-white font-bold text-sm"><?= $r[0] ?></div>
                            <div class="text-gray-600 text-xs"><?= $r[1] ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== LATEST NEWS ===== -->
<?php if (!empty($latestNews)): ?>
<section class="py-24 px-4 border-t border-gray-800/50">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <div class="text-xs text-primary font-extrabold uppercase tracking-[0.25em] mb-3">Community & Tips</div>
                <h2 class="text-4xl font-black text-white">Tin tức & Sự kiện</h2>
            </div>
            <a href="<?= BASE_URL ?>/index.php?route=news" class="group inline-flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-white transition-colors">
                Xem tất cả bài viết <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($latestNews as $news): ?>
                <a href="<?= BASE_URL ?>/index.php?route=news_detail&id=<?= $news['id'] ?>" class="group flex flex-col h-full bg-gray-900/30 border border-gray-800 rounded-3xl overflow-hidden hover:border-primary/40 transition-all hover-lift">
                    <div class="aspect-video relative overflow-hidden bg-gray-800">
                        <?php if(!empty($news['thumbnail'])): ?>
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($news['thumbnail']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="<?= htmlspecialchars($news['title']) ?>">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-700"><i class="fa-solid fa-newspaper text-5xl"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center gap-3 text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-3">
                            <span><i class="fa-regular fa-clock mr-1 text-primary"></i> <?= date('d/m/Y', strtotime($news['created_at'])) ?></span>
                            <span class="w-1 h-1 bg-gray-700 rounded-full"></span>
                            <span>Bởi Admin</span>
                        </div>
                        <h3 class="text-white font-bold text-lg leading-snug group-hover:text-primary transition-colors line-clamp-2 mb-4"><?= htmlspecialchars($news['title']) ?></h3>
                        <div class="mt-auto flex items-center gap-2 text-primary text-xs font-bold uppercase tracking-widest">
                            Đọc bài viết <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== FINAL CTA ===== -->
<section class="py-28 px-4">
    <div class="max-w-4xl mx-auto">
        <div
            class="relative overflow-hidden bg-gradient-to-br from-primary/20 via-primary/10 to-[#0c1220] border border-primary/30 rounded-3xl p-16 text-center shadow-2xl">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-80 h-80 bg-primary/25 rounded-full blur-[100px]">
                </div>
            </div>
            <div class="relative z-10">
                <i class="fa-solid fa-bolt text-6xl text-primary mb-6 block"
                    style="filter: drop-shadow(0 0 30px rgba(255,102,0,0.7))"></i>
                <h2 class="text-4xl md:text-5xl font-black text-white mb-4 leading-tight">Sẵn sàng thi đấu ở<br>đẳng cấp
                    cao hơn?</h2>
                <p class="text-gray-400 text-lg mb-10 max-w-xl mx-auto">Tham gia miễn phí ngay hôm nay và nhận ưu đãi
                    chào mừng thành viên mới.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?= BASE_URL ?>/index.php?route=register"
                        class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white font-extrabold text-base px-12 py-4 rounded-2xl transition-all shadow-2xl shadow-primary/50 hover:-translate-y-1">
                        <i class="fa-solid fa-rocket"></i> Tạo tài khoản miễn phí
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?route=login"
                        class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/15 text-white font-bold text-base px-12 py-4 rounded-2xl border border-white/20 transition-all">
                        Đã có tài khoản? Đăng nhập
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../frontend/auth/partials/guest_footer.php'; ?>