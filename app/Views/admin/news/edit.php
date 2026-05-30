<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Page title area -->
<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa tin tức</h2>
    </div>
</div>

<div class="row">
    <div class="col-12 mt-2">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="header-title mb-4">Cập nhật thông tin bài viết</h4>
                
                <?php if (!empty($data['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($data['error']) ?></div>
                <?php endif; ?>

                <?php $newsItem = $data['newsItem'] ?? []; ?>
                <form action="<?= BASE_URL ?>/index.php?route=admin_news_update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($newsItem['id'] ?? '') ?>">
                    
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="font-weight-bold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($newsItem['title'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="slug" class="font-weight-bold">Đường dẫn thân thiện (Slug)</label>
                            <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($newsItem['slug'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="thumbnail" class="font-weight-bold">Ảnh đại diện (Bỏ trống nếu không đổi)</label>
                            <?php if(!empty($newsItem['thumbnail'])): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($newsItem['thumbnail']) ?>" width="150" alt="Current Cover" class="img-thumbnail rounded">
                            </div>
                            <?php endif; ?>
                            <input type="file" class="form-control-file border-0" id="thumbnail" name="thumbnail" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="font-weight-bold">Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="published" <?= (isset($newsItem['is_published']) && $newsItem['is_published'] == 1) ? 'selected' : '' ?>>Xuất bản</option>
                                <option value="draft" <?= (isset($newsItem['is_published']) && $newsItem['is_published'] == 0) ? 'selected' : '' ?>>Bản nháp</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="meta_keywords" class="font-weight-bold">Từ khóa SEO (Meta Keywords)</label>
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="<?= htmlspecialchars($newsItem['meta_keywords'] ?? '') ?>" placeholder="ví dụ: thể thao, bóng đá, giày nike">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="meta_description" class="font-weight-bold">Mô tả SEO (Meta Description)</label>
                            <input type="text" class="form-control" id="meta_description" name="meta_description" value="<?= htmlspecialchars($newsItem['meta_description'] ?? '') ?>" placeholder="Nhập mô tả ngắn cho bài viết...">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="content" class="font-weight-bold">Nội dung bài viết <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="15"><?= htmlspecialchars($newsItem['content'] ?? '') ?></textarea>
                    </div>

                    <button class="btn btn-primary px-4" type="submit"><i class="fa fa-save"></i> Cập nhật Bài Viết</button>
                    <a href="<?= BASE_URL ?>/index.php?route=admin_news" class="btn btn-secondary px-4 ml-2">Hủy bỏ</a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tích hợp CKEditor 5 thay thế TinyMCE để khắc phục lỗi Read-Only/API Key -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#content'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
        })
        .then(editor => {
            // Căn chỉnh chiều cao tối thiểu cho Editor để UI đẹp hơn
            editor.ui.view.editable.element.style.minHeight = '400px';

            // JS Validation on submit
            document.querySelector('form').addEventListener('submit', function(e) {
                const title = document.getElementById('title').value.trim();
                const content = editor.getData().trim();

                let errors = [];
                if (!title) errors.push("- Vui lòng nhập tiêu đề bài viết.");
                if (!content || content === '<p>&nbsp;</p>') errors.push("- Vui lòng nhập nội dung bài viết.");

                if (errors.length > 0) {
                    e.preventDefault();
                    alert("Vui lòng kiểm tra lại dữ liệu:\n" + errors.join("\n"));
                }
            });
        })
        .catch(error => {
            console.error(error);
        });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>