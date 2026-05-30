<?php
$orders = $orders ?? [];

// Flash
$flash = $flashType = null;
if (!empty($_SESSION['flash'])) {
    $flash    = $_SESSION['flash']['message'] ?? '';
    $flashType= $_SESSION['flash']['type']    ?? 'success';
    unset($_SESSION['flash']);
}

$statusConfig = [
    'pending'    => ['label' => 'Chờ xử lý',   'color' => 'text-yellow-400', 'bg' => 'bg-yellow-400/10', 'border' => 'border-yellow-400/30', 'icon' => 'fa-clock'],
    'processing' => ['label' => 'Đang xử lý',  'color' => 'text-blue-400',   'bg' => 'bg-blue-400/10',   'border' => 'border-blue-400/30',   'icon' => 'fa-gear'],
    'shipped'    => ['label' => 'Đang giao',    'color' => 'text-purple-400', 'bg' => 'bg-purple-400/10', 'border' => 'border-purple-400/30', 'icon' => 'fa-truck'],
    'delivered'  => ['label' => 'Đã giao',      'color' => 'text-green-400',  'bg' => 'bg-green-400/10',  'border' => 'border-green-400/30',  'icon' => 'fa-circle-check'],
    'cancelled'  => ['label' => 'Đã hủy',       'color' => 'text-red-400',    'bg' => 'bg-red-400/10',    'border' => 'border-red-400/30',    'icon' => 'fa-circle-xmark'],
];
?>

<!-- Flash -->
<?php if ($flash): ?>
<div class="max-w-4xl mx-auto px-4 pt-6">
    <div class="flex items-center gap-3 px-5 py-4 rounded-xl <?= $flashType === 'success' ? 'bg-green-500/10 border border-green-600/30 text-green-400' : 'bg-red-500/10 border border-red-600/30 text-red-400' ?>">
        <i class="fa-solid fa-circle-<?= $flashType === 'success' ? 'check' : 'exclamation' ?>"></i>
        <?= htmlspecialchars($flash) ?>
    </div>
</div>
<?php endif; ?>

<!-- Hero -->
<section class="relative overflow-hidden py-16 px-4">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-28 left-1/4 w-[500px] h-[500px] bg-primary/8 rounded-full blur-[120px]"></div>
    </div>
    <div class="max-w-4xl mx-auto relative z-10">
        <div class="flex items-center justify-between mb-2">
            <div>
                <div class="text-xs text-primary font-extrabold uppercase tracking-[0.2em] mb-1">Tài khoản</div>
                <h1 class="text-3xl font-black text-white">Đơn hàng của tôi</h1>
            </div>
            <a href="<?= BASE_URL ?>/index.php?route=products"
               class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white font-bold text-sm px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-primary/30">
                <i class="fa-solid fa-bag-shopping"></i> Mua thêm
            </a>
        </div>
        <p class="text-gray-500 text-sm">
            Xin chào, <span class="text-white font-semibold"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>.
            Dưới đây là toàn bộ lịch sử đơn hàng của bạn.
        </p>
    </div>
</section>

<!-- Order Stats -->
<?php if (!empty($orders)):
    $counts = ['pending'=>0,'processing'=>0,'shipped'=>0,'delivered'=>0,'cancelled'=>0];
    foreach ($orders as $o) $counts[$o['status'] ?? 'pending'] = ($counts[$o['status'] ?? 'pending'] ?? 0) + 1;
?>
<div class="max-w-4xl mx-auto px-4 mb-8">
    <div class="grid grid-cols-5 gap-3">
        <?php foreach ($statusConfig as $key => $cfg): ?>
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-4 text-center">
            <i class="fa-solid <?= $cfg['icon'] ?> <?= $cfg['color'] ?> text-lg mb-2 block"></i>
            <div class="text-xl font-black text-white"><?= $counts[$key] ?? 0 ?></div>
            <div class="text-xs text-gray-600 mt-0.5"><?= $cfg['label'] ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Orders List -->
<div class="max-w-4xl mx-auto px-4 pb-20">
    <?php if (empty($orders)): ?>
    <div class="bg-gray-900 border border-gray-800 rounded-3xl p-16 text-center">
        <i class="fa-solid fa-box-open text-6xl text-gray-700 mb-5 block"></i>
        <h2 class="text-xl font-bold text-white mb-2">Chưa có đơn hàng nào</h2>
        <p class="text-gray-500 mb-7">Hãy để SportZone giúp bạn trang bị cho hành trình thể thao của mình!</p>
        <a href="<?= BASE_URL ?>/index.php?route=products"
           class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white font-bold px-8 py-3 rounded-2xl transition-all shadow-lg shadow-primary/30">
            <i class="fa-solid fa-bolt"></i> Khám phá sản phẩm
        </a>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($orders as $order):
            $st  = $statusConfig[$order['status']] ?? $statusConfig['pending'];
            $date = isset($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : '—';
        ?>
        <div class="bg-gray-900 border border-gray-800 hover:border-gray-700 rounded-2xl overflow-hidden transition-colors group">
            <!-- Order Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800/60">
                <div class="flex items-center gap-4">
                    <div>
                        <span class="text-gray-500 text-xs">Đơn hàng</span>
                        <span class="text-white font-black ml-1">#<?= (int)$order['id'] ?></span>
                    </div>
                    <div class="h-4 w-px bg-gray-700"></div>
                    <span class="text-gray-500 text-xs flex items-center gap-1.5">
                        <i class="fa-regular fa-clock"></i> <?= $date ?>
                    </span>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold <?= $st['bg'] ?> <?= $st['color'] ?> border <?= $st['border'] ?>">
                    <i class="fa-solid <?= $st['icon'] ?>"></i>
                    <?= $st['label'] ?>
                </span>
            </div>

            <!-- Order Info -->
            <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 text-sm text-gray-400">
                        <i class="fa-solid fa-user text-gray-600 w-4 text-center"></i>
                        <span><?= htmlspecialchars($order['recipient_name'] ?? '—') ?></span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-400">
                        <i class="fa-solid fa-phone text-gray-600 w-4 text-center"></i>
                        <span><?= htmlspecialchars($order['recipient_phone'] ?? '—') ?></span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-400 max-w-xs">
                        <i class="fa-solid fa-location-dot text-gray-600 w-4 text-center flex-shrink-0"></i>
                        <span class="truncate"><?= htmlspecialchars($order['shipping_address'] ?? '—') ?></span>
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-xs text-gray-500 mb-1">Tổng tiền</div>
                    <div class="text-2xl font-black text-primary">
                        <?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?>đ
                    </div>
                    <a href="<?= BASE_URL ?>/index.php?route=order_detail&id=<?= (int)$order['id'] ?>"
                       class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-white transition-colors mt-2">
                        Xem chi tiết <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
