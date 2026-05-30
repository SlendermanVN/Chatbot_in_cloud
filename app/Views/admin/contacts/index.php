<?php
// admin/contacts/index.php - Contact Management
$contacts = $contacts ?? [];
$totalPages = $totalPages ?? 1;
$page = $page ?? 1;
// Biến totalCount và unreadCount đã được Controller truyền sang, không cần tính lại bằng vòng lặp nữa
$totalCount = $totalCount ?? 0;
$unreadCount = $unreadCount ?? 0;
?>

<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-envelope-open-text"></i> Quản lý liên hệ</h2>
    </div>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <?php $flash = $_SESSION['flash'];
    unset($_SESSION['flash']); ?>
    <div class="sz-alert sz-alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid fa-circle-<?= $flash['type'] === 'success' ? 'check' : 'exclamation' ?>"></i>
        <?= htmlspecialchars($flash['message']) ?>
        <button class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="sz-stats-row mb-4">
    <div class="sz-stat-card">
        <div class="sz-stat-icon" style="background:rgba(255,102,0,0.12);color:#ff6600">
            <i class="fa-solid fa-inbox"></i>
        </div>
        <div class="sz-stat-body">
            <div class="sz-stat-number"><?= $totalCount ?></div>
            <div class="sz-stat-label">Tổng liên hệ</div>
        </div>
    </div>
    <div class="sz-stat-card">
        <div class="sz-stat-icon" style="background:rgba(239,68,68,0.12);color:#ef4444">
            <i class="fa-solid fa-envelope"></i>
        </div>
        <div class="sz-stat-body">
            <div class="sz-stat-number"><?= $unreadCount ?></div>
            <div class="sz-stat-label">Chưa đọc</div>
        </div>
    </div>
</div>

<div class="sz-tab-container mb-4">
    <div class="sz-tab-bar">
        <button class="sz-tab active" onclick="filterStatus('all')">Tất cả</button>
        <button class="sz-tab" onclick="filterStatus('unread')">Chưa đọc</button>
        <button class="sz-tab" onclick="filterStatus('read')">Đã đọc</button>
        <button class="sz-tab" onclick="filterStatus('replied')">Đã phản hồi</button>
    </div>
</div>

<div class="sz-card">
    <div class="sz-table-wrap">
        <table class="sz-table">
            <thead>
                <tr>
                    <th style="width:60px">ID</th>
                    <th style="width:220px">Khách hàng</th>
                    <th style="width:200px">Thông tin</th>
                    <th>Nội dung yêu cầu</th>
                    <th style="width:120px" class="text-center">Trạng thái</th>
                    <th style="width:100px">Thời gian</th>
                    <th style="width:90px" class="text-center">Tác vụ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contacts)): ?>
                    <?php foreach ($contacts as $c):
                        // SỬA LỖI: Lấy đúng cột 'status' từ Database
                        $status = $c['status'] ?? 'unread';
                        $statusBadge = '';
                        if ($status === 'unread' || $status === 'pending') {
                            $statusBadge = '<span class="sz-badge sz-badge-warning"><i class="fa-solid fa-envelope"></i> Chưa đọc</span>';
                        } elseif ($status === 'read') {
                            $statusBadge = '<span class="sz-badge sz-badge-info"><i class="fa-solid fa-eye"></i> Đã đọc</span>';
                        } elseif ($status === 'replied') {
                            $statusBadge = '<span class="sz-badge sz-badge-success"><i class="fa-solid fa-check"></i> Đã phản hồi</span>';
                        }
                        ?>
                        <tr data-status="<?= $status ?>"
                            class="<?= ($status === 'unread' || $status === 'pending') ? 'sz-row-unread' : '' ?>">
                            <td class="sz-id-cell">#<?= $c['id'] ?></td>
                            <td>
                                <div class="sz-user-cell">
                                    <div class="sz-avatar-sm" style="background: linear-gradient(135deg,#ff6600,#ff8533)">
                                        <?= strtoupper(substr($c['name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <span class="sz-user-name"><?= htmlspecialchars($c['name'] ?? 'Khách hàng') ?></span>
                                        <span class="sz-user-email text-truncate"
                                            style="max-width: 150px; display: inline-block;">
                                            <?= htmlspecialchars($c['subject'] ?? '') ?>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="sz-fw-medium">
                                    <i class="fa-solid fa-envelope mr-1 sz-text-dim sz-text-xs"></i>
                                    <?= htmlspecialchars($c['email'] ?? 'Không có') ?>
                                </div>
                                <div class="sz-text-dim sz-text-xs mt-1">
                                    <i class="fa-solid fa-phone mr-1"></i>
                                    <?= htmlspecialchars($c['phone'] ?? 'Không có') ?>
                                </div>
                            </td>
                            <td>
                                <div class="sz-comment-quote" style="max-width: 400px;">
                                    <i class="fa-solid fa-quote-left sz-quote-icon"></i>
                                    <?= htmlspecialchars($c['message'] ?? '') ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <?= $statusBadge ?>
                            </td>
                            <td>
                                <div class="sz-fw-medium"><?= date('d/m/Y', strtotime($c['created_at'])) ?></div>
                                <div class="sz-text-dim sz-text-xs"><?= date('H:i', strtotime($c['created_at'])) ?></div>
                            </td>
                            <td class="text-center">
                                <div class="sz-action-group justify-content-center">
                                    <button type="button" class="sz-action-btn sz-action-approve"
                                        onclick="viewContact(<?= htmlspecialchars(json_encode($c)) ?>)" title="Xem & Phản hồi">
                                        <i class="fa-solid fa-reply"></i>
                                    </button>

                                    <form action="<?= BASE_URL ?>/index.php?route=admin_contact_delete&id=<?= $c['id'] ?>"
                                        method="POST" class="d-inline" onsubmit="return confirm('Xóa vĩnh viễn liên hệ này?');">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                        <button type="submit" class="sz-action-btn sz-action-delete border-0 bg-transparent"
                                            title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="sz-empty-state">
                                <i class="fa-solid fa-envelope-open"></i>
                                <h5>Hộp thư trống!</h5>
                                <p>Hiện tại không có liên hệ nào từ khách hàng.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="sz-pagination">
            <span class="sz-pagination-info">Trang <strong><?= $page ?></strong> / <?= $totalPages ?></span>
            <div class="sz-pagination-links">
                <a href="<?= BASE_URL ?>/index.php?route=admin_contacts&page=<?= max(1, $page - 1) ?>"
                    class="sz-page-link <?= $page <= 1 ? 'disabled' : '' ?>"><i class="fa-solid fa-chevron-left"></i></a>
                <span class="sz-page-link active"><?= $page ?></span>
                <a href="<?= BASE_URL ?>/index.php?route=admin_contacts&page=<?= min($totalPages, $page + 1) ?>"
                    class="sz-page-link <?= $page >= $totalPages ? 'disabled' : '' ?>"><i
                        class="fa-solid fa-chevron-right"></i></a>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="contactModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg"
            style="border-radius: 20px; overflow: hidden; background-color: #111827; color: #f3f4f6;">
            <div class="modal-header border-0"
                style="padding: 1.5rem 2rem; border-bottom: 1px solid #374151 !important;">
                <h5 class="modal-title font-weight-bold text-white" style="font-size: 1.25rem;">
                    <i class="fa-solid fa-envelope-open-text mr-2" style="color: #ff6600;"></i> CHI TIẾT LIÊN HỆ
                </h5>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="d-flex align-items-start">
                            <div class="mr-3 d-flex align-items-center justify-content-center text-white font-weight-bold"
                                style="width: 50px; height: 50px; border-radius: 12px; font-size: 22px; background: linear-gradient(135deg, #ff6600, #ff8533); box-shadow: 0 4px 6px rgba(0,0,0,0.3);"
                                id="modal_avatar">U</div>
                            <div>
                                <label class="text-uppercase font-weight-bold mb-1"
                                    style="font-size: 11px; letter-spacing: 1px; color: #9ca3af;">Người gửi</label>
                                <h5 id="modal_name" class="font-weight-bold mb-1 text-white" style="font-size: 1.1rem;">
                                </h5>
                                <span id="modal_type" class="badge"
                                    style="font-size: 12px; font-weight: 500; padding: 5px 10px; background-color: #374151; color: #e5e7eb;"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3" style="background: #1f2937; border: 1px solid #374151; border-radius: 12px;">
                            <div class="mb-2 font-weight-bold text-white">
                                <i class="fa-solid fa-envelope mr-2" style="width: 16px; color: #9ca3af;"></i>
                                <span id="modal_email"></span>
                            </div>
                            <div class="mb-2 font-weight-bold text-white">
                                <i class="fa-solid fa-phone mr-2" style="width: 16px; color: #9ca3af;"></i>
                                <span id="modal_phone"></span>
                            </div>
                            <div class="font-weight-bold text-white">
                                <i class="fa-regular fa-clock mr-2" style="width: 16px; color: #9ca3af;"></i>
                                <span id="modal_date" class="small font-weight-bold" style="color: #d1d5db;"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-uppercase font-weight-bold mb-2"
                        style="font-size: 11px; letter-spacing: 1px; color: #9ca3af;">Nội dung yêu cầu</label>
                    <div class="p-4 position-relative"
                        style="background: rgba(255,102,0,0.08); border-left: 4px solid #ff6600; border-radius: 0 12px 12px 0;">
                        <i class="fa-solid fa-quote-left position-absolute"
                            style="top: 15px; right: 20px; font-size: 1.5rem; color: #ff6600; opacity: 0.15;"></i>
                        <div id="modal_message" class="font-weight-medium"
                            style="white-space: pre-wrap; font-size: 15px; line-height: 1.7; color: #f3f4f6;"></div>
                    </div>
                </div>
                <div id="reply_section" style="display: none; border-top: 1px dashed #4b5563; padding-top: 1.5rem;">
                    <div id="replied_content_wrap" style="display: none;">
                        <label class="text-uppercase font-weight-bold mb-2"
                            style="font-size: 11px; letter-spacing: 1px; color: #34d399;">Admin đã phản hồi</label>
                        <div class="p-3 d-flex align-items-start"
                            style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); border-radius: 12px;">
                            <i class="fa-solid fa-headset mr-3 mt-1" style="font-size: 1.2rem; color: #34d399;"></i>
                            <div id="replied_content"
                                style="font-size: 14.5px; font-weight: 500; white-space: pre-wrap; line-height: 1.6; color: #a7f3d0;">
                            </div>
                        </div>
                    </div>

                    <form id="replyForm" method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <label class="text-uppercase font-weight-bold mb-2"
                            style="font-size: 11px; letter-spacing: 1px; color: #60a5fa;">Phản hồi / Ghi chú mới</label>
                        <textarea name="replied_message" rows="4"
                            class="form-control mb-3 font-weight-medium shadow-none"
                            style="background-color: #1f2937; color: #f3f4f6; border-radius: 12px; resize: none; border: 1px solid #374151; font-size: 14.5px; padding: 12px;"
                            placeholder="Nhập nội dung phản hồi cho khách hàng..." required></textarea>

                        <div class="d-flex justify-content-between align-items-center p-3"
                            style="background: #1f2937; border: 1px solid #374151; border-radius: 12px;">
                            <button type="button" id="btnMarkRead" class="btn border font-weight-bold shadow-sm"
                                style="border-radius: 8px; background: #374151; color: #d1d5db; border-color: #4b5563;"
                                onclick="markAsRead()">
                                <i class="fa-solid fa-check-double mr-1" style="color: #60a5fa;"></i> Đánh dấu đã đọc
                            </button>
                            <button type="submit" class="btn text-white font-weight-bold px-4 border-0"
                                style="background: #ff6600; border-radius: 8px; box-shadow: 0 4px 6px rgba(255,102,0,0.3);">
                                <i class="fa-solid fa-paper-plane mr-2"></i> Lưu phản hồi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentContactId = null;

    function viewContact(c) {
        currentContactId = c.id;

        // Tạo chữ cái đầu tiên cho Avatar
        let firstChar = c.name ? c.name.charAt(0).toUpperCase() : 'U';
        document.getElementById('modal_avatar').textContent = firstChar;

        // Đổ dữ liệu vào Modal
        document.getElementById('modal_name').textContent = c.name || 'Khách hàng';
        document.getElementById('modal_type').textContent = c.subject || 'Liên hệ chung';
        document.getElementById('modal_email').textContent = c.email || 'Không cung cấp';
        document.getElementById('modal_phone').textContent = c.phone || 'Không cung cấp';
        document.getElementById('modal_date').textContent = c.created_at;
        document.getElementById('modal_message').textContent = c.message;

        var replySection = document.getElementById('reply_section');
        var repliedWrap = document.getElementById('replied_content_wrap');
        var repliedContent = document.getElementById('replied_content');
        var replyForm = document.getElementById('replyForm');
        var btnMarkRead = document.getElementById('btnMarkRead');

        replySection.style.display = 'block';

        // Nếu status là unread thì hiện nút Đánh dấu đã đọc
        if (c.status === 'unread' || c.status === 'pending') {
            btnMarkRead.style.display = 'inline-block';
        } else {
            btnMarkRead.style.display = 'none';
        }

        // Xử lý hiện Form hay hiện Nội dung đã phản hồi
        if (c.admin_note) {
            repliedWrap.style.display = 'block';
            repliedContent.textContent = c.admin_note;
            replyForm.style.display = 'none';
        } else {
            repliedWrap.style.display = 'none';
            replyForm.style.display = 'block';
            replyForm.action = '<?= BASE_URL ?>/index.php?route=admin_contact_reply&id=' + c.id;
        }

        $('#contactModal').modal('show');
    }

    // Hàm hỗ trợ Đánh dấu đã đọc bằng Form POST (Bảo mật CSRF)
    function markAsRead() {
        if (!currentContactId) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= BASE_URL ?>/index.php?route=admin_contact_read&id=' + currentContactId;

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = 'csrf_token';
        tokenInput.value = '<?= $_SESSION['csrf_token'] ?? '' ?>';

        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }

    // Hàm lọc dữ liệu bảng
    function filterStatus(status) {
        document.querySelectorAll('.sz-tab').forEach(t => t.classList.remove('active'));
        event.currentTarget.classList.add('active');

        document.querySelectorAll('tbody tr[data-status]').forEach(row => {
            if (status === 'all' || row.dataset.status === status || (status === 'unread' && row.dataset.status === 'pending')) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>