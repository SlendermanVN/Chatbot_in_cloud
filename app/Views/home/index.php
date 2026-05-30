<?php
$topCategories  = $topCategories  ?? [];
$latestProducts = $latestProducts ?? [];
$latestNews     = $latestNews     ?? [];
$s              = $setting        ?? []; // Shorthand for settings

// Icon mapping by category slug/name
$catIcons = [
    'bong-da'  => ['fa-futbol',          'Bóng đá'],
    'bong-ro'  => ['fa-basketball',      'Bóng rổ'],
    'cau-long' => ['fa-badminton',       'Cầu lông'],
    'tennis'   => ['fa-baseball',        'Tennis'],
    'gym'      => ['fa-dumbbell',        'Gym & Fitness'],
];
?>

<style>
    @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-33.33%); } }
</style>

<!-- ===== HERO SECTION ===== -->
<section class="relative min-h-[90vh] flex items-center overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-40 -left-40 w-[700px] h-[700px] bg-primary/15 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-blue-600/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 w-full relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center gap-2 bg-primary/10 border border-primary/30 text-primary text-[11px] font-extrabold uppercase tracking-[0.2em] px-4 py-2 rounded-full mb-8">
                    <span class="w-1.5 h-1.5 bg-primary rounded-full animate-pulse"></span>
                    <?= htmlspecialchars($s['site_tagline'] ?? "Vietnam's #1 Sports Store") ?>
                </div>
                
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <h1 class="text-6xl md:text-7xl font-black text-white leading-[1] tracking-tighter mb-6">
                        <?= htmlspecialchars($s['about_title'] ?? 'Built for Champions') ?>
                    </h1>
                <?php else: ?>
                    <h1 class="text-5xl md:text-6xl font-black text-white leading-[1.1] tracking-tight mb-6">
                        Nâng tầm<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-orange-300">
                            Hiệu suất.<br>
                        </span>
                        Bứt phá giới hạn.
                    </h1>
                <?php endif; ?>

                <p class="text-gray-400 text-lg max-w-md mb-10 leading-relaxed">
                    <?= htmlspecialchars($s['about_description'] ?? 'Trang bị những sản phẩm thể thao cao cấp — được thiết kế để nâng tầm hiệu suất của bạn.') ?>
                </p>

                <div class="flex flex-wrap gap-4 mb-10">
                    <a href="<?= BASE_URL ?>/index.php?route=products" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white font-extrabold text-sm px-8 py-4 rounded-2xl transition-all shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-0.5">
                        <i class="fa-solid fa-bolt"></i> Khám phá ngay
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= BASE_URL ?>/index.php?route=profile" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-bold text-sm px-8 py-4 rounded-2xl border border-gray-700 hover:border-primary/50 transition-all">
                            <i class="fa-solid fa-user-gear"></i> Trang cá nhân
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/index.php?route=about" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-bold text-sm px-8 py-4 rounded-2xl border border-white/20 transition-all">
                            <i class="fa-solid fa-circle-info"></i> Giới thiệu
                        </a>
                        <a href="<?= BASE_URL ?>/index.php?route=register" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-bold text-sm px-8 py-4 rounded-2xl border border-gray-700 hover:border-primary/50 transition-all">
                            <i class="fa-solid fa-user-plus"></i> Đăng ký
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center gap-4 pt-8 border-t border-gray-800">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username'] ?? 'U') ?>&background=ff6600&color=fff" class="w-12 h-12 rounded-full border-2 border-primary">
                        <div>
                            <div class="text-white font-bold text-lg">Chào mừng trở lại, <span class="text-primary"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>! 👋</div>
                            <div class="text-gray-500 text-sm">Rất vui được đồng hành cùng bạn chinh phục thử thách mới.</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Hero Visual -->
            <div class="relative hidden lg:flex items-center justify-center">
                <div class="relative w-full aspect-square max-w-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-gray-900/40 to-transparent rounded-[40px] border border-primary/10 shadow-2xl"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fa-solid fa-bolt text-[120px] text-primary/10 mb-2 block animate-pulse"></i>
                            <span class="text-white font-black text-3xl tracking-[0.2em] italic"><?= htmlspecialchars($s['site_name'] ?? 'SPORTZONE') ?></span>
                            <div class="text-gray-600 text-xs tracking-[0.4em] uppercase mt-2 font-bold">High Performance Gear</div>
                        </div>
                    </div>
                    <!-- Stats badge for Guest -->
                    <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="absolute -top-6 -right-6 bg-gray-900 border border-gray-700 rounded-3xl p-5 shadow-2xl animate-bounce" style="animation-duration: 4s;">
                        <div class="text-xs text-gray-500 mb-1">Cộng đồng tin tưởng</div>
                        <div class="text-white font-black text-2xl">10,000+</div>
                        <div class="flex gap-0.5 text-yellow-500 text-[10px]">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== MARQUEE BRAND STRIP ===== -->
<div class="border-y border-gray-800 bg-gray-900/50 py-4 overflow-hidden">
    <div class="flex gap-16 items-center whitespace-nowrap" style="animation: marquee 20s linear infinite;">
        <?php for($i=0; $i<3; $i++): ?>
            <span class="text-gray-700 text-xs font-black uppercase tracking-[0.3em]">NIKE</span>
            <span class="text-primary/40">✦</span>
            <span class="text-gray-700 text-xs font-black uppercase tracking-[0.3em]">ADIDAS</span>
            <span class="text-primary/40">✦</span>
            <span class="text-gray-700 text-xs font-black uppercase tracking-[0.3em]">PUMA</span>
            <span class="text-primary/40">✦</span>
            <span class="text-gray-700 text-xs font-black uppercase tracking-[0.3em]">UNDER ARMOUR</span>
            <span class="text-primary/40">✦</span>
            <span class="text-gray-700 text-xs font-black uppercase tracking-[0.3em]">REEBOK</span>
            <span class="text-primary/40">✦</span>
            <span class="text-gray-700 text-xs font-black uppercase tracking-[0.3em]">NEW BALANCE</span>
            <span class="text-primary/40">✦</span>
        <?php endfor; ?>
    </div>
</div>

<!-- ===== WHY CHOOSE US (Landing Section for Guest) ===== -->
<?php if (!isset($_SESSION['user_id'])): ?>
<section class="py-24 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-1">
                <div class="text-xs text-primary font-extrabold uppercase tracking-[0.2em] mb-4 italic">Tại sao là chúng tôi?</div>
                <h2 class="text-3xl font-black text-white leading-tight">Cam kết<br>vàng từ<br><span class="text-primary">SportZone</span></h2>
            </div>
            <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-gray-900 border border-gray-800 p-8 rounded-[2rem] hover:border-primary/30 transition-all group">
                    <i class="fa-solid fa-shield-halved text-3xl text-primary mb-5 group-hover:scale-110 transition-transform"></i>
                    <h3 class="text-white font-bold mb-3">100% Chính hãng</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Bảo hành chính hãng, phát hiện hàng giả hoàn tiền 200% ngay lập tức.</p>
                </div>
                <div class="bg-gray-900 border border-gray-800 p-8 rounded-[2rem] hover:border-primary/30 transition-all group">
                    <i class="fa-solid fa-truck-fast text-3xl text-blue-400 mb-5 group-hover:scale-110 transition-transform"></i>
                    <h3 class="text-white font-bold mb-3">Giao hàng 24h</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Nhận hàng trong ngày tại nội thành TP.HCM và Hà Nội. Miễn phí ship từ 500k.</p>
                </div>
                <div class="bg-gray-900 border border-gray-800 p-8 rounded-[2rem] hover:border-primary/30 transition-all group">
                    <i class="fa-solid fa-arrows-rotate text-3xl text-green-400 mb-5 group-hover:scale-110 transition-transform"></i>
                    <h3 class="text-white font-bold mb-3">Đổi trả 30 ngày</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Không vừa size? Không ưng ý? Đổi trả thoải mái, thủ tục nhanh gọn trong 30 ngày.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== TOP CATEGORIES ===== -->
<?php if (!empty($topCategories)): ?>
<section class="py-20 px-4 bg-[#0b0f19]">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-end justify-between mb-12">
            <div>
                <div class="text-xs text-primary font-extrabold uppercase tracking-[0.2em] mb-2">Phân loại</div>
                <h2 class="text-3xl font-black text-white">Danh mục hàng đầu</h2>
            </div>
            <a href="<?= BASE_URL ?>/index.php?route=products" class="text-sm font-bold text-gray-500 hover:text-primary transition-colors flex items-center gap-1">
                Xem toàn bộ <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach ($topCategories as $cat): 
                $slug = $cat['slug'] ?? '';
                $icon = $catIcons[$slug][0] ?? 'fa-tag';
            ?>
            <a href="<?= BASE_URL ?>/index.php?route=products&category_id=<?= $cat['id'] ?>" class="group bg-gray-900 border border-gray-800 p-8 rounded-[2.5rem] flex flex-col items-center text-center hover:bg-primary/5 hover:border-primary/40 transition-all relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-24 h-24 bg-primary/5 rounded-full group-hover:scale-150 transition-transform"></div>
                <div class="w-16 h-16 bg-gray-800 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-primary group-hover:text-white transition-all">
                    <i class="fa-solid <?= $icon ?> text-2xl"></i>
                </div>
                <h3 class="text-white font-black group-hover:text-primary transition-colors"><?= htmlspecialchars($cat['name']) ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== FEATURED PRODUCTS ===== -->
<?php if (!empty($latestProducts)): ?>
<section class="py-24 px-4 relative overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-end justify-between mb-12">
            <div>
                <div class="text-xs text-primary font-extrabold uppercase tracking-[0.2em] mb-2">Trendings</div>
                <h2 class="text-3xl font-black text-white">Sản phẩm nổi bật</h2>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($latestProducts as $p): ?>
            <a href="<?= BASE_URL ?>/index.php?route=product_detail&slug=<?= $p['slug'] ?>" class="group flex flex-col h-full bg-gray-900/40 border border-gray-800 rounded-[2rem] overflow-hidden hover:border-primary/50 transition-all">
                <div class="aspect-[4/5] relative overflow-hidden bg-gray-800">
                    <?php if (!empty($p['primary_image'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($p['primary_image']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-700"><i class="fa-solid fa-image text-4xl"></i></div>
                    <?php endif; ?>
                    <?php if (!empty($p['sale_price'])): ?>
                        <div class="absolute top-4 left-4 bg-primary text-white text-[10px] font-black px-3 py-1 rounded-full shadow-lg">SALE</div>
                    <?php endif; ?>
                </div>
                <div class="p-6 flex-1 flex flex-col">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-2"><?= htmlspecialchars($p['category_name'] ?? 'Sport Gear') ?></div>
                    <h3 class="text-white font-bold group-hover:text-primary transition-colors line-clamp-1 mb-4"><?= htmlspecialchars($p['name']) ?></h3>
                    <div class="mt-auto flex items-center justify-between">
                        <div>
                            <?php if (!empty($p['sale_price'])): ?>
                                <div class="text-primary font-black text-lg"><?= number_format($p['sale_price'], 0, ',', '.') ?>đ</div>
                                <div class="text-gray-600 text-xs line-through"><?= number_format($p['price'], 0, ',', '.') ?>đ</div>
                            <?php else: ?>
                                <div class="text-white font-black text-lg"><?= number_format($p['price'], 0, ',', '.') ?>đ</div>
                            <?php endif; ?>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-gray-500 group-hover:bg-primary group-hover:text-white transition-all">
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

<!-- ===== HOW IT WORKS (Guest Only) ===== -->
<?php if (!isset($_SESSION['user_id'])): ?>
<section class="py-24 px-4 bg-gray-900/20 border-y border-gray-800">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-black text-white mb-16">Bắt đầu chinh phục chỉ sau 3 bước</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
            <?php foreach ([
                ['01', 'fa-user-plus', 'Tạo tài khoản', 'Đăng ký nhanh chóng, bảo mật tuyệt đối.'],
                ['02', 'fa-bag-shopping', 'Chọn trang bị', 'Duyệt hơn 500+ sản phẩm chính hãng.'],
                ['03', 'fa-truck-fast', 'Nhận hàng nhanh', 'Giao hàng tận nơi, kiểm tra mới thanh toán.']
            ] as $step): ?>
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 bg-gray-900 border border-gray-700 rounded-3xl flex items-center justify-center mb-6 relative shadow-xl">
                    <i class="fa-solid <?= $step[1] ?> text-2xl text-primary"></i>
                    <span class="absolute -top-3 -right-3 w-8 h-8 bg-primary text-white font-black rounded-full flex items-center justify-center text-xs shadow-lg"><?= $step[0] ?></span>
                </div>
                <h3 class="text-white font-bold text-lg mb-2"><?= $step[2] ?></h3>
                <p class="text-gray-500 text-sm"><?= $step[3] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== TESTIMONIALS (Guest Only) ===== -->
<?php if (!isset($_SESSION['user_id'])): ?>
<section class="py-24 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-black text-white">Được tin tưởng bởi hàng nghìn vận động viên</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ([
                ['Nguyễn Minh', 'VĐV Bóng đá', 'Sản phẩm tại SportZone rất bền, dịch vụ hỗ trợ tận tình. Giao hàng cực nhanh.'],
                ['Thu Hà', 'Yoga Instructor', 'Thảm yoga chất lượng tốt, không mùi, bám sàn tốt. Rất hài lòng!'],
                ['Lê Khoa', 'Runner', 'Giày chạy bộ mua ở đây chính hãng, êm chân, giúp mình phá kỷ lục cá nhân.']
            ] as $t): ?>
            <div class="bg-gray-900 border border-gray-800 p-8 rounded-[2rem] hover:border-primary/40 transition-colors">
                <div class="flex gap-1 text-yellow-500 mb-4 text-xs">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <p class="text-gray-400 italic mb-6 text-sm">"<?= $t[2] ?>"</p>
                <div class="flex items-center gap-3 pt-4 border-t border-gray-800">
                    <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center font-bold text-primary uppercase text-sm"><?= substr($t[0], 0, 1) ?></div>
                    <div>
                        <div class="text-white font-bold text-sm"><?= $t[0] ?></div>
                        <div class="text-gray-600 text-xs"><?= $t[1] ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== LATEST NEWS ===== -->
<?php if (!empty($latestNews)): ?>
<section class="py-20 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-end justify-between mb-12">
            <div>
                <div class="text-xs text-primary font-extrabold uppercase tracking-[0.2em] mb-2">Blog</div>
                <h2 class="text-3xl font-black text-white">Tin tức mới nhất</h2>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($latestNews as $news): ?>
            <a href="<?= BASE_URL ?>/index.php?route=news_detail&slug=<?= $news['slug'] ?>" class="group bg-gray-900/30 border border-gray-800 rounded-[2rem] overflow-hidden hover:border-primary/40 transition-all">
                <div class="aspect-video bg-gray-800 overflow-hidden">
                    <?php if (!empty($news['thumbnail'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($news['thumbnail']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-700"><i class="fa-solid fa-newspaper text-4xl"></i></div>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <h3 class="text-white font-bold group-hover:text-primary transition-colors line-clamp-2"><?= htmlspecialchars($news['title']) ?></h3>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== FINAL CTA ===== -->
<section class="py-24 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-gradient-to-br from-primary/20 via-primary/5 to-transparent border border-primary/20 rounded-[3rem] p-16 text-center shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-64 bg-primary/20 rounded-full blur-[80px]"></div>
            </div>
            <div class="relative z-10">
                <h2 class="text-4xl font-black text-white mb-6">Sẵn sàng bứt phá giới hạn?</h2>
                <p class="text-gray-400 mb-10 text-lg">Tham gia cùng cộng đồng SportZone ngay hôm nay.</p>
                <div class="flex justify-center">
                    <a href="<?= BASE_URL ?>/index.php?route=register" class="bg-primary hover:bg-primary-dark text-white font-extrabold px-12 py-4 rounded-2xl transition-all shadow-xl shadow-primary/40 hover:-translate-y-1">
                        Bắt đầu ngay miễn phí
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>