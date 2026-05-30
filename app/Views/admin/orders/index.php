<?php
function getAdminOrderBadge($status) {
    $map = [
        'pending'    => ['warning', 'Chờ xử lý',      'fa-clock'],
        'processing' => ['info',    'Đang chuẩn bị',   'fa-gear'],
        'shipped'    => ['blue',    'Đang giao',        'fa-truck'],
        'delivered'  => ['success', 'Đã giao',          'fa-circle-check'],
        'cancelled'  => ['danger',  'Đã hủy',           'fa-circle-xmark'],
    ];
    $s = $map[$status] ?? ['secondary', $status, 'fa-circle'];
    return "<span class=\"sz-badge sz-badge-{$s[0]}\"><i class=\"fa-solid {$s[2]}\"></i> {$s[1]}</span>";
}
?>

<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-receipt"></i> Quản lý đơn hàng</h2>
    </div>
</div>

<!-- Status Filter Tabs -->
<?php
$statusTabs = [
    ''           => ['Tất cả',        'fa-list'],
    'pending'    => ['Chờ xử lý',     'fa-clock'],
    'processing' => ['Đang chuẩn bị', 'fa-gear'],
    'shipped'    => ['Đang giao',     'fa-truck'],
    'delivered'  => ['Đã giao',       'fa-circle-check'],
    'cancelled'  => ['Đã hủy',        'fa-circle-xmark'],
];
$currentStatus = $_GET['status'] ?? '';
?>
<div class="sz-tab-bar">
    <?php foreach ($statusTabs as $val => [$lbl, $icon]): ?>
        <?php
        $count = $val === '' ? array_sum($stats ?? []) : ($stats[$val] ?? 0);
        $isActive = $currentStatus === $val;
        ?>
        <a href="<?= BASE_URL ?>/index.php?route=admin_orders<?= $val ? '&status='.$val : '' ?>"
           class="sz-tab <?= $isActive ? 'active' : '' ?>">
            <i class="fa-solid <?= $icon ?>"></i>
            <?= $lbl ?>
            <span class="sz-tab-count"><?= $count ?></span>
        </a>
    <?php endforeach; ?>
</div>

<!-- Search bar -->
<div class="sz-filter-bar mb-4">
    <form action="<?= BASE_URL ?>/index.php" method="GET" class="sz-search-form">
        <input type="hidden" name="route" value="admin_orders">
        <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
        <div class="sz-input-icon">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="keyword" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                   placeholder="Tìm theo Mã ĐH, tên người nhận..." class="sz-input">
        </div>
        <button type="submit" class="sz-btn sz-btn-primary">Tìm kiếm</button>
    </form>
</div>

<!-- Orders Table -->
<div class="sz-card">
    <div class="sz-table-wrap">
        <table class="sz-table">
            <thead>
                <tr>
                    <th style="width:80px">Mã ĐH</th>
                    <th>Khách hàng</th>
                    <th>Người nhận</th>
                    <th>Tổng tiền</th>
                    <th style="width:130px">Trạng thái</th>
                    <th>Ngày đặt</th>
                    <th style="width:180px" class="text-center">Cập nhật</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="sz-id-cell">#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <div class="sz-user-cell">
                                    <div class="sz-avatar-sm">
                                        <?= strtoupper(substr($order['username'] ?? 'K', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <span class="sz-user-name"><?= htmlspecialchars($order['username'] ?? 'Khách vãng lai') ?></span>
                                        <span class="sz-user-email"><?= htmlspecialchars($order['email'] ?? '') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="sz-fw-medium"><?= htmlspecialchars($order['recipient_name']) ?></div>
                                <div class="sz-text-dim"><?= htmlspecialchars($order['recipient_phone']) ?></div>
                            </td>
                            <td>
                                <span class="sz-amount"><?= number_format($order['total_amount'], 0, ',', '.') ?> VNĐ</span>
                            </td>
                            <td><?= getAdminOrderBadge($order['status']) ?></td>
                            <td>
                                <div class="sz-fw-medium"><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                                <div class="sz-text-dim"><?= date('H:i', strtotime($order['created_at'])) ?></div>
                            </td>
                            <td>
                                <?php if (!in_array($order['status'], ['delivered', 'cancelled'])): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/index.php?route=admin_order_status&id=<?= $order['id'] ?>" class="sz-inline-form">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                        <select name="status" class="sz-select-sm">
                                            <option value="pending"    <?= $order['status'] === 'pending'    ? 'selected' : '' ?>>Chờ xử lý</option>
                                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Đang chuẩn bị</option>
                                            <option value="shipped"    <?= $order['status'] === 'shipped'    ? 'selected' : '' ?>>Đang giao</option>
                                            <option value="delivered"  <?= $order['status'] === 'delivered'  ? 'selected' : '' ?>>Đã giao</option>
                                            <option value="cancelled"  <?= $order['status'] === 'cancelled'  ? 'selected' : '' ?>>Đã hủy</option>
                                        </select>
                                        <button type="submit" class="sz-btn-icon sz-btn-success"
                                                onclick="return confirm('Xác nhận cập nhật?');" title="Lưu">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="sz-text-dim sz-italic">Đã kết thúc</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="sz-empty-state">
                                <i class="fa-solid fa-inbox"></i>
                                <h5>Không có đơn hàng nào</h5>
                                <p>Trạng thái này hiện chưa có đơn hàng.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (!empty($totalPages) && $totalPages > 1): ?>
        <div class="sz-pagination">
            <span class="sz-pagination-info">Trang <?= $page ?? 1 ?> / <?= $totalPages ?></span>
            <div class="sz-pagination-links">
                <?php if (($page ?? 1) > 1): ?>
                    <a href="?route=admin_orders&page=<?= ($page ?? 1) - 1 ?><?= !empty($currentStatus) ? '&status='.$currentStatus : '' ?><?= !empty($_GET['keyword']) ? '&keyword='.urlencode($_GET['keyword']) : '' ?>" class="sz-page-link">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                <span class="sz-page-link active"><?= $page ?? 1 ?></span>
                <?php if (($page ?? 1) < $totalPages): ?>
                    <a href="?route=admin_orders&page=<?= ($page ?? 1) + 1 ?><?= !empty($currentStatus) ? '&status='.$currentStatus : '' ?><?= !empty($_GET['keyword']) ? '&keyword='.urlencode($_GET['keyword']) : '' ?>" class="sz-page-link">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
