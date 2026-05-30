<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Cart.php';
require_once __DIR__ . '/../Models/Product.php';

class CartController extends BaseController
{
    //TODO Văn Phát: Xử lý giỏ hàng (thêm, cập nhật, xóa) và hiển thị giỏ hàng

    private $cartModel;
    private $productModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->cartModel = new Cart($pdo);
        $this->productModel = new Product($pdo);
    }

    /**
     * Hiển thị trang giỏ hàng
     * URL: ?route=cart
     */
    public function index()
    {
        $this->requireLogin();

        $userId = $_SESSION['user_id'];
        $items = $this->cartModel->getByUser($userId);
        $total = $this->cartModel->getTotal($userId);

        $this->render('cart/index', compact('items', 'total'), 'Giỏ hàng - SportZone');
    }

    /**
     * Thêm SP vào giỏ (AJAX hoặc form POST)
     * URL: ?route=cart_add  [POST: product_id, quantity]
     */
    public function add()
    {
        $this->requireLogin();

        $submittedToken = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
            $this->json(['success' => false, 'message' => 'Lỗi bảo mật CSRF'], 403);
            return;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        if (!$productId) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ.'], 400);
            return;
        }

        $product = $this->productModel->getById($productId);
        if (!$product || !$product['is_active']) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.'], 400);
            return;
        }

        $success = $this->cartModel->addItem($_SESSION['user_id'], $productId, $quantity);
        $cartCount = $this->cartModel->countItems($_SESSION['user_id']);
        $_SESSION['cart_count'] = $cartCount;

        // Nếu request là AJAX → trả JSON
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            $this->json([
                'success' => $success,
                'message' => $success ? 'Đã thêm vào giỏ hàng!' : 'Không thể thêm vào giỏ hàng.',
                'cart_count' => $cartCount,
            ]);
        }

        // Nếu là form thường → redirect
        if ($success) {
            $this->redirectWithMessage('cart', 'success', 'Đã thêm sản phẩm vào giỏ hàng!');
        } else {
            $this->redirectWithMessage('products', 'error', 'Không thể thêm vào giỏ hàng.');
        }
    }

    /**
     * Cập nhật số lượng SP trong giỏ (AJAX)
     * URL: ?route=cart_update  [POST: product_id, quantity]
     */
    public function update()
    {
        $this->requireLogin();

        $submittedToken = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
            $this->json(['success' => false, 'message' => 'Lỗi bảo mật CSRF'], 403);
            return;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);

        if (!$productId) {
            $this->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.'], 400);
            return;
        }

        $product = $this->productModel->getById($productId);
        if (!$product || !$product['is_active']) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.'], 400);
            return;
        }

        $success = $this->cartModel->updateQuantity($_SESSION['user_id'], $productId, $quantity);
        $total = $this->cartModel->getTotal($_SESSION['user_id']);
        $count = $this->cartModel->countItems($_SESSION['user_id']);
        $_SESSION['cart_count'] = $count;

        $this->json([
            'success' => $success,
            'cart_total' => number_format($total, 0, ',', '.') . ' đ',
            'cart_count' => $count,
        ]);
    }

    /**
     * Xóa SP khỏi giỏ hàng (AJAX hoặc link)
     * URL: ?route=cart_remove  [POST: product_id]
     */
    public function remove()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage('cart', 'error', 'Phương thức không được phép.');
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
            $this->redirectWithMessage('cart', 'error', 'Lỗi bảo mật CSRF.');
            return;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);

        if (!$productId) {
            $this->redirectWithMessage('cart', 'error', 'Không tìm thấy sản phẩm.');
            return;
        }

        $success = $this->cartModel->removeItem($_SESSION['user_id'], $productId);
        $count = $this->cartModel->countItems($_SESSION['user_id']);
        $_SESSION['cart_count'] = $count;

        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            $this->json(['success' => $success, 'cart_count' => $count]);
        }

        $this->redirectWithMessage('cart', 'success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}
