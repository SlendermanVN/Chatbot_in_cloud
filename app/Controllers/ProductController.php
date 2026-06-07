<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Review.php';

require_once __DIR__ . '/../Classes/HttpClient.php';

class ProductController extends BaseController
{
    //TODO Văn Phát: Xử lý hiển thị danh sách sản phẩm, chi tiết sản phẩm, và CRUD cho admin
    private $productModel;
    private $reviewModel;
    private $azureBlobStorage;
    private $containerName = 'sportzoneimage';

    public function __construct($pdo, $cloud)
    {
        parent::__construct($pdo);
        $this->productModel = new Product($pdo);
        $this->settingModel = new Setting($pdo);
        $this->reviewModel = new Review($pdo);
        $this->azureBlobStorage = $cloud->BlobStorage();
    }

    // ============================================================
    // PUBLIC
    // ============================================================

    /**
     * Danh sách sản phẩm (có tìm kiếm + lọc danh mục + phân trang)
     * URL: ?route=products&page=1&keyword=nike&category=1
     */
    public function index()
    {
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Sửa lỗi: View gửi name="search"
        $keyword = isset($_GET['search']) ? trim(htmlspecialchars($_GET['search'])) : '';

        // Sửa lỗi: View gửi name="categories[]" (array)
        $categoryIds = isset($_GET['categories']) && is_array($_GET['categories']) ? array_map('intval', $_GET['categories']) : null;

        $products = $this->productModel->getAll($limit, $offset, $categoryIds, $keyword ?: null);
        $total = $this->productModel->countAll($categoryIds, $keyword ?: null);
        $totalPages = ceil($total / $limit);
        $categories = $this->productModel->getCategories();

        $azure = [
            'account_name' => $this->azureBlobStorage['account_name'],
            'account_key' => $this->azureBlobStorage['account_key'],
            'container_name' => $this->containerName,
        ];

        $rawSettings = $this->settingModel->getAll();
        $setting = array_column($rawSettings, 'setting_value', 'setting_key');

        $this->render('products/index', compact(
            'products',
            'total',
            'totalPages',
            'page',
            'keyword',
            'categoryIds',
            'categories',
            'setting',
            'azure'
        ));
    }

    /**
     * Chi tiết sản phẩm
     * URL: ?route=product_detail&id=1
     */
    public function detail($idOrSlug)
    {
        // Ưu tiên tìm theo Slug nếu đầu vào không phải là số thuần túy (hoặc có param slug)
        // Tuy nhiên router đang truyền $id từ $_GET['id']. 
        // Ta sẽ đổi router để truyền cả 2 hoặc ưu tiên slug.

        $product = null;
        if (is_numeric($idOrSlug)) {
            $product = $this->productModel->getById($idOrSlug);
        } else {
            $product = $this->productModel->getBySlug($idOrSlug);
        }

        if (!$product) {
            http_response_code(404);
            $this->render('404', [], '404 - Không tìm thấy');
            return;
        }

        $id = $product['id'];

        // Kiểm tra SP đã trong giỏ chưa (nếu đã login)
        $inCart = false;
        if (isset($_SESSION['user_id'])) {
            $inCart = $this->productModel->checkInCart($_SESSION['user_id'], $id);
        }

        // Kiểm tra user đã mua SP chưa (để có thể viết review)
        $hasPurchased = false;
        if (isset($_SESSION['user_id'])) {
            $hasPurchased = $this->productModel->hasPurchased($_SESSION['user_id'], $id);
        }

        // SP liên quan cùng danh mục
        $related = [];
        if ($product['category_id']) {
            $related = $this->productModel->getRelated($product['category_id'], $id, 4);
        }

        // Lấy danh sách đánh giá
        $reviews = $this->reviewModel->getByProductId($id);

        $this->render(
            'products/detail',
            compact('product', 'inCart', 'hasPurchased', 'related', 'reviews'),
            htmlspecialchars($product['name']) . ' - SportZone'
        );
    }

    /**
     * Lưu đánh giá sản phẩm
     * URL: ?route=product_review_store
     */
    public function storeReview()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
            $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 5;
            $content = isset($_POST['content']) ? trim(htmlspecialchars($_POST['content'])) : '';

            if ($productId <= 0 || empty($content)) {
                $this->redirectWithMessage('product_detail&id=' . $productId, 'error', 'Vui lòng nhập nội dung đánh giá.');
                return;
            }

            $success = $this->reviewModel->addProductReview($_SESSION['user_id'], $productId, $rating, $content);

            if ($success) {
                $this->redirectWithMessage('product_detail&id=' . $productId, 'success', 'Cảm ơn bạn! Đánh giá của bạn đã được gửi và đang chờ duyệt.');
            } else {
                $this->redirectWithMessage('product_detail&id=' . $productId, 'error', 'Có lỗi xảy ra, vui lòng thử lại.');
            }
        }
    }

    // ============================================================
    // ADMIN - CRUD
    // ============================================================

    /**
     * Admin: Danh sách sản phẩm
     */
    public function adminIndex()
    {
        $this->requireAdmin();

        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;
        $keyword = isset($_GET['keyword']) ? trim(htmlspecialchars($_GET['keyword'])) : '';

        $products = $this->productModel->getAllAdmin($limit, $offset, $keyword);
        $total = $this->productModel->countAllAdmin($keyword);
        $totalPages = ceil($total / $limit);

        $azure = $this->azureBlobStorage;
        $azure += ['container_name' => $this->containerName];

        $this->render('admin/products/index', compact(
            'products',
            'total',
            'totalPages',
            'page',
            'keyword',
            'azure'
        ), 'Quản lý Sản phẩm - Admin');
    }

    /**
     * Admin: danh sách SP (có search + phân trang)
     * URL: ?route=admin_product_create
     */
    public function create()
    {
        $this->requireAdmin();
        $categories = $this->productModel->getCategories();

        $azure = $this->azureBlobStorage;
        $azure += ['container_name' => $this->containerName];

        $this->render('admin/products/create', compact('categories', 'azure'), 'Thêm sản phẩm - Admin');
    }

    /**
     * Admin: lưu SP mới vào DB
     */
    public function store()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin_product_create');
            return;
        }

        // CSRF check
        $submittedToken = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
            $this->redirectWithMessage('admin_product_create', 'error', 'Token bảo mật không hợp lệ!');
            return;
        }

        // ---- Validate ----
        $errors = [];
        $name = trim($_POST['name'] ?? '');
        $price = trim($_POST['price'] ?? '');

        if (empty($name))
            $errors[] = 'Tên sản phẩm không được để trống.';
        if (!is_numeric($price) || $price <= 0)
            $errors[] = 'Giá phải là số dương.';

        $salePrice = !empty($_POST['sale_price']) ? (float) $_POST['sale_price'] : null;
        if ($salePrice !== null && $salePrice >= (float) $price) {
            $errors[] = 'Giá khuyến mãi phải nhỏ hơn giá gốc.';
        }

        if ($errors) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => implode('<br>', $errors)];
            $this->redirect('admin_product_create');
            return;
        }

        // ---- Chuẩn bị data ----
        $slug = Product::makeSlug($name);

        $data = [
            'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
            'name' => htmlspecialchars($name),
            'slug' => $slug,
            'description' => $_POST['description'] ?? '',  // WYSIWYG — Đắc Nghĩa handle sanitize
            'price' => (float) $price,
            'sale_price' => $salePrice,
            'stock' => (int) ($_POST['stock'] ?? 0),
            'sku' => htmlspecialchars(trim($_POST['sku'] ?? '')),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'meta_title' => htmlspecialchars(trim($_POST['meta_title'] ?? '')),
            'meta_description' => htmlspecialchars(trim($_POST['meta_description'] ?? '')),
        ];

        $productId = $this->productModel->create($data);

        // ---- Xử lý upload ảnh ----
        if ($productId && isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $uploadedImages = $this->handleUploadMultiple($_FILES['images'], $productId);
            // Ảnh đầu tiên là primary
            if (!empty($uploadedImages)) {
                $this->productModel->addImage($productId, $uploadedImages[0], $data['name'], 1);
                foreach (array_slice($uploadedImages, 1) as $img) {
                    $this->productModel->addImage($productId, $img, $data['name'], 0);
                }
            }
        }

        $this->redirectWithMessage('admin_products', 'success', 'Thêm sản phẩm thành công!');
    }

    /**
     * Admin: form sửa sản phẩm
     * URL: ?route=admin_product_edit&id=1
     */
    public function edit($id)
    {
        $this->requireAdmin();

        $product = $this->productModel->getByIdForAdmin($id);

        if (!$product) {
            $this->redirectWithMessage('admin_orders', 'error', 'Sản phẩm không tồn tại.');
            return;
        }

        // Lấy ảnh hiện tại
        $product['images'] = $this->productModel->getImages($id);

        $categories = $this->productModel->getCategories();
        $azure = $this->azureBlobStorage;
        $azure += ['container_name' => $this->containerName];
        $this->render('admin/products/edit', compact('product', 'categories', 'azure'), 'Sửa sản phẩm - Admin');
    }

    /**
     * Admin: cập nhật sản phẩm
     */
    public function update($id)
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin_product_edit&id=' . $id);
            return;
        }

        // CSRF check
        $submittedToken = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
            $this->redirectWithMessage('admin_product_edit&id=' . $id, 'error', 'Token bảo mật không hợp lệ!');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $errors = [];

        if (empty($name))
            $errors[] = 'Tên sản phẩm không được để trống.';
        if (!is_numeric($price) || $price <= 0)
            $errors[] = 'Giá phải là số dương.';

        $salePrice = !empty($_POST['sale_price']) ? (float) $_POST['sale_price'] : null;
        if ($salePrice !== null && $salePrice >= (float) $price) {
            $errors[] = 'Giá khuyến mãi phải nhỏ hơn giá gốc.';
        }

        if ($errors) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => implode('<br>', $errors)];
            $this->redirect('admin_product_edit&id=' . $id);
            return;
        }

        $data = [
            'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
            'name' => htmlspecialchars($name),
            'slug' => Product::makeSlug($name),
            'description' => $_POST['description'] ?? '',
            'price' => (float) $price,
            'sale_price' => $salePrice,
            'stock' => (int) ($_POST['stock'] ?? 0),
            'sku' => htmlspecialchars(trim($_POST['sku'] ?? '')),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'meta_title' => htmlspecialchars(trim($_POST['meta_title'] ?? '')),
            'meta_description' => htmlspecialchars(trim($_POST['meta_description'] ?? '')),
        ];

        $this->productModel->update($id, $data);

        // Upload ảnh mới nếu có
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $uploadedImages = $this->handleUploadMultiple($_FILES['images'], $id);
            foreach ($uploadedImages as $img) {
                $this->productModel->addImage($id, $img, $data['name'], 0);
            }
        }

        $this->redirectWithMessage('admin_product_edit&id=' . $id, 'success', 'Cập nhật sản phẩm thành công!');
    }

    /**
     * Admin: xóa sản phẩm (soft delete)
     */
    public function delete($id)
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage('admin_products', 'error', 'Yêu cầu không hợp lệ.');
            return;
        }

        // CSRF check
        $submittedToken = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
            $this->redirectWithMessage('admin_products', 'error', 'Token bảo mật không hợp lệ!');
            return;
        }

        $product = $this->productModel->getByIdForAdmin($id);
        if (!$product) {
            $this->redirectWithMessage('admin_products', 'error', 'Sản phẩm không tồn tại.');
            return;
        }

        $imagePath = $product['image_path'] ?? null;
        if ($imagePath) {
            try {
                $date = gmdate('D, d M Y H:i:s \G\M\T');
                $url = "https://{$this->azureBlobStorage->accountName}.blob.core.windows.net/{$this->containerName}/{$imagePath}";

                $canonicalizedHeaders = "x-ms-date:{$date}\nx-ms-version:2021-08-06";
                $canonicalizedResource = "/{$this->azureBlobStorage->accountName}/{$this->containerName}/{$imagePath}";

                $stringToSign = "DELETE\n\n\n\n\n\n\n\n\n\n\n\n{$canonicalizedHeaders}\n{$canonicalizedHeaders}\n{$canonicalizedResource}";
                $signature = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($this->azureBlobStorage->accountKey), true));

                $headers = [
                    'x-ms-date' => $date,
                    'x-ms-version' => '2021-08-06',
                    'Authorization' => "SharedKey {$this->azureBlobStorage->accountName}:{$signature}"
                ];

                HttpClient::request('DELETE', $url, [], $headers, 10);
            } catch (Exception $e) {
                error_log("Lỗi xóa ảnh từ Azure Blob Storage: " . $e->getMessage());
            }
        }

        $this->productModel->delete($id);
        $this->redirectWithMessage('admin_products', 'success', 'Đã ẩn sản phẩm thành công.');
    }

    // ============================================================
    // HELPER: Upload ảnh
    // ============================================================

    /**
     * Xử lý upload nhiều ảnh cùng lúc
     * Validate: chỉ cho phép jpg, png, webp, gif — tối đa 5MB/ảnh
     * @return array Danh sách đường dẫn ảnh đã lưu
     */
    private function handleUploadMultiple($files, $productId)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $uploadDir = 'products/'; // Thư mục con trong Azure Blob Storage
        $saved = [];

        // Kiểm tra kết nối Azure Blob Storage     
        if (!$this->azureBlobStorage) {
            throw new Exception("Không thể kết nối Azure Blob Storage. Vui lòng kiểm tra cấu hình.");
        }

        // $_FILES['images'] khi multiple: name là array
        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK)
                continue;
            if ($files['size'][$i] > $maxSize)
                continue;

            // Kiểm tra MIME type thật (không tin vào extension)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $files['tmp_name'][$i]);
            finfo_close($finfo);


            if (!in_array($mimeType, $allowedTypes))
                continue;

            // Đổi tên file để tránh trùng và tránh path traversal
            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $newName = 'product_' . $productId . '_' . time() . '_' . $i . '.' . $ext;
            $dest = $uploadDir . $newName;

            // Thêm bước đọc file thành binary data để upload lên Azure Blob Storage
            try {

                $binaryData = file_get_contents($files['tmp_name'][$i]);
                $date = gmdate('D, d M Y H:i:s \G\M\T');

                // Xây dựng chuỗi ký tên xác thực theo yêu cầu của Azure Blob Storage
                $canonicalizedHeaders = "x-ms-blob-type:BlockBlob\nx-ms-date:{$date}\nx-ms-version:2021-08-06";
                $canonicalizedResource = "/{$this->azureBlobStorage->accountName}/{$this->containerName}/{$dest}";

                $stringToSign = "PUT\n\n\n{$files['size'][$i]}\n\n{$mimeType}\n\n\n\n\n\n\n{$canonicalizedHeaders}\n{$canonicalizedResource}";
                $signature = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($this->azureBlobStorage->accountKey), true));

                $url = "https://{$this->azureBlobStorage->accountName}.blob.core.windows.net/{$this->containerName}/{$dest}";

                $headers = [
                    "Authorization: SharedKey {$this->azureBlobStorage->accountName}:{$signature}",
                    "x-ms-blob-type: BlockBlob",
                    "x-ms-date: {$date}",
                    "x-ms-version: 2021-08-06",
                    "Content-Type: {$mimeType}",
                    "Content-Length: {$files['size'][$i]}"
                ];

                HttpClient::request('PUT', $url, $binaryData, $headers, 20);
            } catch (Exception $e) {
                // Log lỗi chi tiết để debug
                error_log("Lỗi upload ảnh lên Azure Blob Storage: " . $e->getMessage());
                continue; // Bỏ qua file này và tiếp tục với file tiếp theo
            }

            if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
                $saved[] = 'products/' . $newName;
            }
        }

        return $saved;
    }
    public function deleteImage()
    {
        $this->requireAdmin();
        $imageId = (int) ($_POST['image_id'] ?? 0);
        $productId = (int) ($_POST['product_id'] ?? 0);

        if ($imageId && $productId) {
            $submittedToken = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
                $this->redirectWithMessage('admin_product_edit&id=' . $productId, 'error', 'Token bảo mật không hợp lệ!');
                return;
            }

            $image = $this->productModel->getImageById($imageId);
            if ($image) {
                $filePath = $image['image_path'];
                $this->productModel->deleteImage($imageId, $productId);
                $this->redirectWithMessage('admin_product_edit&id=' . $productId, 'success', 'Đã xóa ảnh thành công!');

                if (empty($filePath)) {
                    error_log("Ảnh sản phẩm ID {$imageId} không có đường dẫn lưu trữ.");
                    return;
                }

                try {
                    $date = gmdate('D, d M Y H:i:s \G\M\T');
                    $url = "https://{$this->azureBlobStorage->accountName}.blob.core.windows.net/{$this->containerName}/{$filePath}";

                    $canonicalizedHeaders = "x-ms-date:{$date}\nx-ms-version:2021-08-06";
                    $canonicalizedResource = "/{$this->azureBlobStorage->accountName}/{$this->containerName}/{$filePath}";

                    $stringToSign = "DELETE\n\n\n\n\n\n\n\n\n\n\n\n{$canonicalizedHeaders}\n{$canonicalizedResource}";
                    $signature = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($this->azureBlobStorage->accountKey), true));

                    $headers = [
                        "Authorization: SharedKey {$this->azureBlobStorage->accountName}:{$signature}",
                        "x-ms-date: {$date}",
                        "x-ms-version: 2021-08-06"
                    ];

                    HttpClient::request('DELETE', $url, [], $headers, 10);
                } catch (Exception $e) {
                    error_log("Lỗi xóa ảnh từ Azure Blob Storage: " . $e->getMessage());
                }
                return;
            }
        }
        $this->redirectWithMessage('admin_products', 'error', 'Lỗi khi xóa ảnh!');
    }

    public function setPrimaryImage()
    {
        $this->requireAdmin();
        $imageId = (int) ($_POST['image_id'] ?? 0);
        $productId = (int) ($_POST['product_id'] ?? 0);

        if ($imageId && $productId) {
            $submittedToken = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
                $this->redirectWithMessage('admin_product_edit&id=' . $productId, 'error', 'Token bảo mật không hợp lệ!');
                return;
            }

            $this->productModel->setPrimaryImage($productId, $imageId);
            $this->redirectWithMessage('admin_product_edit&id=' . $productId, 'success', 'Đã đặt làm ảnh chính!');
            return;
        }
        $this->redirectWithMessage('admin_products', 'error', 'Lỗi khi đặt ảnh chính!');
    }
}
