<?php
require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../Models/Order.php";
require_once __DIR__ . "/../Models/Product.php";
require_once __DIR__ . "/../Models/Review.php";
require_once __DIR__ . "/../Models/Setting.php";
require_once __DIR__ . "/../Models/Faq.php";
require_once __DIR__ . "/../Models/Chatbot.php";
require_once __DIR__ . "/../Models/News.php";

require_once __DIR__ . "/../Classes/HttpClient.php";

header('Content-Type: application/json; charset=utf-8');

class ChatbotController extends BaseController
{
  // Models
  private $chatbotModel;
  private $orderModel;
  private $productModel;
  private $reviewModel;
  private $userModel;
  private $faqModel;
  private $newsModel;

  private $userId;
  private $sessionToken;

  public function __construct($pdo, $pdo2)
  {
    parent::__construct($pdo);
    $this->orderModel = new Order($pdo);
    $this->productModel = new Product($pdo);
    $this->reviewModel = new Review($pdo);
    $this->userModel = new User($pdo);
    $this->faqModel = new Faq($pdo);
    $this->chatbotModel = new Chatbot($pdo2);
    $this->newsModel = new News($pdo);

    $this->userId = $_SESSION['user_id'] ?? null;
    $this->sessionToken = $_SESSION['chat_session_token'] ?? null;
  }

  public function index()
  {
    $this->requireLogin();
    if (!$this->sessionToken) {
      $this->createSession();
    }

    $rawSettings = $this->settingModel->getAll();
    $settings = array_column($rawSettings, 'setting_value', 'setting_key');

    $this->render("chatbot/index", [
      'inputData' => $this->getInput(),
      'settings' => $settings
    ]);
  }

  public function getInput()
  {
    return [
      'Đơn hàng của người dùng' => $this->orderModel->getOrdersByUser($this->userId),
      'Tất cả sản phẩm' => $this->productModel->getAll(),
      'Bảng đánh giá của người dùng' => $this->reviewModel->getPending(),
      'Câu hỏi thường gặp' => $this->faqModel->getAllGrouped(),
      'Thông tin người dùng' => $this->userModel->findById($this->userId),
      'Tin tức' => $this->newsModel->getAll(),
      'Từ khóa nhanh' => $this->getKnowledgeBase(),
      'Lịch sử trò chuyện' => $this->getHistory()
    ];
  }

  public function getHistory()
  {
    return $this->chatbotModel->getChatHistory($this->userId, $this->sessionToken);
  }

  public function createSession()
  {
    if (!$this->sessionToken) {
      $this->sessionToken = $this->chatbotModel->createChatSession($this->userId);
      $_SESSION['chat_session_token'] = $this->sessionToken;
    }
  }

  public function getKnowledgeBase()
  {
    return $this->chatbotModel->getKnowledgeBase();
  }
}