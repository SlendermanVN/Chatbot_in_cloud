<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-comments"></i> Kiểm duyệt Bình luận/Đánh giá</h2>
    </div>
</div>

<!-- Stat Cards -->
<div class="sz-stats-row">
    <div class="sz-stat-card">
        <div class="sz-stat-icon" style="background:rgba(245,158,11,0.12);color:#f59e0b">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
        <div class="sz-stat-body">
            <div class="sz-stat-number"><?= $data['pendingCount'] ?? 0 ?></div>
            <div class="sz-stat-label">Chờ duyệt</div>
        </div>
    </div>
    <div class="sz-stat-card">
        <div class="sz-stat-icon" style="background:rgba(16,185,129,0.12);color:#10b981">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="sz-stat-body">
            <div class="sz-stat-number"><?= $data['approvedCount'] ?? 0 ?></div>
            <div class="sz-stat-label">Đã duyệt</div>
        </div>
    </div>
</div>

<!-- Comments Table -->
<div class="sz-card">
    <div class="sz-table-wrap">
        <table class="sz-table">
            <thead>
                <tr>
                    <th style="width:60px">ID</th>
                    <th style="width:200px">Người gửi</th>
                    <th>Nội dung / Bài viết</th>
                    <th style="width:110px" class="text-center">Trạng thái</th>
                    <th style="width:100px">Thời gian</th>
                    <th style="width:90px" class="text-center">Tác vụ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['comments'])): ?>
                    <?php foreach ($data['comments'] as $cmt): ?>
                        <tr>
                            <td class="sz-id-cell">#<?= $cmt['id'] ?></td>
                            <td>
                                <div class="sz-user-cell">
                                    <div class="sz-avatar-sm" style="background: linear-gradient(135deg,#ff6600,#ff8533)">
                                        <?= strtoupper(substr($cmt['full_name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <span class="sz-user-name"><?= htmlspecialchars($cmt['full_name'] ?? 'Khách') ?></span>
                                        <span class="sz-user-email"><?= htmlspecialchars($cmt['email'] ?? '') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($cmt['rating'])): ?>
                                    <div class="flex gap-0.5 text-yellow-500 text-xs mb-2">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <i class="fa-<?= $i <= $cmt['rating'] ? 'solid' : 'regular' ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="sz-comment-quote">
                                    <i class="fa-solid fa-quote-left sz-quote-icon"></i>
                                    <?= htmlspecialchars($cmt['content']) ?>
                                </div>
                                
                                <div class="sz-comment-source">
                                    <?php if (!empty($cmt['product_id'])): ?>
                                        <span class="sz-text-dim">Sản phẩm:</span>
                                        <a href="<?= BASE_URL ?>/index.php?route=product_detail&id=<?= $cmt['product_id'] ?>"
                                            target="_blank" class="sz-news-link">
                                            <?= htmlspecialchars($cmt['product_name'] ?? 'Sản phẩm') ?>
                                            <i class="fa-solid fa-arrow-up-right-from-square sz-link-icon"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="sz-text-dim">Bài viết:</span>
                                        <a href="<?= BASE_URL ?>/index.php?route=news_detail&id=<?= $cmt['article_id'] ?>"
                                            target="_blank" class="sz-news-link">
                                            <?= htmlspecialchars($cmt['article_title'] ?? 'Bài viết') ?>
                                            <i class="fa-solid fa-arrow-up-right-from-square sz-link-icon"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if ($cmt['is_approved'] === 1): ?>
                                    <span class="sz-badge sz-badge-success"><i class="fa-solid fa-check"></i> Đã duyệt</span>
                                <?php else: ?>
                                    <span class="sz-badge sz-badge-warning"><i class="fa-solid fa-hourglass-half"></i> Chờ
                                        duyệt</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="sz-fw-medium"><?= date('d/m/Y', strtotime($cmt['created_at'] ?? 'now')) ?></div>
                                <div class="sz-text-dim sz-text-xs"><?= date('H:i', strtotime($cmt['created_at'] ?? 'now')) ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="sz-action-group justify-content-center">
                                    <?php if ($cmt['is_approved'] !== 1): ?>
                                        <form action="<?= BASE_URL ?>/index.php?route=admin_comment_approve" method="POST"
                                            class="sz-inline-form">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                            <input type="hidden" name="id" value="<?= $cmt['id'] ?>">
                                            <button type="submit" class="sz-action-btn sz-action-approve" title="Duyệt bình luận">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form action="<?= BASE_URL ?>/index.php?route=admin_comment_delete" method="POST"
                                        class="sz-inline-form" onsubmit="return confirm('Xóa vĩnh viễn bình luận này?');">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                        <input type="hidden" name="id" value="<?= $cmt['id'] ?>">
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
                                <i class="fa-solid fa-check-double"></i>
                                <h5>Tất cả đã hoàn tất!</h5>
                                <p>Hiện tại không có bình luận nào cần xem xét.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="sz-pagination">
        <span class="sz-pagination-info">Trang <strong><?= $data['currentPage'] ?? 1 ?></strong> /
            <?= $data['totalPages'] ?? 1 ?></span>
        <div class="sz-pagination-links">
            <span class="sz-page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            <span class="sz-page-link active"><?= $data['currentPage'] ?? 1 ?></span>
            <span class="sz-page-link"><i class="fa-solid fa-chevron-right"></i></span>
        </div>
    </div>
</div>