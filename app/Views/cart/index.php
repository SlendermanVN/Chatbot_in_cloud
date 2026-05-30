<?php /* View: Giỏ hàng — URL: ?route=cart */ ?>
<div class="cart-page bg-[#0b0f19] min-h-screen text-gray-300 py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <h1 class="text-3xl font-bold text-white mb-8 border-b border-gray-800 pb-4">Giỏ hàng của bạn</h1>

        <!-- Flash message -->
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="mb-6 bg-<?= $_SESSION['flash']['type'] == 'error' ? 'red' : 'green' ?>-900/50 border border-<?= $_SESSION['flash']['type'] == 'error' ? 'red' : 'green' ?>-700 text-<?= $_SESSION['flash']['type'] == 'error' ? 'red' : 'green' ?>-200 px-4 py-3 rounded-lg relative" role="alert">
                <?= $_SESSION['flash']['message'] ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?php if (empty($items)): ?>
            <!-- Giỏ hàng trống -->
            <div class="empty-state py-20 px-4 text-center bg-gray-900 rounded-2xl shadow-lg border border-gray-800 flex flex-col items-center justify-center">
                <div class="p-6 bg-gray-800/50 rounded-full mb-6 relative">
                    <i class="fa-solid fa-cart-arrow-down text-6xl text-gray-500"></i>
                    <span class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 rounded-full text-white text-xs flex items-center justify-center border-2 border-gray-900">0</span>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">Giỏ hàng đang trống</h3>
                <p class="text-gray-400 mb-8 max-w-md mx-auto">Có vẻ như bạn chưa thêm sản phẩm nào vào giỏ hàng. Hãy khám phá những sản phẩm thể thao chất lượng tại hệ thống của chúng tôi.</p>
                <a href="<?= BASE_URL ?>/index.php?route=products" class="inline-flex items-center justify-center bg-primary hover:bg-primary-dark hover:-translate-y-1 hover:shadow-glow text-white font-medium px-8 py-3 rounded-lg transition-all duration-300">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Khám phá sản phẩm
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Danh sách sản phẩm -->
                <div class="cart-items lg:col-span-2">
                    <div class="bg-gray-900 rounded-2xl shadow-lg border border-gray-800 overflow-hidden">
                        <!-- Desktop Table -->
                        <div class="overflow-x-auto w-full">
                            <table class="w-full text-left border-collapse whitespace-nowrap min-w-full">
                                <thead class="bg-gray-800 border-b border-gray-700">
                                    <tr>
                                        <th class="p-4 sm:p-5 font-semibold text-gray-300">Sản phẩm</th>
                                        <th class="p-4 sm:p-5 font-semibold text-gray-300 hidden sm:table-cell">Đơn giá</th>
                                        <th class="p-4 sm:p-5 font-semibold text-gray-300 text-center">Số lượng</th>
                                        <th class="p-4 sm:p-5 font-semibold text-gray-300 text-right">Tổng thành tiền</th>
                                        <th class="p-4 sm:p-5"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-800">
                                    <?php foreach ($items as $item): ?>
                                        <tr class="hover:bg-gray-800/50 transition-colors duration-200">
                                            <td class="p-4 sm:p-5 flex flex-col sm:flex-row items-center sm:items-start gap-4">
                                                <a href="<?= BASE_URL ?>/index.php?route=product_detail&id=<?= $item['product_id'] ?>" class="flex-shrink-0 group relative overflow-hidden rounded border border-gray-700 aspect-square w-20">
                                                    <?php $imgSrc = !empty($item['primary_image']) ? BASE_URL . '/' . htmlspecialchars($item['primary_image']) : BASE_URL . '/images/no-image.png'; ?>
                                                    <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($item['name'] ?? '') ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                                </a>
                                                <div class="flex flex-col mt-2 sm:mt-0">
                                                    <a href="<?= BASE_URL ?>/index.php?route=product_detail&id=<?= $item['product_id'] ?>" class="font-medium text-gray-200 hover:text-primary transition-colors text-center sm:text-left text-wrap line-clamp-2 leading-tight">
                                                        <?= htmlspecialchars($item['name'] ?? 'Sản phẩm') ?>
                                                    </a>
                                                    <span class="text-sm text-primary/80 font-medium sm:hidden text-center mt-1"><?= number_format($item['effective_price'], 0, ',', '.') ?> VNĐ</span>
                                                </div>
                                            </td>
                                            <td class="p-4 sm:p-5 text-gray-400 hidden sm:table-cell font-medium">
                                                <?= number_format($item['effective_price'], 0, ',', '.') ?> VNĐ
                                            </td>
                                            <td class="p-4 sm:p-5">
                                                <form method="POST" action="<?= BASE_URL ?>/index.php?route=cart_update" class="flex flex-col sm:flex-row items-center gap-2 justify-center">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                                    <div class="flex items-center bg-gray-800 border border-gray-700 font-medium rounded overflow-hidden w-24">
                                                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="w-full py-1.5 px-0 text-center bg-transparent text-gray-200 focus:ring-0 focus:outline-none border-none">
                                                    </div>
                                                    <button type="submit" class="text-sm text-gray-400 hover:text-white px-2 py-1 bg-gray-700/50 hover:bg-gray-700 rounded transition-colors" title="Cập nhật">
                                                        <i class="fa-solid fa-arrows-rotate"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="p-4 sm:p-5 font-bold text-primary text-right whitespace-nowrap">
                                                <?= number_format($item['effective_price'] * $item['quantity'], 0, ',', '.') ?> VNĐ
                                            </td>
                                            <td class="p-4 sm:p-5 text-right w-12">
                                                <form method="POST" action="<?= BASE_URL ?>/index.php?route=cart_remove">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                                    <button type="submit" class="text-gray-500 hover:text-red-500 w-8 h-8 rounded-full hover:bg-red-500/10 flex items-center justify-center transition-colors float-right" title="Xóa" onclick="return confirm('Xác nhận xóa sản phẩm khỏi giỏ hàng?');">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tóm tắt đơn hàng -->
                <div class="cart-summary relative">
                    <div class="bg-gray-900 p-6 rounded-2xl shadow-lg border border-gray-800 h-fit sticky top-24">
                        <h2 class="text-xl font-bold text-white mb-5 pb-5 border-b border-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-primary"></i> Tổng quát đơn hàng
                        </h2>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center text-gray-400">
                                <span>Tổng giá sản phẩm (<b class="text-gray-300"><?= count($items) ?></b> mặt hàng)</span>
                                <span class="font-medium text-gray-300"><?= number_format($total, 0, ',', '.') ?> VNĐ</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-400">
                                <span>Phí vận chuyển</span>
                                <span class="font-medium text-gray-300">Chưa tính</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mb-8 pt-5 border-t border-gray-800">
                            <span class="text-lg font-bold text-white">Tổng thanh toán:</span>
                            <span class="text-2xl font-black text-primary drop-shadow-[0_0_10px_rgba(var(--primary-rgb),0.5)]">
                                <?= number_format($total, 0, ',', '.') ?> VNĐ
                            </span>
                        </div>
                        
                        <a href="<?= BASE_URL ?>/index.php?route=checkout" class="flex items-center justify-center w-full bg-primary hover:bg-primary-dark hover:-translate-y-1 hover:shadow-glow text-white font-bold py-3.5 rounded-lg transition-all duration-300 gap-2 mb-4">
                            Tiến hành Đặt hàng <i class="fa-solid fa-arrow-right-long"></i>
                        </a>
                        
                        <a href="<?= BASE_URL ?>/index.php?route=products" class="flex items-center justify-center text-sm text-gray-500 hover:text-gray-300 font-medium py-2 rounded-lg border border-gray-800 hover:border-gray-600 hover:bg-gray-800 transition-all">
                            <i class="fa-solid fa-basket-shopping mr-2"></i> Mua thêm sản phẩm
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
