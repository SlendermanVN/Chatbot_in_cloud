<?php

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../Models/User.php";

class AuthController extends BaseController
{
    private $userModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        
        // Đảm bảo session luôn bật
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->userModel = new User($pdo); // Truyền pdo vào model

        // Khởi tạo CSRF Token nếu chưa có
        if (empty($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Hiển thị form Đăng nhập
     */
    public function login()
    {
        $rawSettings = $this->settingModel->getAll();
        $setting = array_column($rawSettings, 'setting_value', 'setting_key');

        // Sử dụng renderRaw để trang đăng nhập độc lập (giống landing)
        $this->renderRaw('frontend/auth/login', [
            'setting' => $setting
        ]);
    }

    /**
     * Xử lý logic POST Đăng nhập
     */
    public function postLogin()
    {
        $rawSettings = $this->settingModel->getAll();
        $setting = array_column($rawSettings, "setting_value", "setting_key");

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->redirect('login');
            return;
        }

        // 1. Kiểm tra CSRF Token
        $submittedToken = $_POST["csrf_token"] ?? "";
        if (!hash_equals($_SESSION["csrf_token"] ?? '', $submittedToken)) {
            $this->redirectWithMessage('login', 'error', 'Lỗi bảo mật, vui lòng thử lại!');
            return;
        }

        // 2. Validate dữ liệu đầu vào
        $email    = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";

        if (empty($email) || empty($password)) {
            $this->renderRaw('frontend/auth/login', [
                'setting' => $setting,
                'error'   => "Vui lòng nhập đầy đủ Email và Mật khẩu."
            ]);
            return;
        }

        // ✅ Kiểm tra độ dài mật khẩu (tối thiểu 8 ký tự)
        if (strlen($password) < 8) {
            $this->renderRaw('frontend/auth/login', [
                'setting' => $setting,
                'error'   => "Mật khẩu phải có ít nhất 8 ký tự."
            ]);
            return;
        }

        // 3. Kiểm tra user tồn tại & mật khẩu đúng
        // NOTE: User Model tự mapping password_hash -> password, is_banned -> status
        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->renderRaw('frontend/auth/login', [
                'setting' => $setting,
                'error'   => "Email hoặc mật khẩu không chính xác."
            ]);
            return;
        }

        // 4. Kiểm tra tài khoản có bị khóa không
        // status = 0 nghĩa là bị khóa (User Model đã map: is_banned=1 -> status=0)
        if ($user['status'] == 0) {
            $this->render('frontend/auth/login', [
                'setting' => $setting,
                'error'   => "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.",
                'title'   => 'Đăng nhập - SportZone'
            ]);
            return;
        }

        // 5. Lưu thông tin vào Session
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email']    = $user['email'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['avatar']   = $user['avatar'] ?? 'uploads/avatars/default.png';

        // Lấy số lượng giỏ hàng ban đầu
        require_once __DIR__ . "/../Models/Cart.php";
        $cartModel = new Cart($this->pdo);
        $_SESSION['cart_count'] = $cartModel->countItems($user['id']);

        // 6. ✅ PHÂN LUỒNG REDIRECT THEO ROLE
        $targetUrl = "";
        if ($user['role'] === 'admin') {
            $targetUrl = BASE_URL . '/index.php?route=admin_products';
            unset($_SESSION['redirect_url']); // Admin ưu tiên vào trang quản lý
        } else {
            if (!empty($_SESSION['redirect_url'])) {
                $targetUrl = $_SESSION['redirect_url'];
                unset($_SESSION['redirect_url']);
            } else {
                $targetUrl = BASE_URL . '/index.php?route=home';
            }
        }

        $_SESSION['flash'] = [
            'type' => 'success', 
            'message' => 'Chào mừng, ' . htmlspecialchars($user['username']) . '!'
        ];
        header("Location: " . $targetUrl);
        exit;
    }


    /**
     * Hiển thị form Đăng ký
     */
    public function register()
    {
        $rawSettings = $this->settingModel->getAll();
        $setting = array_column($rawSettings, 'setting_value', 'setting_key');

        $this->renderRaw('frontend/auth/register', [
            'setting' => $setting
        ]);
    }

    /**
     * Xử lý logic POST Đăng ký
     */
    public function postRegister()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $submittedToken = $_POST["csrf_token"] ?? "";
            if (!hash_equals($_SESSION["csrf_token"], $submittedToken)) {
                die("Lỗi bảo mật: Token CSRF không hợp lệ!");
            }

            $fullName = trim($_POST["full_name"] ?? "");
            $username = trim($_POST["username"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $password = $_POST["password"] ?? "";
            $passwordConfirmation = $_POST["password_confirmation"] ?? "";

            // Lưu lại dữ liệu cũ để điền lại form nếu lỗi
            $_SESSION['old'] = [
                'full_name' => $fullName,
                'username'  => $username,
                'email'     => $email
            ];

            if (empty($fullName) || empty($username) || empty($email) || empty($password)) {
                $rawSettings = $this->settingModel->getAll();
                $setting = array_column($rawSettings, 'setting_value', 'setting_key');
                $this->renderRaw('frontend/auth/register', [
                    'setting' => $setting,
                    'error' => "Vui lòng nhập đầy đủ các trường thông tin."
                ]);
                return;
            }

            // ✅ Kiểm tra mật khẩu mạnh
            $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
            if (!preg_match($passwordRegex, $password)) {
                $rawSettings = $this->settingModel->getAll();
                $setting = array_column($rawSettings, 'setting_value', 'setting_key');
                $this->renderRaw('frontend/auth/register', [
                    'setting' => $setting,
                    'error' => "Mật khẩu yếu! Cần tối thiểu 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt."
                ]);
                return;
            }

            if ($password !== $passwordConfirmation) {
                $rawSettings = $this->settingModel->getAll();
                $setting = array_column($rawSettings, 'setting_value', 'setting_key');
                
                $this->renderRaw('frontend/auth/register', [
                    'setting' => $setting,
                    'error' => "Mật khẩu xác nhận không khớp!"
                ]);
                return;
            }

            if ($this->userModel->findByEmail($email)) {
                $rawSettings = $this->settingModel->getAll();
                $setting = array_column($rawSettings, 'setting_value', 'setting_key');
                
                $this->renderRaw('frontend/auth/register', [
                    'setting' => $setting,
                    'error' => "Địa chỉ Email này đã được đăng ký!"
                ]);
                return;
            }

            // ✅ Kiểm tra Username đã tồn tại chưa
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            if ($stmt->fetch()) {
                $rawSettings = $this->settingModel->getAll();
                $setting = array_column($rawSettings, 'setting_value', 'setting_key');
                $this->renderRaw('frontend/auth/register', [
                    'setting' => $setting,
                    'error' => "Tên đăng nhập này đã có người sử dụng!"
                ]);
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $userData = [
                "full_name" => $fullName,
                "username"  => $username,
                "email"     => $email,
                "password"  => $hashedPassword
            ];

            if ($this->userModel->createUser($userData)) {
                unset($_SESSION['old']);
                // Dùng hàm redirectWithMessage cực tiện lợi từ BaseController
                $this->redirectWithMessage('login', 'success', 'Đăng ký thành công! Hãy đăng nhập.');
            } else {
                $rawSettings = $this->settingModel->getAll();
                $setting = array_column($rawSettings, 'setting_value', 'setting_key');
                $this->render('frontend/auth/register', [
                    'setting' => $setting,
                    'error' => "Lỗi server, không thể tạo tài khoản!"
                ]);
            }
        }
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        // Chuyển về trang login thông qua router
        $this->redirect('login');
    }
}