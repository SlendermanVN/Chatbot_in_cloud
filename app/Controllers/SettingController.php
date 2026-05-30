<?php
class SettingController extends BaseController
{
    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->requireAdmin(); // Chặn mọi quyền truy cập không phải admin
        $this->settingModel = new Setting($pdo);
    }

    public function index()
    {
        // TODO Anh Đức: Quản lý site_settings (Chỉnh sửa Tên site, Logo, Footer info...) trong Admin Srtdash.
        $settings = $this->settingModel->getAll();

        $data = [
            'settings' => $settings
        ];

        $this->render('admin/settings/index', $data);
    }

    public function saveAll()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF check
            $token = $_POST['csrf_token'] ?? '';
            if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
                $this->redirectWithMessage('admin_settings', 'error', 'Lỗi xác thực (CSRF). Vui lòng thử lại.');
                return;
            }

            // Các setting không muốn lưu (meta data của form)
            $exclude = ['csrf_token'];
            $successCount = 0;

            foreach ($_POST as $key => $value) {
                if (in_array($key, $exclude)) continue;
                
                // Nếu là checkbox (site_active, allow_backorder) và không gửi lên thì giá trị là 0
                // Nhưng trong PHP POST, checkbox không được gửi nếu không check.
                // Ở đây ta duyệt POST nên ta chỉ xử lý những gì được gửi.
                
                $this->settingModel->set($key, $value);
                $successCount++;
            }

            // Xử lý các checkbox đặc thù nếu không có trong POST
            $checkboxes = ['site_active', 'allow_backorder'];
            foreach ($checkboxes as $cb) {
                if (!isset($_POST[$cb])) {
                    $this->settingModel->set($cb, '0');
                }
            }

            $this->redirectWithMessage('admin_settings', 'success', 'Đã cập nhật các thiết lập hệ thống thành công!');
        }
    }
}
