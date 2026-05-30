<?php
// settings/index.php - Admin System Settings
$settings = $settings ?? [];

// Helper: get setting value
function sv($settings, $key, $default = '') {
    foreach ($settings as $s) {
        if ($s['setting_key'] === $key) return $s['setting_value'];
    }
    return $default;
}
?>

<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-sliders"></i> Cài đặt hệ thống</h2>
    </div>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <?php $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
    <div class="sz-alert sz-alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid fa-circle-<?= $flash['type'] === 'success' ? 'check' : 'exclamation' ?>"></i>
        <?= htmlspecialchars($flash['message']) ?>
        <button class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form action="<?= BASE_URL ?>/index.php?route=admin_settings_save" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

    <div class="sz-settings-grid">

        <!-- General Info -->
        <div class="sz-card sz-settings-section">
            <div class="sz-settings-section-header">
                <div class="sz-settings-section-icon" style="background:rgba(255,102,0,0.12);color:#ff6600">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <h4 class="sz-section-title">Thông tin Website</h4>
                    <p class="sz-section-desc">Tên, logo, và thông tin cơ bản của cửa hàng</p>
                </div>
            </div>
            <div class="sz-settings-body">
                <div class="sz-form-group">
                    <label class="sz-label">Tên website</label>
                    <input type="text" name="site_name" value="<?= htmlspecialchars(sv($settings, 'site_name', 'SportZone Vietnam')) ?>" class="sz-input sz-input-full" placeholder="SportZone Vietnam">
                </div>
                <div class="sz-form-group">
                    <label class="sz-label">Mô tả website (SEO)</label>
                    <textarea name="meta_description" rows="3" class="sz-input sz-input-full" placeholder="Mô tả ngắn gọn..."><?= htmlspecialchars(sv($settings, 'meta_description')) ?></textarea>
                </div>
                <div class="sz-form-group">
                    <label class="sz-label">Email liên hệ</label>
                    <input type="email" name="contact_email" value="<?= htmlspecialchars(sv($settings, 'contact_email', 'contact@sportzone.vn')) ?>" class="sz-input sz-input-full">
                </div>
                <div class="sz-form-group">
                    <label class="sz-label">Số điện thoại</label>
                    <input type="text" name="contact_phone" value="<?= htmlspecialchars(sv($settings, 'contact_phone')) ?>" class="sz-input sz-input-full" placeholder="0901 234 567">
                </div>
            </div>
        </div>

        <!-- Store Config -->
        <div class="sz-card sz-settings-section">
            <div class="sz-settings-section-header">
                <div class="sz-settings-section-icon" style="background:rgba(16,185,129,0.12);color:#10b981">
                    <i class="fa-solid fa-gear"></i>
                </div>
                <div>
                    <h4 class="sz-section-title">Cài đặt Cửa hàng</h4>
                    <p class="sz-section-desc">Đơn vị tiền tệ, vận chuyển, và thanh toán</p>
                </div>
            </div>
            <div class="sz-settings-body">
                <div class="sz-form-group">
                    <label class="sz-label">Đơn vị tiền tệ</label>
                    <select name="currency" class="sz-input sz-input-full">
                        <option value="VND" <?= sv($settings, 'currency', 'VND') === 'VND' ? 'selected' : '' ?>>VND – Đồng Việt Nam</option>
                        <option value="USD" <?= sv($settings, 'currency') === 'USD' ? 'selected' : '' ?>>USD – US Dollar</option>
                    </select>
                </div>
                <div class="sz-form-group">
                    <label class="sz-label">Phí vận chuyển cố định (VNĐ)</label>
                    <input type="number" name="shipping_fee" value="<?= htmlspecialchars(sv($settings, 'shipping_fee', '30000')) ?>" class="sz-input sz-input-full" min="0">
                </div>
                <div class="sz-form-group">
                    <label class="sz-label">Miễn phí ship từ (VNĐ)</label>
                    <input type="number" name="free_shipping_threshold" value="<?= htmlspecialchars(sv($settings, 'free_shipping_threshold', '500000')) ?>" class="sz-input sz-input-full" min="0">
                </div>
                <div class="sz-form-row">
                    <div class="sz-toggle-group">
                        <label class="sz-label">Cho phép đặt hàng khi hết hàng</label>
                        <label class="sz-toggle-switch">
                            <input type="checkbox" name="allow_backorder" value="1"
                                   <?= sv($settings, 'allow_backorder') === '1' ? 'checked' : '' ?>>
                            <span class="sz-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="sz-toggle-group">
                        <label class="sz-label">Website đang hoạt động</label>
                        <label class="sz-toggle-switch">
                            <input type="checkbox" name="site_active" value="1"
                                   <?= sv($settings, 'site_active', '1') !== '0' ? 'checked' : '' ?>>
                            <span class="sz-toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social / Address -->
        <div class="sz-card sz-settings-section">
            <div class="sz-settings-section-header">
                <div class="sz-settings-section-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6">
                    <i class="fa-solid fa-share-nodes"></i>
                </div>
                <div>
                    <h4 class="sz-section-title">Địa chỉ & Mạng xã hội</h4>
                    <p class="sz-section-desc">Địa chỉ cửa hàng và liên kết mạng xã hội</p>
                </div>
            </div>
            <div class="sz-settings-body">
                <div class="sz-form-group">
                    <label class="sz-label">Địa chỉ cửa hàng</label>
                    <input type="text" name="store_address" value="<?= htmlspecialchars(sv($settings, 'store_address')) ?>" class="sz-input sz-input-full" placeholder="123 Đường ABC, TP.HCM">
                </div>
                <div class="sz-form-group">
                    <label class="sz-label"><i class="fa-brands fa-facebook" style="color:#1877f2"></i> Facebook URL</label>
                    <input type="url" name="social_facebook" value="<?= htmlspecialchars(sv($settings, 'social_facebook')) ?>" class="sz-input sz-input-full" placeholder="https://facebook.com/...">
                </div>
                <div class="sz-form-group">
                    <label class="sz-label"><i class="fa-brands fa-instagram" style="color:#e4405f"></i> Instagram URL</label>
                    <input type="url" name="social_instagram" value="<?= htmlspecialchars(sv($settings, 'social_instagram')) ?>" class="sz-input sz-input-full" placeholder="https://instagram.com/...">
                </div>
                <div class="sz-form-group">
                    <label class="sz-label"><i class="fa-brands fa-youtube" style="color:#ff0000"></i> YouTube URL</label>
                    <input type="url" name="social_youtube" value="<?= htmlspecialchars(sv($settings, 'social_youtube')) ?>" class="sz-input sz-input-full" placeholder="https://youtube.com/...">
                </div>
            </div>
        </div>

        <!-- About Page Content -->
        <div class="sz-card sz-settings-section" style="grid-column: span 2;">
            <div class="sz-settings-section-header">
                <div class="sz-settings-section-icon" style="background:rgba(139,92,246,0.12);color:#8b5cf6">
                    <i class="fa-solid fa-address-card"></i>
                </div>
                <div>
                    <h4 class="sz-section-title">Nội dung Trang Giới thiệu</h4>
                    <p class="sz-section-desc">Quản lý tiêu đề, mô tả và tầm nhìn sứ mệnh</p>
                </div>
            </div>
            <div class="sz-settings-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="sz-form-group">
                            <label class="sz-label">Tiêu đề chính</label>
                            <input type="text" name="about_title" value="<?= htmlspecialchars(sv($settings, 'about_title', 'Về SportZone Vietnam')) ?>" class="sz-input sz-input-full">
                        </div>
                        <div class="sz-form-group">
                            <label class="sz-label">Mô tả ngắn</label>
                            <textarea name="about_description" rows="5" class="sz-input sz-input-full"><?= htmlspecialchars(sv($settings, 'about_description')) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="sz-form-group">
                            <label class="sz-label">Sứ mệnh</label>
                            <textarea name="about_mission" rows="2" class="sz-input sz-input-full"><?= htmlspecialchars(sv($settings, 'about_mission')) ?></textarea>
                        </div>
                        <div class="sz-form-group">
                            <label class="sz-label">Tầm nhìn</label>
                            <textarea name="about_vision" rows="2" class="sz-input sz-input-full"><?= htmlspecialchars(sv($settings, 'about_vision')) ?></textarea>
                        </div>
                        <div class="sz-form-group">
                            <label class="sz-label">Giá trị cốt lõi</label>
                            <textarea name="about_values" rows="2" class="sz-input sz-input-full"><?= htmlspecialchars(sv($settings, 'about_values')) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- end grid -->

    <!-- Save Bar -->
    <div class="sz-save-bar">
        <div class="sz-save-bar-info">
            <i class="fa-solid fa-circle-info"></i>
            Thay đổi sẽ được áp dụng ngay lập tức sau khi lưu.
        </div>
        <div class="sz-save-bar-actions">
            <a href="<?= BASE_URL ?>/index.php?route=admin_dashboard" class="sz-btn sz-btn-ghost">Hủy</a>
            <button type="submit" class="sz-btn sz-btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Lưu cài đặt
            </button>
        </div>
    </div>
</form>
