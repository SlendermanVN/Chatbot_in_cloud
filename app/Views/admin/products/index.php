<?php
function productBadge($isActive) {
    if ($isActive) {
        return '<span class="sz-badge sz-badge-success"><i class="fa-solid fa-circle-check"></i> Hoạt động</span>';
    }
    return '<span class="sz-badge sz-badge-secondary"><i class="fa-solid fa-eye-slash"></i> Đã ẩn</span>';
}
?>

<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-box-open"></i> Quản lý sản phẩm</h2>
    </div>
    <a href="<?= BASE_URL ?>/index.php?route=admin_product_create" class="sz-btn sz-btn-primary">
        <i class="fa-solid fa-plus"></i> Thêm Sản Phẩm
    </a>
</div>

<!-- Search + Filter Bar -->
<div class="sz-filter-bar">
    <form action="<?= BASE_URL ?>/index.php" method="GET" class="sz-search-form">
        <input type="hidden" name="route" value="admin_products">
        <div class="sz-input-icon">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>"
                   placeholder="Tìm theo tên, SKU..." class="sz-input">
        </div>
        <button class="sz-btn sz-btn-primary" type="submit">Tìm kiếm</button>
        <?php if (!empty($keyword)): ?>
            <a href="<?= BASE_URL ?>/index.php?route=admin_products" class="sz-btn sz-btn-ghost">
                <i class="fa-solid fa-xmark"></i> Xóa
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- Products Table -->
<div class="sz-card">
    <div class="sz-table-wrap">
        <table class="sz-table">
            <thead>
                <tr>
                    <th style="width:60px">ID</th>
                    <th>Sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá bán</th>
                    <th style="width:80px">Kho</th>
                    <th style="width:120px">Trạng thái</th>
                    <th style="width:100px" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td class="sz-id-cell">#<?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <div class="sz-product-cell">
                                    <?php if (!empty($p['primary_image'])): ?>
                                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($p['primary_image']) ?>"
                                             alt="<?= htmlspecialchars($p['name']) ?>" class="sz-product-thumb">
                                    <?php else: ?>
                                        <div class="sz-product-thumb sz-no-img">
                                            <i class="fa-solid fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="sz-product-info">
                                        <span class="sz-product-name" title="<?= htmlspecialchars($p['name']) ?>">
                                            <?= htmlspecialchars($p['name']) ?>
                                        </span>
                                        <span class="sz-product-sku">SKU: <?= htmlspecialchars($p['sku'] ?: '—') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="sz-category-tag"><?= htmlspecialchars($p['category_name'] ?? 'Chưa có') ?></span>
                            </td>
                            <td>
                                <?php if ($p['sale_price'] > 0): ?>
                                    <div class="sz-price-sale"><?= number_format($p['sale_price'], 0, ',', '.') ?>đ</div>
                                    <div class="sz-price-original"><?= number_format($p['price'], 0, ',', '.') ?>đ</div>
                                <?php else: ?>
                                    <div class="sz-price"><?= number_format($p['price'], 0, ',', '.') ?>đ</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['stock'] > 10): ?>
                                    <span class="sz-stock-ok"><?= $p['stock'] ?></span>
                                <?php elseif ($p['stock'] > 0): ?>
                                    <span class="sz-stock-low"><?= $p['stock'] ?></span>
                                <?php else: ?>
                                    <span class="sz-stock-out">Hết</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= productBadge($p['is_active']) ?>
                                <?php if ($p['is_featured']): ?>
                                    <span class="sz-badge sz-badge-warning mt-1"><i class="fa-solid fa-star"></i> Nổi bật</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="sz-action-group">
                                    <a href="<?= BASE_URL ?>/index.php?route=admin_product_edit&id=<?= $p['id'] ?>"
                                       class="sz-action-btn sz-action-edit" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <?php if ($p['is_active']): ?>
                                        <form action="<?= BASE_URL ?>/index.php?route=admin_product_delete&id=<?= $p['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Ẩn sản phẩm này?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <button type="submit" class="sz-action-btn sz-action-delete" title="Ẩn sản phẩm">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="sz-empty-state">
                                <i class="fa-solid fa-box-open"></i>
                                <h5>Không tìm thấy sản phẩm nào</h5>
                                <p><?= !empty($keyword) ? 'Thử tìm với từ khóa khác.' : 'Bắt đầu thêm sản phẩm đầu tiên!' ?></p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="sz-pagination">
            <span class="sz-pagination-info">Trang <?= $page ?> / <?= $totalPages ?></span>
            <div class="sz-pagination-links">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= BASE_URL ?>/index.php?route=admin_products&page=<?= $i ?>&keyword=<?= urlencode($keyword ?? '') ?>"
                       class="sz-page-link <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
