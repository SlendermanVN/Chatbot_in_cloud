<?php
require_once __DIR__ . "/../Models/Setting.php";

class BaseController
{
    protected $pdo;
    protected $settingModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->settingModel = new Setting($pdo);
    }

    /**
     * Render view kèm header/footer
     * AUTO-DETECT LAYOUT: Nếu $view bắt đầu bằng 'admin/' → Admin Layout
     *                     Ngược lại → Frontend Layout (templates/)
     *
     * @param string $view   Đường dẫn view, vd: 'admin/dashboard/index' hoặc 'products/index'
     * @param array  $data   Dữ liệu truyền vào view (extract thành biến)
     * @param string $title  Tiêu đề trang (được extract thành $title trong view)
     */
    protected function render($view, $data = [], $title = '')
    {
        // Extract array thành biến cục bộ để view dùng trực tiếp
        // vd: $data['products'] => $products trong view
        extract($data);

        // ✅ AUTO-DETECT LAYOUT dựa trên prefix của $view
        if (str_starts_with($view, 'admin/')) {
            // Khu vực Admin: Sidebar + Topbar riêng biệt, KHÔNG có giỏ hàng Frontend
            $headerPath = __DIR__ . '/../Views/admin/layouts/header.php';
            $footerPath = __DIR__ . '/../Views/admin/layouts/footer.php';
        } else {
            // Khu vực Frontend: load settings để footer/header dùng được
            $rawFooterSettings = $this->settingModel->getAll();
            $footerSettings = array_column($rawFooterSettings, 'setting_value', 'setting_key');

            $headerPath = __DIR__ . '/../../templates/header.php';
            $footerPath = __DIR__ . '/../../templates/footer.php';
        }

        require_once $headerPath;
        require_once __DIR__ . '/../Views/' . $view . '.php';
        require_once $footerPath;
    }

    /**
     * Render view KHÔNG bọc header/footer (dùng cho landing page, trang standalone)
     * View tự chịu toàn bộ HTML từ <!DOCTYPE> đến </html>
     */
    protected function renderRaw($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }

    /**
     * Redirect đến route khác
     */
    protected function redirect($route)
    {
        header("Location: " . BASE_URL . "/index.php?route={$route}");
        exit;
    }

    /**
     * Redirect với message (flash message dùng Session)
     */
    protected function redirectWithMessage($route, $type, $message)
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        $this->redirect($route);
    }

    /**
     * Kiểm tra user đã đăng nhập chưa, nếu chưa redirect về login
     */
    protected function requireLogin()
    {
        // 1. Kiểm tra session cơ bản
        if (!isset($_SESSION['user_id'])) {
            // Lưu lại URL hiện tại (bao gồm cả query string) để sau khi login quay lại đúng trang
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            $this->respondUnauthorized('Vui lòng đăng nhập để sử dụng tính năng này.');
        }
        // 2. LOGIC FORCE LOGOUT (Bắt buộc người dùng bị khóa phải văng ra ngoài)
        require_once __DIR__ . '/../Models/User.php';
        $userModel = new User($this->pdo);
        $currentUser = $userModel->getFullUserById($_SESSION['user_id']);
        if (!$currentUser || $currentUser['is_banned'] == 1) {
            session_destroy();
            session_start();
            $this->respondUnauthorized('Tài khoản của bạn đã bị khóa hoặc không tồn tại!');
        }
    }
    /**
     * Hàm phụ trợ xử lý báo lỗi (Hỗ trợ cả AJAX và Redirect thường)
     */
    private function respondUnauthorized($message)
    {
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($isAjax) {
            $this->json([
                'success' => false,
                'message' => $message,
                'require_login' => true
            ], 401);
        }

        $this->redirectWithMessage('login', 'danger', $message);
        exit;
    }

    /**
     * Kiểm tra user có phải admin không, nếu không redirect về home
     */
    protected function requireAdmin()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Bạn không có quyền truy cập khu vực quản trị.'];
            header('Location: ' . BASE_URL . '/index.php?route=login');
            exit;
        }
    }

    /**
     * Trả về JSON (dùng cho AJAX)
     */
    protected function json($data, $statusCode = 200)
    {
        // Xóa sạch output buffer để chống lỗi BOM / Khoảng trắng
        if (ob_get_length()) {
            ob_clean();
        }
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Xác thực CSRF Token
     */
    protected function verifyCsrf()
    {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'CSRF token mismatch.'], 403);
            }
            die("CSRF Token không hợp lệ. Vui lòng quay lại và tải lại trang.");
        }
    }
}
