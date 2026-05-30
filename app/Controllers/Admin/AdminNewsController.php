<?php

require_once __DIR__ . "/../BaseController.php";
require_once __DIR__ . "/../../Models/News.php";

class AdminNewsController extends BaseController
{
    private $newsModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->requireAdmin();
        $this->newsModel = new News($pdo);

        if (empty($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }
    }

    public function index()
    {
        $keyword = $_GET['keyword'] ?? '';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $news = $this->newsModel->getPaginatedNews($keyword, $limit, $offset);
        $totalNews = $this->newsModel->countTotalNews($keyword);
        $totalPages = max(1, ceil($totalNews / $limit));

        $this->render('admin/news/index', [
            'news' => $news,
            'keyword' => $keyword,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ], 'Quản lý Tin tức - Admin');
    }

    private function createSlug($string)
    {
        $search = [
            "#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#",
            "#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#",
            "#(ì|í|ị|ỉ|ĩ)#",
            "#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#",
            "#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#",
            "#(ỳ|ý|ỵ|ỷ|ỹ)#",
            "#(đ)#",
            "#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#",
            "#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#",
            "#(Ì|Í|Ị|Ỉ|Ĩ)#",
            "#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#",
            "#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#",
            "#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#",
            "#(Đ)#",
            "/[^a-zA-Z0-9\-\_]/"
        ];
        $replace = ["a", "e", "i", "o", "u", "y", "d", "A", "E", "I", "O", "U", "Y", "D", "-"];
        $string = preg_replace($search, $replace, $string);
        $string = preg_replace("/(-)+/", "-", $string);
        return strtolower($string);
    }

    public function create()
    {
        $this->render('admin/news/create', [], 'Thêm Tin Tức - Admin');
    }

    public function store()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->redirect('admin_news');
            return;
        }

        $submittedToken = $_POST["csrf_token"] ?? "";
        if (!hash_equals($_SESSION["csrf_token"] ?? '', $submittedToken)) {
            $this->redirectWithMessage('admin_news', 'error', 'Token bảo mật không hợp lệ!');
            return;
        }

        $title = trim($_POST["title"] ?? "");
        $slug = trim($_POST["slug"] ?? "");
        $content = $_POST["content"] ?? "";
        $metaDesc = htmlspecialchars(trim($_POST["meta_description"] ?? ""));
        $metaKeywords = htmlspecialchars(trim($_POST["meta_keywords"] ?? ""));
        $status = (isset($_POST["status"]) && $_POST["status"] === 'published') ? 1 : 0;

        if (empty($slug)) {
            $slug = $this->createSlug($title);
        }

        // Xử lý upload ảnh
        $thumbnailPath = "";
        if (isset($_FILES["thumbnail"]) && $_FILES["thumbnail"]["error"] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . "/../../../public/uploads/";
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);

            $fileExtension = strtolower(pathinfo($_FILES["thumbnail"]["name"], PATHINFO_EXTENSION));
            $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];

            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = time() . "_" . uniqid() . "." . $fileExtension;
                if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $uploadDir . $newFileName)) {
                    $thumbnailPath = "uploads/" . $newFileName;
                }
            }
        }

        $this->newsModel->create($title, $slug, $content, $thumbnailPath, $status, $metaDesc, $metaKeywords);
        $this->redirectWithMessage('admin_news', 'success', 'Thêm bài viết thành công!');
    }

    public function edit()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $newsItem = $this->newsModel->getById($id);
        if (!$newsItem) {
            $this->redirectWithMessage('admin_news', 'error', 'Bài viết không tồn tại!');
            return;
        }
        $this->render('admin/news/edit', ['newsItem' => $newsItem], 'Sửa Tin Tức - Admin');
    }

    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->redirect('admin_news');
            return;
        }

        $submittedToken = $_POST["csrf_token"] ?? "";
        if (!hash_equals($_SESSION["csrf_token"] ?? '', $submittedToken)) {
            $this->redirectWithMessage('admin_news', 'error', 'Token bảo mật không hợp lệ!');
            return;
        }

        $id = (int) ($_POST["id"] ?? 0);
        $title = trim($_POST["title"] ?? "");
        $slug = trim($_POST["slug"] ?? "");
        $content = $_POST["content"] ?? "";
        $metaDesc = htmlspecialchars(trim($_POST["meta_description"] ?? ""));
        $metaKeywords = htmlspecialchars(trim($_POST["meta_keywords"] ?? ""));
        $status = (isset($_POST["status"]) && $_POST["status"] === 'published') ? 1 : 0;

        if (empty($slug)) {
            $slug = $this->createSlug($title);
        }

        $existingNews = $this->newsModel->getById($id);
        if (!$existingNews) {
            $this->redirectWithMessage('admin_news', 'error', 'Bài viết không tồn tại!');
            return;
        }

        $thumbnailPath = $existingNews["thumbnail"];
        if (isset($_FILES["thumbnail"]) && $_FILES["thumbnail"]["error"] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . "/../../../public/uploads/";
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);

            $fileExtension = strtolower(pathinfo($_FILES["thumbnail"]["name"], PATHINFO_EXTENSION));
            $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];

            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = time() . "_" . uniqid() . "." . $fileExtension;
                if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $uploadDir . $newFileName)) {
                    // Xóa ảnh cũ
                    if (!empty($existingNews["thumbnail"])) {
                        $oldPath = __DIR__ . "/../../../public/" . $existingNews["thumbnail"];
                        if (file_exists($oldPath))
                            unlink($oldPath);
                    }
                    $thumbnailPath = "uploads/" . $newFileName;
                }
            }
        }

        $this->newsModel->update($id, $title, $slug, $content, $thumbnailPath, $status, $metaDesc, $metaKeywords);
        $this->redirectWithMessage('admin_news', 'success', 'Cập nhật bài viết thành công!');
    }

    public function delete()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->redirect('admin_news');
            return;
        }

        $submittedToken = $_POST["csrf_token"] ?? "";
        if (!hash_equals($_SESSION["csrf_token"] ?? '', $submittedToken)) {
            $this->redirectWithMessage('admin_news', 'error', 'Token bảo mật không hợp lệ!');
            return;
        }

        $id = (int) ($_POST["id"] ?? 0);
        $existingNews = $this->newsModel->getById($id);

        if ($existingNews) {
            if (!empty($existingNews["thumbnail"])) {
                $oldPath = __DIR__ . "/../../../public/" . $existingNews["thumbnail"];
                if (file_exists($oldPath))
                    unlink($oldPath);
            }
            $this->newsModel->delete($id);
        }

        $this->redirectWithMessage('admin_news', 'success', 'Xóa bài viết thành công!');
    }
}
