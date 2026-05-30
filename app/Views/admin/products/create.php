<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-plus"></i> Thêm sản phẩm mới</h2>
    </div>
</div>

<div class="row">
    <div class="col-12 mt-2">
        <form action="<?= BASE_URL ?>/index.php?route=admin_product_store" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <div class="row">
                <!-- Cột trái: Thông tin cơ bản -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title mb-4"><i class="fa-solid fa-circle-info text-primary"></i> Thông tin cơ bản</h4>
                            
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" required placeholder="Nhập tên sản phẩm..." class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="font-weight-bold">Mô tả sản phẩm</label>
                                <textarea id="description" name="description" rows="5" placeholder="Mô tả chi tiết..." class="form-control"></textarea>
                                <small class="form-text text-muted">Cho phép copy paste HTML từ trình soạn thảo ngoài.</small>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title mb-4"><i class="fa-solid fa-tags text-info"></i> Giá & Kho</h4>
                            
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="sku" class="font-weight-bold">Mã SKU</label>
                                    <input type="text" id="sku" name="sku" placeholder="VD: SP001" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="stock" class="font-weight-bold">Tồn kho ban đầu</label>
                                    <input type="number" id="stock" name="stock" min="0" value="0" class="form-control">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="font-weight-bold">Giá bán (Gốc) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" id="price" name="price" required min="0" placeholder="0" class="form-control">
                                        <div class="input-group-append">
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="sale_price" class="font-weight-bold">Giá khuyến mãi</label>
                                    <div class="input-group">
                                        <input type="number" id="sale_price" name="sale_price" min="0" placeholder="0" class="form-control">
                                        <div class="input-group-append">
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Phân loại & Tham số -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title mb-4"><i class="fa-solid fa-layer-group text-primary"></i> Phân loại</h4>
                            <div class="form-group">
                                <label for="category_id" class="font-weight-bold">Danh mục <span class="text-danger">*</span></label>
                                <select id="category_id" name="category_id" required class="form-control">
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>&#9888; Chưa có danh mục — vui lòng tạo danh mục trước</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title mb-4"><i class="fa-solid fa-image text-success"></i> Media</h4>
                            <div class="form-group">
                                <label class="font-weight-bold">Hình ảnh sản phẩm</label>
                                <div class="sz-upload-zone" id="upload-zone">
                                    <input type="file" id="images" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp,.gif" class="sz-file-input">
                                    <div class="sz-upload-content">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                        <p>Kéo thả hoặc click để chọn ảnh</p>
                                        <span>Tối đa 5 file, định dạng JPG, PNG, WEBP</span>
                                    </div>
                                </div>
                                <div id="image-preview" class="row no-gutters mt-3">
                                    <!-- Previews will appear here -->
                                </div>
                                <div class="mt-3 p-3 bg-light rounded border-left border-warning" style="border-left-width: 4px !important;">
                                    <small class="text-secondary d-block mb-1">
                                        <i class="fa-solid fa-circle-info text-warning"></i> <strong>Lưu ý:</strong>
                                    </small>
                                    <small class="text-muted d-block">
                                        - Ảnh đầu tiên sẽ được chọn làm ảnh đại diện chính.<br>
                                        - Kích thước đề xuất: 800x800px. Tối đa 5MB/ảnh.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title mb-4"><i class="fa-solid fa-toggle-on text-warning"></i> Trạng thái</h4>
                            
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                <label class="custom-control-label" for="is_active">Công khai (Active)</label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" value="1">
                                <label class="custom-control-label" for="is_featured">Sản phẩm Nổi bật (Featured)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fa-solid fa-floppy-disk"></i> LƯU SẢN PHẨM</button>
                    <a href="<?= BASE_URL ?>/index.php?route=admin_products" class="btn btn-secondary btn-block mt-2">Hủy bỏ</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('images');
    const previewContainer = document.getElementById('image-preview');
    const uploadZone = document.getElementById('upload-zone');

    if(fileInput) {
        fileInput.addEventListener('change', function(e) {
            previewContainer.innerHTML = ''; // Clear previous previews
            const files = e.target.files;
            
            if (files) {
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const div = document.createElement('div');
                        div.className = 'col-4 p-1';
                        div.innerHTML = `
                            <div class="position-relative border rounded bg-white" style="padding-bottom: 100%; overflow: hidden;">
                                <img src="${event.target.result}" alt="Preview" class="position-absolute w-100 h-100" style="object-fit: cover;">
                                ${index === 0 ? '<div class="position-absolute fixed-bottom bg-warning text-dark text-center py-1" style="font-size: 10px; font-weight: 700; opacity: 0.9;">ẢNH CHÍNH</div>' : ''}
                            </div>
                        `;
                        previewContainer.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });

        // Add visual feedback for drag and drop
        uploadZone.addEventListener('dragover', () => uploadZone.classList.add('active'));
        uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('active'));
        uploadZone.addEventListener('drop', () => uploadZone.classList.remove('active'));
    }

    // ✅ JS Validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const priceValue = document.getElementById('price').value;
        const price = parseFloat(priceValue);
        const salePriceValue = document.getElementById('sale_price').value;
        const salePrice = salePriceValue ? parseFloat(salePriceValue) : 0;
        const categoryId = document.getElementById('category_id').value;

        let errors = [];
        if (!name) errors.push("- Vui lòng nhập tên sản phẩm.");
        if (priceValue === '' || isNaN(price) || price <= 0) errors.push("- Giá gốc phải là số dương.");
        if (salePrice > 0 && salePrice >= price) errors.push("- Giá khuyến mãi phải nhỏ hơn giá gốc.");
        if (!categoryId) errors.push("- Vui lòng chọn danh mục.");

        if (errors.length > 0) {
            e.preventDefault();
            alert("Vui lòng kiểm tra lại dữ liệu:\n" + errors.join("\n"));
        }
    });
});
</script>
