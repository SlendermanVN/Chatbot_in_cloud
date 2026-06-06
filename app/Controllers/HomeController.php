<?php
require_once __DIR__ . '/../app/Models/Product.php';
require_once __DIR__ . '/../app/Models/News.php';

require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController
{
    private $productModel;
    private $newsModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->productModel = new Product($pdo);
        $this->newsModel = new News($pdo);
        $this->settingModel = new Setting($pdo);
    }

    public function index()
    {
        // 1. Admin → Admin Dashboard
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $this->redirect('admin_dashboard');
        }

        // 2. Lấy dữ liệu chung cho tất cả mọi người (Guest & Member)
        $rawSettings = $this->settingModel->getAll();
        $settings = array_column($rawSettings, 'setting_value', 'setting_key');
        $topCategories = $this->productModel->getTopCategories(4);
        $latestProducts = $this->productModel->getAll(8, 0);
        $latestNews = $this->newsModel->getAll(3, 0, true);

        $data = [
            'setting' => $settings,
            'topCategories' => $topCategories,
            'latestProducts' => $latestProducts,
            'latestNews' => $latestNews,
        ];

        // 3. Render trang chủ chung (Header sẽ tự xử lý hiển thị Guest/Member)
        $this->render('home/index', $data, 'SportZone Vietnam - High Performance');
    }

    public function loadMoreProducts()
    {
        $limit = 5;
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

        $products = $this->productModel->getAll($limit, $offset);

        header('Content-Type: application/json');
        echo json_encode($products);
        exit();
    }
}
