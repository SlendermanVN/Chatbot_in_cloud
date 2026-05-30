<?php

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../Models/Review.php';

class AdminCommentController extends BaseController
{
    private $reviewModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->requireAdmin();
        $this->reviewModel = new Review($pdo);

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function index()
    {
        $comments = $this->reviewModel->getPending();

        $pendingCount = $this->reviewModel->countPending();
        $approvedCount = $this->reviewModel->countApproved();

        $this->render('admin/comments/index', [
            'comments' => $comments,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount
        ], 'Duyệt Bình Luận - Admin');
    }

    public function approve()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin_comments');
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
            $this->redirectWithMessage('admin_comments', 'error', 'Token bảo mật không hợp lệ!');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $this->reviewModel->updateStatus($id, 1);
        }

        $this->redirectWithMessage('admin_comments', 'success', 'Đã duyệt thành công!');
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin_comments');
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
            $this->redirectWithMessage('admin_comments', 'error', 'Token bảo mật không hợp lệ!');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $this->reviewModel->delete($id);
        }

        $this->redirectWithMessage('admin_comments', 'success', 'Đã xóa vĩnh viễn đánh giá!');
    }
}
