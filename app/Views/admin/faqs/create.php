<?php
$isEdit    = isset($faq) && $faq;
$pageTitle = $isEdit ? 'Sửa FAQ' : 'Thêm FAQ Mới';
$action    = $isEdit
    ? BASE_URL . '/index.php?route=admin_faq_update&id=' . (int)$faq['id']
    : BASE_URL . '/index.php?route=admin_faq_store';

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

// Flash Errors
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-circle-question"></i> <?= $isEdit ? 'Chỉnh sửa FAQ' : 'Thêm FAQ mới' ?></h2>
    </div>
</div>

<!-- Validation Errors -->
<?php if (!empty($errors)): ?>
<div class="alert alert-danger" role="alert">
    <h5 class="alert-heading"><i class="fa-solid fa-circle-exclamation"></i> Vui lòng kiểm tra lại:</h5>
    <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="row">
    <!-- Cột trái: Form nhập liệu -->
    <div class="col-lg-8">
        <form method="POST" action="<?= $action ?>" id="faqForm">
            <!-- Danh mục + Thứ tự -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="header-title mb-4"><i class="fa-solid fa-layer-group text-primary"></i> Phân loại</h4>
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label for="faq_category_id" class="font-weight-bold">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" id="faq_category_id" required class="form-control">
                                <option value="">-- Chọn danh mục --</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= (int)$cat['id'] ?>" <?= ((int)($old['category_id'] ?? $faq['category_id'] ?? 0) === (int)$cat['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Fallback FAQ categories -->
                                    <option value="1">Tài khoản & Bảo mật</option>
                                    <option value="2">Đơn hàng & Thanh toán</option>
                                    <option value="3">Vận chuyển & Giao nhận</option>
                                    <option value="4">Chính sách đổi trả</option>
                                    <option value="5">Sản phẩm & Chất lượng</option>
                                    <option value="6">Chương trình khuyến mãi</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="faq_sort" class="font-weight-bold">Thứ tự hiển thị <small class="text-muted">(Số nhỏ = ưu tiên trước)</small></label>
                            <input type="number" name="sort_order" id="faq_sort" min="0" max="9999" value="<?= (int)($old['sort_order'] ?? $faq['sort_order'] ?? 0) ?>" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="faq_is_active" class="font-weight-bold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="is_active" id="faq_is_active" class="form-control">
                                <option value="1" <?= ((int)($old['is_active'] ?? $faq['is_active'] ?? 1) === 1) ? 'selected' : '' ?>>Hiển thị (Đã duyệt)</option>
                                <option value="0" <?= ((int)($old['is_active'] ?? $faq['is_active'] ?? 1) === 0) ? 'selected' : '' ?>>Ẩn (Chờ duyệt)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Câu hỏi -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="header-title mb-4"><i class="fa-solid fa-circle-question text-info"></i> Nội dung FAQ</h4>
                    
                    <div class="form-group">
                        <label for="faq_question" class="font-weight-bold d-flex justify-content-between">
                            <span>Câu hỏi <span class="text-danger">*</span></span>
                            <small class="text-muted"><span id="qLen">0</span>/500</small>
                        </label>
                        <input type="text" name="question" id="faq_question" required maxlength="500" placeholder="Nhập câu hỏi..." value="<?= htmlspecialchars($old['question'] ?? $faq['question'] ?? '') ?>" class="form-control">
                        <div class="progress mt-2" style="height: 3px;">
                            <div id="qBar" class="progress-bar bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="faq_answer" class="font-weight-bold">Câu trả lời <span class="text-danger">*</span></label>
                        <textarea name="answer" id="faq_answer" rows="7" required placeholder="Nhập câu trả lời chi tiết..." class="form-control"><?= htmlspecialchars($old['answer'] ?? $faq['answer'] ?? '') ?></textarea>
                        <small class="form-text text-muted">Xuống dòng mới sẽ được giữ nguyên khi hiển thị.</small>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mb-4">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-floppy-disk"></i> <?= $isEdit ? 'Lưu thay đổi' : 'Thêm FAQ' ?></button>
                <a href="<?= BASE_URL ?>/index.php?route=admin_faqs" class="btn btn-secondary btn-lg ml-2">Hủy</a>
            </div>
        </form>
    </div>

    <!-- Cột phải: Hướng dẫn -->
    <div class="col-lg-4">
        <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(to bottom, #fff, #f8faff);">
            <div class="card-body">
                <h4 class="header-title mb-4" style="color: var(--sz-orange) !important;">
                    <i class="fa-solid fa-lightbulb text-warning"></i> Hướng dẫn soạn thảo
                </h4>
                <div class="sz-guide-box">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fa-solid fa-layer-group text-primary mr-2" style="font-size: 14px;"></i>
                            <strong class="text-dark" style="font-size: 13px;">Phân loại danh mục</strong>
                        </div>
                        <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.5;">Giúp người dùng dễ dàng tìm kiếm câu hỏi theo nhóm chủ đề (Tài khoản, Thanh toán...).</p>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fa-solid fa-pen-nib text-primary mr-2" style="font-size: 14px;"></i>
                            <strong class="text-dark" style="font-size: 13px;">Đặt câu hỏi hiệu quả</strong>
                        </div>
                        <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.5;">Câu hỏi nên đi thẳng vào vấn đề. VD: "Làm sao để đổi size giày?" thay vì "Tôi mua giày bị chật".</p>
                    </div>

                    <div class="mb-0">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fa-solid fa-sort-numeric-down text-primary mr-2" style="font-size: 14px;"></i>
                            <strong class="text-dark" style="font-size: 13px;">Sắp xếp thứ tự</strong>
                        </div>
                        <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.5;">Số nhỏ hơn sẽ luôn xuất hiện ở vị trí đầu tiên trong danh sách của danh mục đó.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h4 class="header-title mb-4"><i class="fa-solid fa-link text-info"></i> Liên kết nhanh</h4>
                <div class="d-flex flex-column gap-2">
                    <a href="<?= BASE_URL ?>/index.php?route=admin_faqs" class="text-secondary mb-2"><i class="fa-solid fa-list mr-2"></i> Danh sách FAQ</a>
                    <a href="<?= BASE_URL ?>/index.php?route=faqs" target="_blank" class="text-secondary"><i class="fa-solid fa-arrow-up-right-from-square mr-2"></i> Xem trang FAQ (mở tab mới)</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var q   = document.getElementById('faq_question');
    var len = document.getElementById('qLen');
    var bar = document.getElementById('qBar');

    function updateBar() {
        if (!q || !len || !bar) return;
        var n = q.value.length;
        len.textContent = n;
        var pct = (n / 500) * 100;
        bar.style.width = pct + '%';
        if (pct > 90) {
            bar.classList.remove('bg-primary');
            bar.classList.add('bg-danger');
        } else {
            bar.classList.add('bg-primary');
            bar.classList.remove('bg-danger');
        }
    }

    if (q) {
        q.addEventListener('input', updateBar);
        updateBar();
    }

    document.getElementById('faqForm').addEventListener('submit', function (e) {
        var errors = [];
        if (!document.getElementById('faq_category_id').value) errors.push('Vui lòng chọn danh mục.');
        if (q && q.value.trim().length < 3) errors.push('Câu hỏi tối thiểu 3 ký tự.');
        var ans = document.getElementById('faq_answer');
        if (ans && ans.value.trim().length < 5) errors.push('Câu trả lời tối thiểu 5 ký tự.');
        if (errors.length) {
            e.preventDefault();
            alert(errors.join('\n'));
        }
    });
})();
</script>
