<?php

$azure = $data['azure'];

function getImageFromAzure($imagePath, $azure = [])
{
    $azureBaseUrl = 'https://' . $azure['account_name'] . '.blob.core.windows.net/' . $azure['container_name'] . '/';
    return $azureBaseUrl . ltrim($imagePath, '/');
}
?>

<div class="sz-page-header">
    <div>
        <h2 class="sz-page-title"><i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa sản phẩm</h2>
    </div>
</div>

<div class="row">
    <div class="col-12 mt-2">
        <form action="<?= BASE_URL ?>/index.php?route=admin_product_update&id=<?= $product['id'] ?>" method="POST"
            enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <div class="row">
                <!-- Cột trái: Thông tin cơ bản -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title mb-4"><i class="fa-solid fa-circle-info text-primary"></i> Thông tin
                                cơ bản</h4>

                            <div class="form-group">
                                <label for="name" class="font-weight-bold">Tên sản phẩm <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" required
                                    value="<?= htmlspecialchars($product['name']) ?>" placeholder="Nhập tên sản phẩm..."
                                    class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="description" class="font-weight-bold">Mô tả sản phẩm</label>
                                <textarea id="description" name="description" rows="5" placeholder="Mô tả chi tiết..."
                                    class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title mb-4"><i class="fa-solid fa-tags text-info"></i> Giá & Kho</h4>

                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="sku" class="font-weight-bold">Mã SKU</label>
                                    <input type="text" id="sku" name="sku"
                                        value="<?= htmlspecialchars($product['sku']) ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="stock" class="font-weight-bold">Tồn kho hiện tại</label>
                                    <input type="number" id="stock" name="stock" min="0"
                                        value="<?= $product['stock'] ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="font-weight-bold">Giá bán (Gốc) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" id="price" name="price" required min="0"
                                            value="<?= $product['price'] ?>" class="form-control">
                                        <div class="input-group-append">
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="sale_price" class="font-weight-bold">Giá khuyến mãi</label>
                                    <div class="input-group">
                                        <input type="number" id="sale_price" name="sale_price" min="0"
                                            value="<?= $product['sale_price'] ?>" class="form-control">
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
                            <h4 class="header-title mb-4"><i class="fa-solid fa-layer-group text-primary"></i> Phân loại
                            </h4>
                            <div class="form-group">
                                <label for="category_id" class="font-weight-bold">Danh mục <span
                                        class="text-danger">*</span></label>
                                <select id="category_id" name="category_id" required class="form-control">
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title mb-4"><i class="fa-solid fa-image text-success"></i> Hình ảnh</h4>

                            <!-- Danh sách ảnh cũ -->
                            <?php if (!empty($product['images'])): ?>
                                <div class="mb-3">
                                    <label class="font-weight-bold text-muted text-uppercase" style="font-size: 12px;">Ảnh
                                        đang có</label>
                                    <div class="row no-gutters">
                                        <?php foreach ($product['images'] as $img): ?>
                                            <div class="col-3 p-1">
                                                <div class="position-relative border rounded bg-light"
                                                    style="padding-bottom: 100%; overflow: hidden;">
                                                    <img src="<?= getImageFromAzure($img['image_path'], $azure) ?>"
                                                        alt="Ảnh sản phẩm" class="position-absolute"
                                                        style="width: 100%; height: 100%; object-fit: cover;">

                                                    <!-- Nút Xóa (Dấu X) -->
                                                    <form action="<?= BASE_URL ?>/index.php?route=admin_product_delete_image"
                                                        method="POST" class="position-absolute"
                                                        style="top: 5px; right: 5px; z-index: 20;"
                                                        onsubmit="return confirm('Xóa ảnh này?')">
                                                        <input type="hidden" name="csrf_token"
                                                            value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                        <button type="submit"
                                                            class="btn btn-danger btn-sm p-0 d-flex align-items-center justify-content-center shadow-sm"
                                                            style="width: 22px; height: 22px; border-radius: 50%; border: 1px solid white;">
                                                            <i class="fa-solid fa-xmark" style="font-size: 12px;"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Nút Primary (Nếu chưa là primary) -->
                                                    <?php if (!$img['is_primary']): ?>
                                                        <form action="<?= BASE_URL ?>/index.php?route=admin_product_set_primary"
                                                            method="POST" class="position-absolute w-100"
                                                            style="bottom: 0; left: 0; z-index: 10;">
                                                            <input type="hidden" name="csrf_token"
                                                                value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                                            <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                            <button type="submit" class="btn btn-dark btn-block btn-xs py-1"
                                                                style="font-size: 9px; opacity: 0.8; border-radius: 0; border: none;"
                                                                title="Đặt làm ảnh chính">
                                                                <i class="fa-solid fa-star"></i> SET MAIN
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <div class="position-absolute fixed-bottom bg-primary text-white text-center py-1 font-weight-bold"
                                                            style="font-size: 9px; opacity: 0.9;">
                                                            <i class="fa-solid fa-check-double"></i> MAIN
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="form-group mt-3">
                                <label class="font-weight-bold">Tải thêm / Ghi đè ảnh mới</label>
                                <div class="custom-file mb-2">
                                    <input type="file" class="custom-file-input" id="images" name="images[]" multiple
                                        accept=".jpg,.jpeg,.png,.webp,.gif">
                                    <label class="custom-file-label" for="images">Chọn file...</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title mb-4"><i class="fa-solid fa-toggle-on text-warning"></i> Trạng thái
                            </h4>

                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                    value="1" <?= $product['is_active'] ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="is_active">Công khai (Active)</label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured"
                                    value="1" <?= $product['is_featured'] ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="is_featured">Sản phẩm Nổi bật
                                    (Featured)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-block btn-lg"><i
                            class="fa-solid fa-floppy-disk"></i> LƯU THAY ĐỔI</button>
                    <a href="<?= BASE_URL ?>/index.php?route=admin_products"
                        class="btn btn-secondary btn-block mt-2">Hủy bỏ</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Script to show selected file names in custom-file input
    document.addEventListener('DOMContentLoaded', function () {
        var fileInput = document.getElementById('images');
        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                var files = e.target.files;
                var label = e.target.nextElementSibling;
                if (files && files.length > 1) {
                    label.innerHTML = files.length + ' file đã chọn';
                } else if (files && files.length === 1) {
                    label.innerHTML = files[0].name;
                } else {
                    label.innerHTML = 'Chọn file...';
                }
            });
        }

        // ✅ JS Validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function (e) {
            const name = document.getElementById('name').value.trim();
            const priceValue = document.getElementById('price').value;
            const price = parseFloat(priceValue);
            const salePriceValue = document.getElementById('sale_price').value;
            const salePrice = salePriceValue ? parseFloat(salePriceValue) : 0;

            let errors = [];
            if (!name) errors.push("- Vui lòng nhập tên sản phẩm.");
            if (priceValue === '' || isNaN(price) || price <= 0) errors.push("- Giá gốc phải là số dương.");
            if (salePrice > 0 && salePrice >= price) errors.push("- Giá khuyến mãi phải nhỏ hơn giá gốc.");

            if (errors.length > 0) {
                e.preventDefault();
                alert("Vui lòng kiểm tra lại dữ liệu:\n" + errors.join("\n"));
            }
        });
    });
</script>