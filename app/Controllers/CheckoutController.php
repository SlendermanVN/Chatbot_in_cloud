<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Cart.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Product.php';

class CheckoutController extends BaseController
{
    private $cartModel;
    private $orderModel;
    private $userModel;
    private $productModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->cartModel = new Cart($pdo);
        $this->orderModel = new Order($pdo);
        $this->userModel = new User($pdo);
        $this->productModel = new Product($pdo);
    }

    /**
     * Hiển thị form thanh toán
     * URL: ?route=checkout
     */
    public function index()
    {
        $this->requireLogin();

        $userId = $_SESSION['user_id'];
        $items = $this->cartModel->getByUser($userId);

        // Giỏ trống → về trang giỏ hàng
        if (empty($items)) {
            $this->redirectWithMessage('cart', 'warning', 'Giỏ hàng của bạn đang trống.');
            return;
        }

        $total = $this->cartModel->getTotal($userId);

        // Lấy thông tin user để điền sẵn vào form
        $userInfo = $this->userModel->findById($userId);

        $this->render('checkout/index', compact('items', 'total', 'userInfo'), 'Thanh toán - SportZone');
    }

    /**
     * Xử lý submit form checkout → tạo đơn hàng
     * URL: ?route=checkout_process  [POST]
     */
    public function process()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('checkout');
            return;
        }

        // Kiểm tra CSRF Token
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $this->redirectWithMessage('checkout', 'error', 'Token bảo mật không hợp lệ. Vui lòng thử lại.');
            return;
        }

        $userId = $_SESSION['user_id'];
        $items = $this->cartModel->getByUser($userId);

        if (empty($items)) {
            $this->redirectWithMessage('cart', 'warning', 'Giỏ hàng của bạn đang trống.');
            return;
        }

        // ---- Validate input ----
        $recipientName = trim($_POST['recipient_name'] ?? '');
        $recipientPhone = trim($_POST['recipient_phone'] ?? '');
        $shippingAddr = trim($_POST['shipping_address'] ?? '');
        $note = trim($_POST['note'] ?? '');

        $errors = [];
        if (empty($recipientName))
            $errors[] = 'Vui lòng nhập tên người nhận.';
        if (!preg_match('/^[0-9]{9,11}$/', $recipientPhone)) {
            $errors[] = 'Số điện thoại không hợp lệ (9-11 chữ số).';
        }
        if (empty($shippingAddr))
            $errors[] = 'Vui lòng nhập địa chỉ giao hàng.';

        if ($errors) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => implode('<br>', $errors)];
            $this->redirect('checkout');
            return;
        }

        // ---- Kiểm tra tồn kho lần cuối trước khi tạo đơn ----
        foreach ($items as $item) {
            $product = $this->productModel->getById($item['product_id']);
            $stock = $product ? (int) $product['stock'] : 0;
            if ($stock < $item['quantity']) {
                $this->redirectWithMessage(
                    'cart',
                    'error',
                    "Sản phẩm \"{$item['product_name']}\" không đủ tồn kho. Vui lòng cập nhật giỏ hàng."
                );
                return;
            }
        }

        // ---- Tạo đơn hàng qua OrderModel ----
        try {
            $total = $this->cartModel->getTotal($userId);

            // Tạo customer_order và order_items, đồng thời xóa giỏ hàng
            $orderId = $this->orderModel->createOrder(
                $userId,
                htmlspecialchars($recipientName),
                htmlspecialchars($recipientPhone),
                htmlspecialchars($shippingAddr),
                htmlspecialchars($note),
                $total,
                $items,
                $this->cartModel
            );

            // Reset giỏ hàng trong session
            $_SESSION['cart_count'] = 0;

            $this->redirectWithMessage(
                'order_success&id=' . $orderId,
                'success',
                'Đặt hàng thành công! Mã đơn hàng của bạn là #' . $orderId
            );

        } catch (Exception $e) {
            error_log('Checkout error: ' . $e->getMessage());
            $this->redirectWithMessage('checkout', 'error', 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.');
        }
    }
}
