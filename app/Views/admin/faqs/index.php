<?php
$search = $search ?? '';
$faqs   = $faqs   ?? [];
$active = count(array_filter($faqs, fn($f) => ($f['is_active'] ?? 1) == 1));
$cats   = count(array_unique(array_filter(array_column($faqs, 'category'))));

$flash = $flashType = null;
if (!empty($_SESSION['flash'])) {
    $flash     = $_SESSION['flash']['message'] ?? '';
    $flashType = $_SESSION['flash']['type']    ?? 'success';
    unset($_SESSION['flash']);
}
?>

<?php if ($flash): ?>
    <div class="sz-alert sz-alert-<?= $flashType === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid fa-<?= $flashType === 'success' ? 'circle-check' : 'circle-exclamation' ?>"></i>
        <?= htmlspecialchars($flash) ?>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-circle-question"></i> Quản lý FAQ</h2>
    </div>
    <a href="<?= BASE_URL ?>/index.php?route=admin_faq_create" class="sz-btn sz-btn-primary">
        <i class="fa-solid fa-plus"></i> Thêm FAQ mới
    </a>
</div>

<!-- Stats Row -->
<div class="sz-stats-row">
    <div class="sz-stat-card">
        <div class="sz-stat-icon" style="background:rgba(255,102,0,0.12);color:#ff6600">
            <i class="fa-solid fa-circle-question"></i>
        </div>
        <div class="sz-stat-body">
            <div class="sz-stat-number"><?= count($faqs) ?></div>
            <div class="sz-stat-label">Tổng câu hỏi</div>
        </div>
    </div>
    <div class="sz-stat-card">
        <div class="sz-stat-icon" style="background:rgba(16,185,129,0.12);color:#10b981">
            <i class="fa-solid fa-eye"></i>
        </div>
        <div class="sz-stat-body">
            <div class="sz-stat-number"><?= $active ?></div>
            <div class="sz-stat-label">Đang hiển thị</div>
        </div>
    </div>
    <div class="sz-stat-card">
        <div class="sz-stat-icon" style="background:rgba(6,182,212,0.12);color:#06b6d4">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <div class="sz-stat-body">
            <div class="sz-stat-number"><?= $cats ?></div>
            <div class="sz-stat-label">Danh mục</div>
        </div>
    </div>
</div>

<!-- Search + Table -->
<div class="sz-card">
    <div class="sz-card-toolbar">
        <form method="GET" action="<?= BASE_URL ?>/index.php" class="sz-search-form">
            <input type="hidden" name="route" value="admin_faqs">
            <div class="sz-input-icon">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                       placeholder="Tìm câu hỏi, danh mục..." class="sz-input">
            </div>
            <button type="submit" class="sz-btn sz-btn-primary">Tìm kiếm</button>
            <?php if ($search): ?>
                <a href="<?= BASE_URL ?>/index.php?route=admin_faqs" class="sz-btn sz-btn-ghost">
                    <i class="fa-solid fa-xmark"></i> Xóa
                </a>
            <?php endif; ?>
        </form>
        <a href="<?= BASE_URL ?>/index.php?route=faqs" target="_blank" class="sz-text-dim sz-text-sm">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem trang FAQ
        </a>
    </div>

    <div class="sz-table-wrap">
        <table class="sz-table">
            <thead>
                <tr>
                    <th style="width:60px" class="text-center">ID</th>
                    <th style="width:140px">Danh mục</th>
                    <th>Câu hỏi / Trả lời</th>
                    <th style="width:80px" class="text-center">Thứ tự</th>
                    <th style="width:90px" class="text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($faqs)): ?>
                    <?php foreach ($faqs as $faq): ?>
                        <tr>
                            <td class="sz-id-cell text-center">#<?= $faq['id'] ?></td>
                            <td>
                                <span class="sz-badge sz-badge-info"><?= htmlspecialchars($faq['category'] ?? '—') ?></span>
                            </td>
                            <td>
                                <div class="sz-fw-medium"><?= htmlspecialchars($faq['question']) ?></div>
                                <div class="sz-text-dim sz-text-xs sz-text-truncate-350">
                                    <?= htmlspecialchars($faq['answer']) ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="sz-order-badge"><?= (int)($faq['sort_order'] ?? 0) ?></span>
                            </td>
                            <td class="text-right">
                                <div class="sz-action-group justify-content-end">
                                    <a href="<?= BASE_URL ?>/index.php?route=admin_faq_edit&id=<?= (int)$faq['id'] ?>"
                                       class="sz-action-btn sz-action-edit" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?route=admin_faq_delete&id=<?= (int)$faq['id'] ?>"
                                       onclick="return confirm('Xóa câu hỏi này?')"
                                       class="sz-action-btn sz-action-delete" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="sz-empty-state">
                                <i class="fa-solid fa-circle-question"></i>
                                <h5><?= $search ? 'Không tìm thấy kết quả' : 'Chưa có FAQ nào' ?></h5>
                                <p><?= $search ? 'Thử tìm với từ khóa khác.' : 'Hãy thêm câu hỏi đầu tiên!' ?></p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
