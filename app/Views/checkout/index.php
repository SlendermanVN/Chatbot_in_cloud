<?php /* View: Thanh toán — URL: ?route=checkout */ ?>
<div class="checkout-page bg-[#0b0f19] min-h-screen text-gray-300 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb & Header -->
        <div class="mb-10">
            <nav class="flex text-sm text-gray-500 mb-4 font-medium" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="<?= BASE_URL ?>/index.php" class="hover:text-primary transition-colors"><i class="fa-solid fa-house mr-2"></i>Trang chủ</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
                            <a href="<?= BASE_URL ?>/index.php?route=cart" class="hover:text-primary transition-colors">Giỏ hàng</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
                            <span class="text-white">Thanh toán</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fa-solid fa-credit-card text-primary text-2xl"></i> Thanh toán đơn hàng
            </h1>
        </div>

        <!-- Flash message -->
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="mb-6 bg-<?= $_SESSION['flash']['type'] == 'error' ? 'red' : 'green' ?>-900/40 border border-<?= $_SESSION['flash']['type'] == 'error' ? 'red' : 'green' ?>-700 text-<?= $_SESSION['flash']['type'] == 'error' ? 'red' : 'green' ?>-200 px-5 py-4 rounded-xl flex items-start gap-3">
                <i class="fa-solid <?= $_SESSION['flash']['type'] == 'error' ? 'fa-triangle-exclamation text-red-500' : 'fa-check text-green-500' ?> mt-1"></i>
                <div><?= $_SESSION['flash']['message'] ?></div>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            
            <!-- ===== FORM THÔNG TIN GIAO HÀNG ===== -->
            <div class="lg:col-span-7">
                <div class="bg-gray-900 rounded-2xl shadow-lg border border-gray-800 p-6 md:p-8">
                    <h2 class="text-xl font-bold text-white mb-6 pb-4 border-b border-gray-800">
                        <span class="bg-primary/20 text-primary w-8 h-8 rounded-full inline-flex items-center justify-center mr-2 text-sm">1</span> 
                        Thông tin giao nhận
                    </h2>

                    <form method="POST" action="<?= BASE_URL ?>/index.php?route=checkout_process" id="checkoutForm" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="form-group">
                                <label for="recipient_name" class="block text-sm font-bold text-gray-300 mb-2">Tên người nhận <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fa-regular fa-user text-gray-500"></i>
                                    </div>
                                    <input type="text" id="recipient_name" name="recipient_name" value="<?= htmlspecialchars($userInfo['full_name'] ?? '') ?>" placeholder="Họ và Tên" required maxlength="100" class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">
                                </div>
                                <span class="text-red-500 text-sm mt-1 block h-4" id="err_name"></span>
                            </div>

                            <div class="form-group">
                                <label for="recipient_phone" class="block text-sm font-bold text-gray-300 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-phone text-gray-500"></i>
                                    </div>
                                    <input type="tel" id="recipient_phone" name="recipient_phone" value="<?= htmlspecialchars($userInfo['phone'] ?? '') ?>" placeholder="VD: 0901234567" required maxlength="11" pattern="[0-9]{9,11}" class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">
                                </div>
                                <span class="text-red-500 text-sm mt-1 block h-4" id="err_phone"></span>
                            </div>
                        </div>

                        <div class="form-group mb-6">
                            <label for="shipping_address" class="block text-sm font-bold text-gray-300 mb-2">Địa chỉ giao hàng <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute top-3.5 left-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-location-dot text-gray-500"></i>
                                </div>
                                <textarea id="shipping_address" name="shipping_address" rows="3" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố" required maxlength="500" class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"><?= htmlspecialchars($userInfo['address'] ?? '') ?></textarea>
                            </div>
                            <span class="text-red-500 text-sm mt-1 block h-4" id="err_address"></span>
                        </div>

                        <div class="form-group mb-8">
                            <label for="note" class="block text-sm font-bold text-gray-300 mb-2">Ghi chú (tùy chọn)</label>
                            <div class="relative">
                                <div class="absolute top-3.5 left-3 flex items-center pointer-events-none">
                                    <i class="fa-regular fa-comment-dots text-gray-500"></i>
                                </div>
                                <textarea id="note" name="note" rows="2" placeholder="VD: Gọi trước khi giao, giao hàng giờ hành chính..." maxlength="500" class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"></textarea>
                            </div>
                        </div>

                        <!-- ===== PHƯƠNG THỨC THANH TOÁN ===== -->
                        <h2 class="text-xl font-bold text-white mb-6 pb-4 border-b border-gray-800">
                            <span class="bg-primary/20 text-primary w-8 h-8 rounded-full inline-flex items-center justify-center mr-2 text-sm">2</span> 
                            Phương thức thanh toán
                        </h2>

                        <div class="space-y-4 mb-8">
                            <label class="flex items-center p-4 border border-primary bg-primary/5 rounded-xl cursor-pointer hover:bg-primary/10 transition-colors relative overlay-radio">
                                <input type="radio" name="payment_method" value="cod" checked class="w-5 h-5 text-primary bg-gray-900 border-gray-700 focus:ring-primary focus:ring-2 accent-primary mr-4">
                                <div>
                                    <div class="text-white font-medium flex items-center gap-2"><i class="fa-solid fa-money-bill-wave text-green-500"></i> Thanh toán khi nhận hàng (COD)</div>
                                    <div class="text-sm text-gray-500 mt-1">Trả tiền mặt trực tiếp khi Shipper giao hàng thành công.</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-4 border border-gray-700 bg-gray-800/40 rounded-xl cursor-pointer hover:bg-gray-800 transition-colors relative overlay-radio opacity-60">
                                <input type="radio" name="payment_method" value="bank" disabled title="Tạm thời bảo trì" class="w-5 h-5 text-primary bg-gray-900 border-gray-700 mr-4">
                                <div>
                                    <div class="text-white font-medium flex items-center gap-2"><i class="fa-solid fa-building-columns text-blue-400"></i> Chuyển khoản ngân hàng (Sắp ra mắt)</div>
                                </div>
                            </label>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ===== TÓM TẮT ĐƠN HÀNG ===== -->
            <div class="lg:col-span-5">
                <div class="bg-gray-900 rounded-2xl shadow-lg border border-gray-800 sticky top-24 overflow-hidden flex flex-col">
                    <div class="p-6 md:p-8 bg-gray-800/30 border-b border-gray-800">
                        <h2 class="text-xl font-bold text-white flex items-center justify-between">
                            Đơn hàng của bạn <span class="bg-primary/20 text-primary text-sm font-bold px-3 py-1 rounded-full"><?= count($items) ?> sản phẩm</span>
                        </h2>
                    </div>

                    <div class="max-h-96 overflow-y-auto p-6 md:p-8 space-y-6 custom-scrollbar">
                        <?php foreach ($items as $item): ?>
                            <div class="flex gap-4 items-center">
                                <div class="relative w-16 h-16 rounded-xl border border-gray-700 overflow-hidden shrink-0 bg-gray-800">
                                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($item['primary_image'] ?? 'images/no-image.png') ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="w-full h-full object-cover">
                                    <span class="absolute -top-2 -right-2 bg-gray-600 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center border-2 border-gray-900"><?= $item['quantity'] ?></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-200 truncate pr-2 hover:text-white"><?= htmlspecialchars($item['product_name']) ?></p>
                                    <p class="text-sm text-gray-500"><?= number_format($item['effective_price'], 0, ',', '.') ?> VNĐ</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="font-bold text-white"><?= number_format($item['effective_price'] * $item['quantity'], 0, ',', '.') ?> VNĐ</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="p-6 md:p-8 border-t border-gray-800 bg-gray-800/10">
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-gray-400">
                                <span>Tạm tính</span>
                                <span class="text-gray-300 font-medium"><?= number_format($total, 0, ',', '.') ?> VNĐ</span>
                            </div>
                            <div class="flex justify-between text-gray-400">
                                <span>Phí vận chuyển</span>
                                <?php if ($total >= 500000): ?>
                                    <span class="text-emerald-400 font-bold bg-emerald-400/10 border border-emerald-400/20 px-2 py-0.5 rounded text-sm uppercase">Miễn phí</span>
                                <?php else: ?>
                                    <span class="text-gray-300 font-medium">+ 30.000 VNĐ</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-end border-t border-gray-800 pt-6 mb-8 mt-2">
                            <p class="text-lg font-bold text-white">Tổng cộng</p>
                            <p class="text-3xl font-black text-primary drop-shadow-[0_0_10px_rgba(var(--primary-rgb),0.5)]">
                                <?= number_format($total >= 500000 ? $total : $total + 30000, 0, ',', '.') ?> VNĐ
                            </p>
                        </div>
                        
                        <button type="button" onclick="document.getElementById('checkoutForm').requestSubmit()" class="w-full justify-center bg-primary hover:bg-primary-dark hover:-translate-y-1 hover:shadow-glow text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 flex items-center gap-3 text-lg">
                            Đặt Hàng Ngay <i class="fa-solid fa-arrow-right"></i>
                        </button>
                        
                        <p class="text-center text-sm text-gray-500 mt-4 leading-relaxed">
                            Bằng cách đặt hàng, bạn đồng ý với <a href="<?= BASE_URL ?>/index.php?route=policy" class="text-primary hover:underline font-medium">Điều khoản & Chính sách</a> của SportZone.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.getElementById('checkoutForm').addEventListener('submit', function (e) {
        let valid = true;
        const name = document.getElementById('recipient_name').value.trim();
        const errName = document.getElementById('err_name');
        if (!name) { errName.textContent = 'Vui lòng nhập tên người nhận.'; valid = false; } else errName.textContent = '';

        const phone = document.getElementById('recipient_phone').value.trim();
        const errPhone = document.getElementById('err_phone');
        if (!/^[0-9]{9,11}$/.test(phone)) { errPhone.textContent = 'SĐT gồm 9-11 chữ số.'; valid = false; } else errPhone.textContent = '';

        const addr = document.getElementById('shipping_address').value.trim();
        const errAddr = document.getElementById('err_address');
        if (!addr) { errAddr.textContent = 'Vui lòng nhập địa chỉ giao hàng.'; valid = false; } else errAddr.textContent = '';

        if (!valid) e.preventDefault();
    });
</script>
<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
</style>