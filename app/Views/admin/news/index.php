<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-newspaper"></i> Quản lý tin tức</h2>
    </div>
    <a href="?route=admin_news_create" class="sz-btn sz-btn-primary">
        <i class="fa-solid fa-plus"></i> Thêm bài viết
    </a>
</div>

<!-- Search bar -->
<div class="sz-filter-bar">
    <form action="" method="GET" class="sz-search-form">
        <input type="hidden" name="route" value="admin_news">
        <div class="sz-input-icon">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="keyword" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                   placeholder="Tìm bài viết..." class="sz-input">
        </div>
        <button type="submit" class="sz-btn sz-btn-primary">Tìm kiếm</button>
        <?php if (!empty($_GET['keyword'])): ?>
            <a href="?route=admin_news" class="sz-btn sz-btn-ghost"><i class="fa-solid fa-xmark"></i></a>
        <?php endif; ?>
    </form>
</div>

<!-- News Table -->
<div class="sz-card">
    <div class="sz-table-wrap">
        <table class="sz-table">
            <thead>
                <tr>
                    <th style="width:60px">ID</th>
                    <th style="width:90px">Ảnh</th>
                    <th>Tiêu đề / Slug</th>
                    <th style="width:110px" class="text-center">Trạng thái</th>
                    <th style="width:120px">Ngày tạo</th>
                    <th style="width:90px" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['news'])): ?>
                    <?php foreach ($data['news'] as $item): ?>
                        <tr>
                            <td class="sz-id-cell">#<?= $item['id'] ?></td>
                            <td>
                                <div class="sz-news-thumb">
                                    <?php if (!empty($item['thumbnail'])): ?>
                                        <img src="<?= BASE_URL . '/' . htmlspecialchars($item['thumbnail']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                                    <?php else: ?>
                                        <i class="fa-solid fa-image sz-thumb-placeholder"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="sz-fw-medium sz-text-truncate-200" title="<?= htmlspecialchars($item['title']) ?>">
                                    <?= htmlspecialchars($item['title']) ?>
                                </div>
                                <div class="sz-text-dim sz-text-xs sz-text-truncate-200">
                                    <i class="fa-solid fa-link"></i> <?= htmlspecialchars($item['slug']) ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if ($item['is_published'] == 1): ?>
                                    <span class="sz-badge sz-badge-success"><i class="fa-solid fa-circle-check"></i> Published</span>
                                <?php else: ?>
                                    <span class="sz-badge sz-badge-secondary"><i class="fa-solid fa-pen-ruler"></i> Draft</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="sz-fw-medium"><?= date('d/m/Y', strtotime($item['created_at'] ?? 'now')) ?></div>
                                <div class="sz-text-dim sz-text-xs"><?= date('H:i', strtotime($item['created_at'] ?? 'now')) ?></div>
                            </td>
                            <td>
                                <div class="sz-action-group">
                                    <a href="?route=admin_news_edit&id=<?= $item['id'] ?>"
                                       class="sz-action-btn sz-action-edit" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="?route=admin_news_delete" method="POST" class="sz-inline-form"
                                          onsubmit="return confirm('Xóa vĩnh viễn bài viết này?');">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="sz-action-btn sz-action-delete" title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <div class="sz-empty-state">
                                <i class="fa-solid fa-newspaper"></i>
                                <h5>Chưa có bài viết nào</h5>
                                <p>Hãy thêm bài viết đầu tiên để bắt đầu!</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="sz-pagination">
        <span class="sz-pagination-info">Trang <strong><?= $data['currentPage'] ?? 1 ?></strong> / <?= $data['totalPages'] ?? 1 ?></span>
        <div class="sz-pagination-links">
            <span class="sz-page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            <span class="sz-page-link active"><?= $data['currentPage'] ?? 1 ?></span>
            <span class="sz-page-link"><i class="fa-solid fa-chevron-right"></i></span>
        </div>
    </div>
</div>
