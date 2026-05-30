<?php
require_once __DIR__ . '/../Models/Faq.php';

class FaqController extends BaseController
{
    private $faqModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->faqModel = new Faq($pdo);
    }

    // =========================================================
    // FRONT-END
    // =========================================================

    public function index()
    {
        $search     = trim($_GET['search'] ?? '');
        $grouped    = $this->faqModel->getAllGrouped($search);
        $categories = $this->faqModel->getCategories();

        $this->render('faqs/index', compact('grouped', 'search', 'categories'));
    }

    /**
     * Nhận câu hỏi gửi bởi người dùng front-end
     */
    public function submitQuestion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('faqs');
            return;
        }

        // Lấy dữ liệu
        $name     = trim($_POST['name']     ?? ($_SESSION['username'] ?? ''));
        $email    = trim($_POST['email']    ?? ($_SESSION['email'] ?? ''));
        $question = trim($_POST['question'] ?? '');

        // Validate
        $errors = [];
        if (strlen($name) < 2)  $errors[] = 'Vui lòng nhập tên (ít nhất 2 ký tự).';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
        if (strlen($question) < 5) $errors[] = 'Câu hỏi quá ngắn (ít nhất 5 ký tự).';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = $_POST;
            $this->redirect('faqs');
            return;
        }

        // Gọi hàm Model để insert dữ liệu xuống DB
        $this->faqModel->insertPendingQuestion($name, $email, $question);

        // Trả về thông báo thành công theo đúng yêu cầu nghiệp vụ
        $this->redirectWithMessage('faqs', 'success', 'Câu hỏi của bạn đã được gửi. Đội ngũ admin sẽ trả lời và đăng lên trang trong thời gian sớm nhất!');
    }

    // =========================================================
    // ADMIN
    // =========================================================

    public function adminIndex()
    {
        $this->requireAdmin();
        $search = trim($_GET['search'] ?? '');
        $faqs   = $this->faqModel->getAllForAdmin($search);

        $this->render('admin/faqs/index', compact('faqs', 'search'), 'Quản lý FAQ - Admin');
    }

    public function create()
    {
        $this->requireAdmin();
        $categories = $this->faqModel->getCategories();
        $faq        = null;

        $this->render('admin/faqs/create', compact('categories', 'faq'), 'Thêm FAQ - Admin');
    }

    public function store()
    {
        $this->requireAdmin();

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $question   = trim($_POST['question']   ?? '');
        $answer     = trim($_POST['answer']     ?? '');
        $sortOrder  = (int) ($_POST['sort_order'] ?? 0);
        $isActive   = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

        $errors = [];
        if (!$categoryId)             $errors[] = 'Vui lòng chọn danh mục.';
        if (strlen($question) < 3)    $errors[] = 'Câu hỏi quá ngắn (ít nhất 3 ký tự).';
        if (strlen($question) > 500)  $errors[] = 'Câu hỏi tối đa 500 ký tự.';
        if (strlen($answer) < 5)      $errors[] = 'Câu trả lời quá ngắn (ít nhất 5 ký tự).';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = $_POST;
            $this->redirect('admin_faq_create');
            return;
        }

        $this->faqModel->create($categoryId, $question, $answer, $sortOrder, $isActive);
        $this->redirectWithMessage('admin_faqs', 'success', 'Thêm FAQ thành công!');
    }

    public function edit(int $id)
    {
        $this->requireAdmin();

        $faq = $this->faqModel->findById($id);
        if (!$faq) {
            $this->redirect('admin_faqs');
            return;
        }

        $categories = $this->faqModel->getCategories();
        $this->render('admin/faqs/create', compact('categories', 'faq'), 'Sửa FAQ - Admin');
    }

    public function update(int $id)
    {
        $this->requireAdmin();

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $question   = trim($_POST['question']   ?? '');
        $answer     = trim($_POST['answer']     ?? '');
        $sortOrder  = (int) ($_POST['sort_order'] ?? 0);
        $isActive   = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

        if ($categoryId && strlen($question) >= 3 && strlen($answer) >= 5) {
            $this->faqModel->update($id, $categoryId, $question, $answer, $sortOrder, $isActive);
        }

        $this->redirectWithMessage('admin_faqs', 'success', 'Cập nhật FAQ thành công!');
    }

    public function delete(int $id)
    {
        $this->requireAdmin();
        $this->faqModel->delete($id);
        $this->redirectWithMessage('admin_faqs', 'success', 'Đã xóa FAQ.');
    }
}
