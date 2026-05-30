<?php
class UserController extends BaseController
{
    private $userModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->userModel = new User($pdo);
    }

    public function index()
    {
        $this->requireAdmin();
        
        $keyword = isset($_GET['keyword']) ? trim(htmlspecialchars($_GET['keyword'])) : '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $users = $this->userModel->getAll($limit, $offset, $keyword);
        $total = $this->userModel->countTotal($keyword);
        $totalPages = ceil($total / $limit);

        $this->render('admin/users/index', [
            'users' => $users,
            'keyword' => $keyword,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function resetPassword($id)
    {
        $this->requireAdmin();

        $newPassword = bin2hex(random_bytes(4));

        $this->userModel->updatePassword($id, $newPassword);

        $this->redirectWithMessage('users', 'success', "Mật khẩu mới của user #{$id}: <strong>{$newPassword}</strong>");
    }

    public function banUser($id)
    {
        $this->requireAdmin();

        // Kiểm tra bảo mật CSRF
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submittedToken = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
                $this->redirectWithMessage('admin_users', 'error', 'Token bảo mật không hợp lệ.');
                return;
            }

            $this->userModel->setStatus($id, 0); // 0 = Khóa
            $this->redirectWithMessage('admin_users', 'success', "Đã khóa tài khoản #{$id}.");
        } else {
            $this->redirect('admin_users');
        }
    }

    public function unbanUser($id)
    {
        $this->requireAdmin();

        // Kiểm tra bảo mật CSRF
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submittedToken = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
                $this->redirectWithMessage('admin_users', 'error', 'Token bảo mật không hợp lệ.');
                return;
            }
            $this->userModel->setStatus($id, 1); // 1 = Hoạt động
            $this->redirectWithMessage('admin_users', 'success', "Đã mở khóa tài khoản #{$id}.");
        } else {
            $this->redirect('admin_users');
        }
    }
}

