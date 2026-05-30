<?php
// Admin layout handles the shell
?>

<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-lock"></i> Đổi mật khẩu quản trị</h2>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <?php if (!empty($_SESSION['flash'])): 
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        ?>
        <div class="alert <?= $flash['type'] === 'success' ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
            <strong><i class="fa-solid fa-circle-<?= $flash['type'] === 'success' ? 'check' : 'exclamation' ?>"></i></strong> <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; font-size: 20px;">
                        <i class="fa-solid fa-lock-open"></i>
                    </div>
                    <div>
                        <h4 class="header-title mb-1">Cập nhật mật khẩu</h4>
                        <p class="text-muted mb-0 small">Bảo vệ tài khoản admin bằng mật khẩu mạnh.</p>
                    </div>
                </div>

                <form action="<?= BASE_URL ?>/index.php?route=post_change_password" method="POST">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" required class="form-control form-control-lg">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6 mb-4">
                            <label class="font-weight-bold">Mật khẩu mới <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" required class="form-control form-control-lg">
                            <small class="form-text text-muted italic mt-2">Ít nhất 8 ký tự, có chữ hoa, thường, số, ký tự đặc biệt.</small>
                        </div>

                        <div class="form-group col-md-6 mb-4">
                            <label class="font-weight-bold">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" required class="form-control form-control-lg">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center mt-3 pt-3 border-top">
                        <a href="<?= BASE_URL ?>/index.php?route=profile" class="btn btn-secondary btn-lg mr-3">Hủy</a>
                        <button type="submit" class="btn btn-primary btn-lg font-weight-bold">
                            Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
