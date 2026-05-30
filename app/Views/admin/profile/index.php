<?php
$user = $user ?? [];

// Admin layout will handle the shell (sidebar, topbar)
?>

<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-user-gear"></i> Hồ sơ quản trị viên</h2>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-lg-4 col-md-5 mt-2">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="mb-4">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px; font-size: 50px;">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                </div>
                <h4 class="font-weight-bold mb-1"><?= htmlspecialchars($user['username'] ?? '') ?></h4>
                <span class="badge badge-primary py-1 px-3 text-uppercase mb-4">Super Admin</span>

                <div class="row border-top pt-4 mt-2">
                    <div class="col-6 border-right">
                        <h4 class="font-weight-bold mb-1">#<?= $user['id'] ?></h4>
                        <small class="text-muted text-uppercase font-weight-bold">Admin ID</small>
                    </div>
                    <div class="col-6">
                        <h4 class="font-weight-bold mb-1"><?= date('d/m/y', strtotime($user['created_at'] ?? 'now')) ?></h4>
                        <small class="text-muted text-uppercase font-weight-bold">Ngày tham gia</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Link -->
        <div class="card">
            <div class="card-body">
                <a href="<?= BASE_URL ?>/index.php?route=admin_dashboard" class="d-flex justify-content-between align-items-center text-decoration-none">
                    <div class="d-flex align-items-center">
                        <div class="rounded bg-info text-white d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <span class="font-weight-bold text-dark">Về Dashboard</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column: Settings -->
    <div class="col-lg-8 col-md-7 mt-2">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="header-title mb-4"><i class="fa-solid fa-user-gear text-primary"></i> Thông tin cá nhân</h4>
                
                <form action="<?= BASE_URL ?>/index.php?route=profile_update" method="POST">
                    <div class="form-group">
                        <label class="font-weight-bold">Họ và tên Quản trị</label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required class="form-control form-control-lg">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Tên định danh (Username)</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required class="form-control form-control-lg">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Email Quản trị</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required class="form-control form-control-lg">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-4">
                        <a href="<?= BASE_URL ?>/index.php?route=change_password" class="text-secondary font-weight-bold">
                            <i class="fa-solid fa-lock mr-1"></i> Đổi mật khẩu Admin
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg font-weight-bold px-4">
                            <i class="fa-solid fa-floppy-disk"></i> Cập nhật hồ sơ Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
