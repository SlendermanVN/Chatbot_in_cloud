<?php

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../Models/News.php";

class NewsController extends BaseController
{
    private $newsModel;
    private $setting;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->newsModel = new News($pdo);
        
        // Kế thừa settingModel từ BaseController để lấy cấu hình web
        $rawSettings = $this->settingModel->getAll();
        $this->setting = array_column($rawSettings, 'setting_value', 'setting_key');
    }

    /**
     * HIỂN THỊ DANH SÁCH TIN TỨC (FRONTEND)
     */
    public function index()
    {
        $keyword = $_GET["keyword"] ?? "";

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $newsList = $this->newsModel->getPaginatedNews($keyword, $limit, $offset, true);
        $totalNews = $this->newsModel->countTotalNews($keyword, true);
        $totalPages = ceil($totalNews / $limit);

        // Gắn dữ liệu truyền ra view, bao gồm cả biến 'setting' cho header
        $data = [
            "keyword" => $keyword,
            "newsList" => $newsList,
            "currentPage" => $page,
            "totalPages" => $totalPages,
            "setting" => $this->setting,
            "title" => "Tin tức & Blogs Thể thao"
        ];

        // SỬA LỖI: Dùng render() thay vì require_once để ghép Header/Footer
        $this->render('frontend/news/index', $data);
    }

    /**
     * HIỂN THỊ CHI TIẾT BÀI VIẾT (FRONTEND)
     */
    public function detail($idOrSlug = null)
    {
        if (empty($idOrSlug)) {
            $this->redirect('news');
        }

        $newsItem = null;
        if (is_numeric($idOrSlug)) {
            $newsItem = $this->newsModel->getById($idOrSlug);
        } else {
            $newsItem = $this->newsModel->findBySlug($idOrSlug);
        }
        
        if (!$newsItem || $newsItem['is_published'] == 0) {
            $this->redirectWithMessage('news', 'error', 'Bài viết không tồn tại hoặc đã bị ẩn.');
        }

        $id = $newsItem['id'];

        require_once __DIR__ . "/../Models/Review.php";
        $reviewModel = new Review($this->pdo);
        $comments = $reviewModel->getByArticleId($id);

        $data = [
            'setting' => $this->setting,
            'news' => $newsItem,
            'comments' => $comments,
            'title' => $newsItem['title']
        ];

        // Render ra view chi tiết
        $this->render('frontend/news/detail', $data);
    }

    public function storeComment()
    {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $articleId = (int)($_POST['article_id'] ?? 0);
            $content = trim($_POST['content'] ?? '');
            
            if (!$articleId || empty($content)) {
                $this->redirectWithMessage('news_detail&id=' . $articleId, 'error', 'Vui lòng nhập nội dung bình luận.');
                return;
            }

            require_once __DIR__ . "/../Models/Review.php";
            $reviewModel = new Review($this->pdo);
            $reviewModel->addArticleComment($_SESSION['user_id'], $articleId, $content);
            
            $this->redirectWithMessage('news_detail&id=' . $articleId, 'success', 'Bình luận của bạn đã được gửi và đang chờ duyệt.');
        }
    }

    // ==========================================================
    // KHU VỰC ADMIN (CHỈ ADMIN MỚI ĐƯỢC VÀO)
    // ==========================================================

    public function create()
    {
        $this->requireAdmin(); // Chặn người dùng thường

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = htmlspecialchars($_POST['title']);
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            $content = $_POST['content'];
            $meta_desc = htmlspecialchars($_POST['meta_desc']);
            $status = isset($_POST['status']) ? 1 : 0;

            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = time() . '_' . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image);
            }

            $this->newsModel->create($title, $slug, $content, $image, $status, $meta_desc);
            
            // Dùng hàm redirect chuẩn thay vì header thủ công
            $this->redirectWithMessage('admin_news', 'success', 'Thêm bài viết thành công!');
        } else {
            $this->render('admin/news/create', ['setting' => $this->setting]);
        }
    }

    public function edit($id)
    {
        $this->requireAdmin(); // Chặn người dùng thường

        $newsItem = $this->newsModel->getById($id);
        if (!$newsItem) {
            $this->redirectWithMessage('admin_news', 'error', 'Bài viết không tồn tại!');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = htmlspecialchars($_POST['title']);
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            $content = $_POST['content'];
            $meta_desc = htmlspecialchars($_POST['meta_desc']);
            $status = isset($_POST['status']) ? 1 : 0;

            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = time() . '_' . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image);
            }

            $this->newsModel->update($id, $title, $slug, $content, $image, $status, $meta_desc);
            
            $this->redirectWithMessage('admin_news', 'success', 'Cập nhật thành công!');
        } else {
            $this->render('admin/news/edit', ['news' => $newsItem, 'setting' => $this->setting]);
        }
    }

    public function delete($id)
    {
        $this->requireAdmin(); // Chặn người dùng thường

        $this->newsModel->delete($id);
        $this->redirectWithMessage('admin_news', 'success', 'Xóa bài viết thành công!');
    }
}