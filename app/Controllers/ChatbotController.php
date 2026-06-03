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

  private $geminiApiKey;
  private $geminiModel;
  private $geminiUrl;

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

    $this->geminiApiKey = getenv('GEMINI_API_KEY');
    $this->geminiModel = getenv('GEMINI_MODEL') ?: 'gemini-2.5-flash-preview';
    $this->geminiUrl = "https://gemini.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key={$this->geminiApiKey}";
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
      'chatHistory' => $this->getHistory(),
      'setting' => $settings
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

  public function askChatbot($userPrompt)
  {
    $this->requireLogin();

    if (!headers_sent()) {
      header('Content-Type: application/json; charset=utf-8');
    }

    try {
      $inputData = json_decode(file_get_contents('php://input'), true);
      $currentUserPrompt = isset($inputData['prompt']) ? trim($inputData['prompt']) : $userPrompt;

      if (empty($currentUserPrompt)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Câu hỏi không được để trống. Vui lòng nhập câu hỏi của bạn.']);
        return;
      }

      $chatbotId = $this->chatbotModel->getChatSessionId($this->userId, $this->sessionToken);
      $this->chatbotModel->addChatMessage([$chatbotId, $currentUserPrompt, 'user']);

      $input = $this->getInput();

      $developerSystemInstruction = `Bạn là một trợ lý ảo hỗ trợ khách hàng của một cửa hàng thương mại điện tử. Nhiệm vụ của bạn là trả lời các câu hỏi của khách hàng dựa trên dữ liệu có sẵn. Dữ liệu bao gồm thông tin về đơn hàng, sản phẩm, đánh giá, câu hỏi thường gặp, thông tin người dùng, tin tức và từ khóa nhanh.Riêng việc cần dữ liệu hình ảnh để so sánh hoặc tìm hiểu, hãy dùng api của Azure Blob Storage để lấy hình ảnh từ "image_path" ở thông tin mỗi sản phẩm trong mục tất cả sản phẩm. Hãy sử dụng dữ liệu này để cung cấp câu trả lời chính xác và hữu ích cho khách hàng. Nếu không có đủ thông tin để trả lời, hãy yêu cầu khách hàng cung cấp thêm chi tiết hoặc hướng dẫn họ liên hệ với bộ phận hỗ trợ khách hàng để được giúp đỡ thêm.`;

      $contentPayload = [];
      $chatHistory = $this->getHistory();

      foreach ($chatHistory as $message) {
        $contentPayload[] = [
          'role' => $message['role'],
          'content' => $message['content']
        ];
      }

      $groundingContext = "[DỮ LIỆU HỆ THỐNG THỜI GIAN THỰC]\n";
      $groundingContext .= "- Tên khách hàng: " . $input['Thông tin người dùng']['name'] . "\n";
      $groundingContext .= "[CÂU HỎI HIỆN TẠI]: " . $currentUserPrompt . "\n";

      $contentsPayload[] = [
        "role" => "user",
        "parts" => [
          [
            "text" => $groundingContext
          ]
        ]
      ];

      $payload = [
        "systemInstruction" => [
          "parts" => ["text" => $developerSystemInstruction]
        ],
        "contents" => $contentsPayload
      ];

      $urlWithKey = $this->geminiUrl . "?key=" . $this->geminiApiKey;
      $rawResponse = HttpClient::request("POST", $urlWithKey, $payload, [
        "Content-Type" => "application/json"
      ], 30);

      $responseArray = json_decode($rawResponse, true);
      $botReply = $responseArray['candidates'][0]['content']['parts'][0]['text'] ?? 'Trợ lý không thể xử lý câu hỏi này.';

      http_response_code(200);
      echo json_encode(['status' => 'success', 'message' => $botReply]);

      $this->chatbotModel->addChatMessage([$chatbotId, $botReply, 'bot']);

    } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(['status' => 'error', 'message' => 'Đã xảy ra lỗi khi xử lý câu hỏi: ' . $e->getMessage()]);
    }
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