<?php
require_once __DIR__ . '/BaseController.php';

class ProfileController extends BaseController
{
    private $userModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->userModel = new User($pdo);
    }

    /**
     * Hiển thị trang Profile cá nhân
     */
    public function index()
    {
        $this->requireLogin();

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->findById($userId);

        if (!$user) {
            $this->redirectWithMessage('home', 'error', 'Không tìm thấy người dùng!');
            return;
        }

        // Nếu là admin, render view admin profile
        if ($_SESSION['role'] === 'admin') {
            $this->render('admin/profile/index', compact('user'), 'Hồ sơ Admin - SportZone');
        } else {
            $this->render('users/profile', compact('user'), 'Hồ sơ cá nhân - SportZone');
        }
    }

    /**
     * Cập nhật thông tin Profile
     */
    public function update()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('profile');
            return;
        }

        $userId = $_SESSION['user_id'];
        $fullName = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // Validate cơ bản
        if (empty($username) || empty($email)) {
            $this->redirectWithMessage('profile', 'error', 'Vui lòng điền đầy đủ Tên đăng nhập và Email.');
            return;
        }

        // Lấy thông tin user hiện tại để kiểm tra password (nếu muốn đổi pass)
        $user = $this->userModel->findById($userId);
        // Lưu ý: findById của mình chưa lấy password_hash, mình cần getUserFull để bảo mật hơn
        // Nhưng tạm thời ta cho phép cập nhật các thông tin khác, đổi pass tính sau hoặc thêm method
        
        // Cập nhật thông tin
        // Ở đây ta có thể mở rộng User Model method updateProfile công khai hơn
        // Tạm thời dùng method update có sẵn của model nhưng điều chỉnh cho profile
        
        // TODO: Xử lý upload ảnh đại diện (avatar) nếu có
        // ✅ Xử lý upload ảnh đại diện (avatar) nếu có
        $avatarPath = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            // 1. Kiểm tra dung lượng
            if ($_FILES['avatar']['size'] > $maxSize) {
                $this->redirectWithMessage('profile', 'error', 'Ảnh đại diện không được vượt quá 2MB.');
                return;
            }

            // 2. Kiểm tra MIME type thực tế (Chống giả mạo extension)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                $this->redirectWithMessage('profile', 'error', 'Định dạng ảnh không hợp lệ! Chỉ chấp nhận JPG, PNG, WEBP, GIF.');
                return;
            }

            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
             
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
            $targetFile = $uploadDir . $fileName;
             
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                $avatarPath = 'uploads/avatars/' . $fileName;
            }
        }

        // Thực hiện cập nhật — Ta cần một method updateProfile riêng trong User Model 
        // để tránh việc admin-only fields như role bị thay đổi bừa bãi.
        // Tôi sẽ bổ sung method này vào User.php sau.
        
        try {
            // Thực hiện cập nhật
            $this->userModel->updateProfile($userId, $username, $email, $avatarPath, $fullName);
            
            // Cập nhật lại session
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            if ($avatarPath) {
                $_SESSION['avatar'] = $avatarPath;
            }

            $this->redirectWithMessage('profile', 'success', 'Cập nhật hồ sơ thành công!');
        } catch (Exception $e) {
            $this->redirectWithMessage('profile', 'error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị trang đổi mật khẩu
     */
    public function showChangePassword()
    {
        $this->requireLogin();
        
        if ($_SESSION['role'] === 'admin') {
            $this->render('admin/auth/change_password', [], 'Đổi mật khẩu Admin - SportZone');
        } else {
            $this->render('auth/change_password', [], 'Đổi mật khẩu - SportZone');
        }
    }

    /**
     * Xử lý đổi mật khẩu
     */
    public function postChangePassword()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('change_password');
            return;
        }

        $userId = $_SESSION['user_id'];
        $currentPwd = $_POST['current_password'] ?? '';
        $newPwd = $_POST['new_password'] ?? '';
        $confirmPwd = $_POST['confirm_password'] ?? '';

        if (empty($currentPwd) || empty($newPwd)) {
            $this->redirectWithMessage('change_password', 'error', 'Vui lòng nhập đầy đủ thông tin.');
            return;
        }

        if ($newPwd !== $confirmPwd) {
            $this->redirectWithMessage('change_password', 'error', 'Mật khẩu mới không khớp.');
            return;
        }

        // ✅ Kiểm tra mật khẩu mạnh cho mật khẩu mới
        $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
        if (!preg_match($passwordRegex, $newPwd)) {
            $this->redirectWithMessage('change_password', 'error', 'Mật khẩu mới quá yếu! Cần tối thiểu 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.');
            return;
        }

        // Kiểm tra mật khẩu cũ
        $user = $this->userModel->getFullUserById($userId);
        if (!$user || !password_verify($currentPwd, $user['password_hash'])) {
            $this->redirectWithMessage('change_password', 'error', 'Mật khẩu hiện tại không chính xác.');
            return;
        }

        // Cập nhật mật khẩu mới
        if ($this->userModel->updatePassword($userId, $newPwd)) {
            $this->redirectWithMessage('change_password', 'success', 'Đã đổi mật khẩu thành công!');
        } else {
            $this->redirectWithMessage('change_password', 'error', 'Có lỗi xảy ra, vui lòng thử lại.');
        }
    }
}
