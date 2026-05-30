<?php
// Users index view - admin/users/index.php
$users = $users ?? [];
$totalPages = $totalPages ?? 1;
$page = $page ?? 1;
?>

<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-users"></i> Quản lý người dùng</h2>
    </div>
</div>

<!-- Search bar -->
<div class="sz-filter-bar">
    <form action="<?= BASE_URL ?>/index.php" method="GET" class="sz-search-form">
        <input type="hidden" name="route" value="admin_users">
        <div class="sz-input-icon">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="keyword" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                   placeholder="Tìm theo tên, email..." class="sz-input">
        </div>
        <button type="submit" class="sz-btn sz-btn-primary">Tìm kiếm</button>
    </form>
</div>

<!-- Users Table -->
<div class="sz-card">
    <div class="sz-table-wrap">
        <table class="sz-table">
            <thead>
                <tr>
                    <th style="width:60px">ID</th>
                    <th>Người dùng</th>
                    <th>Email</th>
                    <th style="width:100px" class="text-center">Vai trò</th>
                    <th style="width:120px" class="text-center">Trạng thái</th>
                    <th style="width:120px">Ngày tham gia</th>
                    <th style="width:80px" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="sz-id-cell">#<?= $u['id'] ?></td>
                            <td>
                                <div class="sz-user-cell">
                                    <div class="sz-avatar-sm" style="background:linear-gradient(135deg,
                                        <?= $u['role'] === 'admin' ? '#ff6600,#ff8533' : '#3b82f6,#60a5fa' ?>)">
                                        <?= strtoupper(substr($u['full_name'] ?? $u['username'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <span class="sz-user-name"><?= htmlspecialchars($u['full_name'] ?? $u['username'] ?? 'N/A') ?></span>
                                        <span class="sz-user-email">@<?= htmlspecialchars($u['username'] ?? '') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="sz-text-dim"><?= htmlspecialchars($u['email'] ?? '') ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="sz-badge sz-badge-warning"><i class="fa-solid fa-crown"></i> Admin</span>
                                <?php else: ?>
                                    <span class="sz-badge sz-badge-info"><i class="fa-solid fa-user"></i> User</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if (($u['status'] ?? 1) == 1): ?>
                                    <span class="sz-badge sz-badge-success"><i class="fa-solid fa-circle"></i> Hoạt động</span>
                                <?php else: ?>
                                    <span class="sz-badge sz-badge-danger"><i class="fa-solid fa-ban"></i> Bị khóa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="sz-fw-medium"><?= date('d/m/Y', strtotime($u['created_at'] ?? 'now')) ?></div>
                            </td>
                            <td class="text-center">
                                <div class="sz-action-group justify-content-center">
                                    <?php if ($u['role'] !== 'admin'): ?>
                                        <?php if (($u['status'] ?? 1) == 1): ?>
                                            <form action="<?= BASE_URL ?>/index.php?route=admin_user_ban&id=<?= $u['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Khóa tài khoản này?');">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                <button type="submit" class="sz-action-btn sz-action-delete border-0 bg-transparent" title="Khóa TK">
                                                    <i class="fa-solid fa-ban"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form action="<?= BASE_URL ?>/index.php?route=admin_user_unban&id=<?= $u['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Mở khóa tài khoản này?');">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                <button type="submit" class="sz-action-btn sz-action-approve border-0 bg-transparent" title="Mở khóa">
                                                    <i class="fa-solid fa-unlock"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="sz-text-dim sz-text-xs">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="sz-empty-state">
                                <i class="fa-solid fa-users"></i>
                                <h5>Chưa có người dùng nào</h5>
                                <p>Người dùng sẽ xuất hiện khi họ đăng ký.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="sz-pagination">
            <span class="sz-pagination-info">Trang <?= $page ?> / <?= $totalPages ?></span>
            <div class="sz-pagination-links">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= BASE_URL ?>/index.php?route=admin_users&page=<?= $i ?><?= !empty($_GET['keyword']) ? '&keyword='.urlencode($_GET['keyword']) : '' ?>"
                       class="sz-page-link <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
