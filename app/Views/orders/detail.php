<?php
$order      = $order      ?? [];
$orderItems = $orderItems ?? [];

$statusConfig = [
    'pending'    => ['label' => 'Chờ xử lý',  'color' => 'text-yellow-400', 'bg' => 'bg-yellow-400/10', 'border' => 'border-yellow-400/30', 'icon' => 'fa-clock'],
    'processing' => ['label' => 'Đang xử lý', 'color' => 'text-blue-400',   'bg' => 'bg-blue-400/10',   'border' => 'border-blue-400/30',   'icon' => 'fa-gear fa-spin'],
    'shipped'    => ['label' => 'Đang giao',   'color' => 'text-purple-400', 'bg' => 'bg-purple-400/10', 'border' => 'border-purple-400/30', 'icon' => 'fa-truck'],
    'delivered'  => ['label' => 'Đã giao',     'color' => 'text-green-400',  'bg' => 'bg-green-400/10',  'border' => 'border-green-400/30',  'icon' => 'fa-circle-check'],
    'cancelled'  => ['label' => 'Đã hủy',      'color' => 'text-red-400',    'bg' => 'bg-red-400/10',    'border' => 'border-red-400/30',    'icon' => 'fa-circle-xmark'],
];

$st   = $statusConfig[$order['status'] ?? 'pending'] ?? $statusConfig['pending'];
$date = isset($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : '—';

// Progress steps
$steps = ['pending', 'processing', 'shipped', 'delivered'];
$currentStep = array_search($order['status'] ?? 'pending', $steps);
if ($order['status'] === 'cancelled') $currentStep = -1;

$stepLabels = ['Đặt hàng', 'Đang xử lý', 'Đang giao', 'Đã giao'];
$stepIcons  = ['fa-file-circle-check', 'fa-gear', 'fa-truck', 'fa-house'];
?>

<!-- Breadcrumb -->
<div class="max-w-4xl mx-auto px-4 pt-8 pb-2">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="<?= BASE_URL ?>/index.php" class="hover:text-white transition-colors">Trang chủ</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="<?= BASE_URL ?>/index.php?route=orders" class="hover:text-white transition-colors">Đơn hàng của tôi</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-white font-semibold">#<?= (int)($order['id'] ?? 0) ?></span>
    </nav>
</div>

<div class="max-w-4xl mx-auto px-4 pb-20">

    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8 mt-4">
        <div>
            <h1 class="text-2xl font-black text-white">Chi tiết đơn hàng <span class="text-primary">#<?= (int)($order['id'] ?? 0) ?></span></h1>
            <p class="text-gray-500 text-sm mt-1">
                <i class="fa-regular fa-clock mr-1"></i><?= $date ?>
            </p>
        </div>
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold <?= $st['bg'] ?> <?= $st['color'] ?> border <?= $st['border'] ?>">
            <i class="fa-solid <?= $st['icon'] ?>"></i> <?= $st['label'] ?>
        </span>
    </div>

    <!-- Progress Bar (ẩn khi bị hủy) -->
    <?php if ($order['status'] !== 'cancelled'): ?>
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between relative">
            <!-- Line -->
            <div class="absolute top-5 left-[10%] right-[10%] h-0.5 bg-gray-700 z-0"></div>
            <div class="absolute top-5 left-[10%] h-0.5 bg-primary z-0 transition-all duration-700"
                 style="width: <?= $currentStep < 0 ? '0%' : min(100, ($currentStep / 3) * 80) ?>%"></div>

            <?php foreach ($steps as $i => $step): ?>
            <div class="relative z-10 flex flex-col items-center gap-2 flex-1">
                <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all
                    <?= $i <= $currentStep
                        ? 'bg-primary border-primary text-white shadow-lg shadow-primary/30'
                        : 'bg-gray-900 border-gray-700 text-gray-600' ?>">
                    <i class="fa-solid <?= $stepIcons[$i] ?> text-sm"></i>
                </div>
                <div class="text-xs font-medium text-center <?= $i <= $currentStep ? 'text-white' : 'text-gray-600' ?>">
                    <?= $stepLabels[$i] ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-red-500/10 border border-red-600/30 rounded-2xl p-4 mb-6 flex items-center gap-3 text-red-400">
        <i class="fa-solid fa-circle-xmark text-xl"></i>
        <div>
            <div class="font-bold">Đơn hàng đã bị hủy</div>
            <div class="text-xs text-red-400/70 mt-0.5">Nếu bạn đã thanh toán, vui lòng liên hệ hỗ trợ để được hoàn tiền.</div>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Products List -->
        <div class="md:col-span-2 space-y-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <i class="fa-solid fa-box text-primary text-base"></i>
                Sản phẩm (<?= count($orderItems) ?>)
            </h2>

            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
                <?php if (empty($orderItems)): ?>
                <div class="p-8 text-center text-gray-500">Không có sản phẩm trong đơn hàng.</div>
                <?php else: ?>
                <div class="divide-y divide-gray-800/60">
                    <?php foreach ($orderItems as $item): ?>
                    <div class="flex items-center gap-4 px-5 py-4">
                        <!-- Image -->
                        <a href="<?= BASE_URL ?>/index.php?route=product_detail&id=<?= (int)$item['product_id'] ?>"
                           class="w-16 h-16 flex-shrink-0 bg-[#0b0f19] rounded-xl overflow-hidden border border-gray-800 hover:border-primary transition-colors">
                            <?php if (!empty($item['product_image'])): ?>
                                <img src="<?= BASE_URL ?>/../<?= htmlspecialchars($item['product_image']) ?>"
                                     alt="<?= htmlspecialchars($item['product_name'] ?? $item['name'] ?? '') ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-700">
                                    <i class="fa-solid fa-image text-xl"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <a href="<?= BASE_URL ?>/index.php?route=product_detail&id=<?= (int)$item['product_id'] ?>"
                               class="font-semibold text-white text-sm truncate hover:text-primary transition-colors block">
                                <?= htmlspecialchars($item['product_name'] ?? $item['name'] ?? 'Sản phẩm') ?>
                            </a>
                            <div class="text-gray-500 text-xs mt-0.5">
                                Đơn giá: <?= number_format($item['price_at_order'] ?? $item['price'] ?? 0, 0, ',', '.') ?>đ
                            </div>
                        </div>
                        <!-- Qty + Total -->
                        <div class="text-right flex-shrink-0">
                            <div class="text-xs text-gray-500">x<?= (int)($item['quantity'] ?? 1) ?></div>
                            <div class="text-white font-bold text-sm">
                                <?= number_format(($item['price_at_order'] ?? $item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') ?>đ
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Total row -->
                <div class="px-5 py-4 border-t border-gray-800 bg-black/20 flex justify-between items-center">
                    <span class="text-gray-400 font-medium">Tổng cộng</span>
                    <span class="text-xl font-black text-primary">
                        <?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?>đ
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Order Info Sidebar -->
        <div class="space-y-4">
            <!-- Shipping Info -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5">
                <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-primary text-sm"></i> Thông tin giao hàng
                </h3>
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-600 uppercase tracking-wider mb-0.5">Người nhận</div>
                        <div class="text-white text-sm font-medium"><?= htmlspecialchars($order['recipient_name'] ?? '—') ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 uppercase tracking-wider mb-0.5">Số điện thoại</div>
                        <div class="text-white text-sm font-medium"><?= htmlspecialchars($order['recipient_phone'] ?? '—') ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 uppercase tracking-wider mb-0.5">Địa chỉ</div>
                        <div class="text-white text-sm"><?= htmlspecialchars($order['shipping_address'] ?? '—') ?></div>
                    </div>
                    <?php if (!empty($order['note'])): ?>
                    <div>
                        <div class="text-xs text-gray-600 uppercase tracking-wider mb-0.5">Ghi chú</div>
                        <div class="text-gray-400 text-sm italic"><?= htmlspecialchars($order['note']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-2">
                <a href="<?= BASE_URL ?>/index.php?route=orders"
                   class="w-full flex items-center justify-center gap-2 bg-gray-800 hover:bg-gray-700 text-white font-medium text-sm py-3 px-4 rounded-xl border border-gray-700 transition-colors">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Quay lại danh sách
                </a>
                <a href="<?= BASE_URL ?>/index.php?route=products"
                   class="w-full flex items-center justify-center gap-2 bg-primary/10 hover:bg-primary/20 text-primary font-medium text-sm py-3 px-4 rounded-xl border border-primary/30 transition-colors">
                    <i class="fa-solid fa-bag-shopping text-xs"></i> Tiếp tục mua sắm
                </a>
                <?php if ($order['status'] === 'delivered'): ?>
                <a href="<?= BASE_URL ?>/index.php?route=products"
                   class="w-full flex items-center justify-center gap-2 bg-yellow-400/10 hover:bg-yellow-400/20 text-yellow-400 font-medium text-sm py-3 px-4 rounded-xl border border-yellow-400/30 transition-colors">
                    <i class="fa-solid fa-star text-xs"></i> Đánh giá sản phẩm
                </a>
                <?php endif; ?>
                <?php if (in_array($order['status'], ['pending'])): ?>
                <div class="text-center pt-1">
                    <span class="text-xs text-gray-600">Muốn hủy đơn? <a href="<?= BASE_URL ?>/index.php?route=contact" class="text-primary hover:underline">Liên hệ hỗ trợ</a></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
