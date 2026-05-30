<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Order.php';

class OrderController extends BaseController
{
    private $orderModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->orderModel = new Order($pdo);
    }

    /**
     * Admin: danh sách tất cả đơn hàng (có lọc theo trạng thái + phân trang)
     * URL: ?route=admin_orders&status=pending&page=1
     */
    //TODO Văn Phát: Hiển thị danh sách đơn hàng cho admin, có thể lọc theo trạng thái và phân trang
    public function index()
    {
        $this->requireAdmin();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;
        $status = $_GET['status'] ?? '';
        $keyword = $_GET['keyword'] ?? '';

        // Lấy danh sách đơn hàng
        $orders = $this->orderModel->getOrders($limit, $offset, $status, $keyword);

        // Đếm tổng (cho phân trang)
        $total = $this->orderModel->countOrders($status, $keyword);
        $totalPages = ceil($total / $limit);

        // Đếm theo từng trạng thái (hiện trên tabs)
        $stats = $this->orderModel->countOrdersByStatus();

        $this->render(
            'admin/orders/index',
            compact('orders', 'total', 'totalPages', 'page', 'status', 'stats'),
            'Quản lý đơn hàng - Admin'
        );
    }

    /**
     * Admin: cập nhật trạng thái đơn hàng
     * URL: ?route=admin_order_status&id=1  [POST: status]
     */
    public function updateStatus($id)
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin_orders');
            return;
        }

        $newStatus = $_POST['status'] ?? '';
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($newStatus, $validStatuses)) {
            $this->redirectWithMessage('admin_orders', 'error', 'Trạng thái không hợp lệ.');
            return;
        }

        // Kiểm tra đơn tồn tại
        $order = $this->orderModel->getOrderById($id);

        if (!$order) {
            $this->redirectWithMessage('admin_orders', 'error', 'Không tìm thấy đơn hàng.');
            return;
        }

        // Không cho phép thay đổi đơn đã delivered hoặc cancelled
        if (in_array($order['status'], ['delivered', 'cancelled'])) {
            $this->redirectWithMessage(
                'admin_orders',
                'warning',
                'Không thể thay đổi trạng thái đơn hàng đã hoàn thành hoặc đã hủy.'
            );
            return;
        }

        // Cập nhật — trigger trg_order_status_after_update sẽ tự xử lý stock
        $this->orderModel->updateStatus($id, $newStatus);

        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
        ];

        $this->redirectWithMessage(
            'admin_orders',
            'success',
            "Đã cập nhật đơn #{$id} sang trạng thái: {$statusLabels[$newStatus]}"
        );
    }

    /**
     * Trang xác nhận đặt hàng thành công (cho user)
     * URL: ?route=order_success&id=1
     */
    public function success()
    {
        $this->requireLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $order = $this->orderModel->getOrderById($id);

        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $this->redirect('home');
            return;
        }

        // Lấy items của đơn
        $orderItems = $this->orderModel->getOrderItems($id);

        $this->render('orders/success', compact('order', 'orderItems'), 'Đặt hàng thành công - SportZone');
    }

    /**
     * Frontend: Xem lịch sử mua hàng cá nhân
     * URL: ?route=order_history
     */
    public function history()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];
        $orders = $this->orderModel->getOrdersByUser($userId);

        $this->render('orders/history', compact('orders'), 'Lịch sử mua hàng - SportZone');
    }

    /**
     * Frontend: Xem chi tiết đơn hàng cá nhân
     * URL: ?route=order_detail&id=1
     */
    public function detail()
    {
        $this->requireLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $order = $this->orderModel->getOrderById($id);

        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $this->redirectWithMessage('order_history', 'error', 'Không tìm thấy đơn hàng hoặc bạn không có quyền xem!');
            return;
        }

        $orderItems = $this->orderModel->getOrderItems($id);

        $this->render('orders/detail', compact('order', 'orderItems'), 'Chi tiết đơn hàng #' . $id . ' - SportZone');
    }
}
