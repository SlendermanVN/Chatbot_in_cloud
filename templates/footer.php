</main> <footer class="bg-[#0b0f19] text-gray-400 py-16 border-t border-gray-800 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                
                <!-- Brand + Tagline + Social -->
                <div class="space-y-6">
                    <a href="/?route=home" class="text-2xl font-black italic tracking-widest text-primary">
                        <?= htmlspecialchars($footerSettings['site_name'] ?? 'SportZone Vietnam') ?>
                    </a>
                    <p class="text-sm text-gray-500 leading-relaxed mt-4">
                        <?= htmlspecialchars($footerSettings['site_tagline'] ?? 'Hệ thống cửa hàng phân phối dụng cụ thể thao chính hãng hàng đầu Việt Nam.') ?>
                    </p>
                    <div class="flex space-x-4 pt-2">
                        <a href="<?= htmlspecialchars($footerSettings['social_facebook'] ?? '#') ?>" 
                           target="_blank" rel="noopener"
                           class="w-10 h-10 rounded flex items-center justify-center bg-gray-800 text-gray-400 hover:bg-primary hover:text-white transition-all">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="<?= htmlspecialchars($footerSettings['social_instagram'] ?? '#') ?>"
                           target="_blank" rel="noopener"
                           class="w-10 h-10 rounded flex items-center justify-center bg-gray-800 text-gray-400 hover:bg-primary hover:text-white transition-all">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="<?= htmlspecialchars($footerSettings['social_youtube'] ?? '#') ?>"
                           target="_blank" rel="noopener"
                           class="w-10 h-10 rounded flex items-center justify-center bg-gray-800 text-gray-400 hover:bg-primary hover:text-white transition-all">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Về Chúng Tôi -->
                <div>
                    <h3 class="text-sm font-bold text-white mb-6 uppercase tracking-widest">Về Chúng Tôi</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="/?route=about" class="hover:text-primary transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-[10px] text-primary"></i> Giới thiệu công ty</a></li>
                        <li><a href="/?route=news" class="hover:text-primary transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-[10px] text-primary"></i> Tin tức thể thao</a></li>
                        <li><a href="/?route=faqs" class="hover:text-primary transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-[10px] text-primary"></i> Hỏi đáp thường gặp</a></li>
                        <li><a href="/?route=contact" class="hover:text-primary transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-[10px] text-primary"></i> Chăm sóc khách hàng</a></li>
                    </ul>
                </div>

                <!-- Thông Tin Liên Hệ -->
                <div>
                    <h3 class="text-sm font-bold text-white mb-6 uppercase tracking-widest">Liên Hệ</h3>
                    <ul class="space-y-3 text-sm">
                        <?php if (!empty($footerSettings['store_address'])): ?>
                        <li class="flex items-start gap-2 text-gray-500">
                            <i class="fa-solid fa-location-dot text-primary mt-0.5 flex-shrink-0"></i>
                            <?= htmlspecialchars($footerSettings['store_address']) ?>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($footerSettings['contact_phone'])): ?>
                        <li class="flex items-center gap-2 text-gray-500">
                            <i class="fa-solid fa-phone text-primary flex-shrink-0"></i>
                            <a href="tel:<?= htmlspecialchars($footerSettings['contact_phone']) ?>" class="hover:text-primary transition-colors">
                                <?= htmlspecialchars($footerSettings['contact_phone']) ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($footerSettings['contact_email'])): ?>
                        <li class="flex items-center gap-2 text-gray-500">
                            <i class="fa-solid fa-envelope text-primary flex-shrink-0"></i>
                            <a href="mailto:<?= htmlspecialchars($footerSettings['contact_email']) ?>" class="hover:text-primary transition-colors">
                                <?= htmlspecialchars($footerSettings['contact_email']) ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li><a href="/?route=faqs" class="hover:text-primary transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-[10px] text-primary"></i> Chính sách vận chuyển</a></li>
                        <li><a href="/?route=faqs" class="hover:text-primary transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-[10px] text-primary"></i> Đổi trả 30 ngày</a></li>
                    </ul>
                </div>

                <!-- Nhận Báo Giá -->
                <div>
                    <h3 class="text-sm font-bold text-white mb-6 uppercase tracking-widest">Nhận Báo Giá</h3>
                    <p class="text-xs text-gray-500 mb-4 leading-relaxed">Đăng ký để nhận thông báo về các đợt DROP sản phẩm giới hạn.</p>
                    <form class="flex group" onsubmit="event.preventDefault();">
                        <input type="email" placeholder="Email address..." class="px-4 py-3 bg-gray-900 text-white rounded-l w-full focus:outline-none focus:ring-1 focus:ring-primary border border-gray-700 transition-all text-sm">
                        <button class="bg-gray-800 border-y border-r border-gray-700 hover:bg-primary hover:border-primary transition-colors px-5 rounded-r text-white font-medium"><i class="fa-solid fa-paper-plane"></i></button>
                    </form>
                </div>
                
            </div>

            <div class="border-t border-gray-800 mt-16 pt-8 flex flex-col md:flex-row justify-between items-center text-xs tracking-wider text-gray-600 uppercase font-bold">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($footerSettings['site_name'] ?? 'SportZone Vietnam') ?>. High-Performance Digital Athletics.</p>
                <div class="mt-4 md:mt-0 flex items-center gap-6">
                    <a href="/?route=faqs" class="hover:text-primary transition-colors">Terms of Service</a>
                    <a href="/?route=faqs" class="hover:text-primary transition-colors">Privacy Protocol</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>