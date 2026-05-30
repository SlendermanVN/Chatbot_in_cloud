<?php /* View: Đặt hàng thành công — URL: ?route=order_success */ ?>
<div class="order-success-page bg-[#0b0f19] min-h-screen text-gray-300 py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-gray-900 rounded-3xl shadow-2xl border border-gray-800 p-8 md:p-12 text-center relative overflow-hidden">
            <!-- Glow effect -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-64 bg-emerald-500/10 blur-[80px] rounded-full pointer-events-none"></div>

            <div class="w-24 h-24 bg-emerald-500/10 border-2 border-emerald-500 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-8 relative z-10 shadow-[0_0_20px_rgba(16,185,129,0.3)]">
                <i class="fa-solid fa-check text-5xl"></i>
            </div>
            
            <h1 class="text-3xl md:text-4xl font-black text-white mb-4 relative z-10">Đặt Hàng Thành Công!</h1>
            <p class="text-gray-400 mb-8 text-lg max-w-lg mx-auto">
                Cảm ơn bạn đã tin tưởng mua sắm tại SportZone. Đơn hàng của bạn đã được ghi nhận và đang chờ xử lý.
            </p>

            <div class="bg-gray-800/50 border border-gray-700 rounded-2xl p-6 mb-10 text-left relative z-10 inline-block min-w-full sm:min-w-[400px]">
                <h3 class="font-bold text-gray-300 mb-4 border-b border-gray-700 pb-3 flex justify-between">
                    <span>Mã Đơn Hàng</span>
                    <span class="text-primary">#<?= $order['id'] ?></span>
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tên người nhận:</span>
                        <span class="text-gray-200 font-medium"><?= htmlspecialchars($order['recipient_name']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Số điện thoại:</span>
                        <span class="text-gray-200 font-medium"><?= htmlspecialchars($order['recipient_phone']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tổng thanh toán:</span>
                        <span class="text-primary font-bold text-base"><?= number_format($order['total_amount'], 0, ',', '.') ?> VNĐ</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Phương thức:</span>
                        <span class="text-gray-200 font-medium uppercase">COD (Thanh toán khi nhận hàng)</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 relative z-10">
                <a href="<?= BASE_URL ?>/index.php?route=order_detail&id=<?= $order['id'] ?>" class="w-full sm:w-auto bg-gray-800 hover:bg-gray-700 text-white font-medium py-3 px-8 rounded-xl transition-colors border border-gray-700">
                    Xem chi tiết đơn
                </a>
                <a href="<?= BASE_URL ?>/index.php?route=products" class="w-full sm:w-auto bg-primary hover:bg-primary-dark hover:-translate-y-1 hover:shadow-glow text-white font-bold py-3 px-8 rounded-xl transition-all duration-300">
                    Tiếp tục mua sắm
                </a>
            </div>
        </div>
        
    </div>
</div>
