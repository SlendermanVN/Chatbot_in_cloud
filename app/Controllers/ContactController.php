<?php
require_once __DIR__ . '/../Models/Contact.php';

class ContactController extends BaseController
{
    private $contactModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->contactModel = new Contact($pdo);
        $this->settingModel = new Setting($pdo);
    }

    public function index()
    {
        // TODO Anh Đức: Hiển thị form liên hệ (Client).
        $rawSettings = $this->settingModel->getAll();
        $setting = array_column($rawSettings, 'setting_value', 'setting_key');

        $this->render('contact/index', ['setting' => $setting]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $customer_type = trim($_POST['customer_type'] ?? '');
            $company_name = trim($_POST['company_name'] ?? '');
            $method_contact_type = trim($_POST['method_contact_type'] ?? '');
            $contact_value = trim($_POST['contact_value'] ?? '');
            $contact_message = trim($_POST['contact_message'] ?? '');

            // Validate dữ liệu
            if (empty($fullname) || empty($contact_value) || empty($contact_message)) {
                $this->redirectWithMessage('contact', 'error', 'Vui lòng điền đầy đủ Họ Tên, Thông tin liên lạc và Nội dung!');
                return;
            }

            // Chuẩn bị dữ liệu cho DB
            $user_id = $_SESSION['user_id'] ?? null;
            $email = ($method_contact_type === 'email') ? $contact_value : '';
            $phone = ($method_contact_type === 'phone' || $method_contact_type === 'zalo') ? $contact_value : '';
            if (empty($email)) $email = $contact_value; // Fallback nếu chọn zalo/phone nhưng input là email
            
            $customerTypeLabel = [
                'ca_nhan' => 'Cá nhân',
                'doanh_nghiep' => 'Doanh nghiệp',
                'club' => 'CLB/Nhóm thể thao'
            ][$customer_type] ?? 'Khách hàng';

            $subject = "Liên hệ từ " . $customerTypeLabel . (!empty($company_name) ? " - " . $company_name : "");
            
            $fullMessage = "Phương thức muốn liên hệ: " . strtoupper($method_contact_type) . " - " . $contact_value . "\n";
            $fullMessage .= "Nội dung:\n" . $contact_message;

            $response = $this->contactModel->create($user_id, $fullname, $email, $phone, $subject, $fullMessage);

            if ($response) {
                $this->redirectWithMessage('contact', 'success', 'Liên hệ của bạn đã được gửi thành công!');
            } else {
                $this->redirectWithMessage('contact', 'error', 'Có lỗi xảy ra khi gửi liên hệ, vui lòng thử lại sau.');
            }
        }
        
        $this->redirectWithMessage('contact', 'error', 'Yêu cầu không hợp lệ.');
    }

    public function adminIndex()
    {
        $this->requireAdmin();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $contacts = $this->contactModel->getAll($limit, $offset);
        $totalCount = $this->contactModel->countTotal();
        $unreadCount = $this->contactModel->countUnread();
        $totalPages = ceil($totalCount / $limit);

        $this->render('admin/contacts/index', [
            'contacts' => $contacts,
            'totalCount' => $totalCount,
            'unreadCount' => $unreadCount,
            'totalPages' => $totalPages,
            'page' => $page
        ], 'Quản lý liên hệ - Admin');
    }

    public function reply($id)
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submittedToken = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
                $this->redirectWithMessage('admin_contacts', 'error', 'Token bảo mật không hợp lệ.');
                return;
            }

            $replied_message = trim($_POST['replied_message'] ?? '');
            if (empty($replied_message)) {
                $this->redirectWithMessage('admin_contacts', 'error', 'Vui lòng nhập nội dung phản hồi.');
                return;
            }

            $response = $this->contactModel->reply($id, $replied_message);
            if ($response) {
                $this->redirectWithMessage('admin_contacts', 'success', 'Đã gửi phản hồi thành công!');
            } else {
                $this->redirectWithMessage('admin_contacts', 'error', 'Có lỗi xảy ra khi gửi phản hồi.');
            }
        } else {
            $this->redirect('admin_contacts');
        }
    }

    public function markAsRead($id)
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submittedToken = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
                $this->redirectWithMessage('admin_contacts', 'error', 'Token bảo mật không hợp lệ.');
                return;
            }
        }
        $this->contactModel->updateStatus($id, 'read');
        $this->redirectWithMessage('admin_contacts', 'success', 'Đã đánh dấu đã đọc.');
    }

    public function delete($id)
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submittedToken = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
                $this->redirectWithMessage('admin_contacts', 'error', 'Token bảo mật không hợp lệ.');
                return;
            }
        }
        $response = $this->contactModel->getById($id);
        if ($response) {
            $this->contactModel->delete($id);
            $this->redirectWithMessage('admin_contacts', 'success', 'Đã xóa liên hệ thành công.');
        } else {
            $this->redirectWithMessage('admin_contacts', 'error', 'Liên hệ không tồn tại.');
        }
    }
}
