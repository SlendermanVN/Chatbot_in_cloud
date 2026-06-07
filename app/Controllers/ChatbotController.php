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
    $this->geminiModel = getenv('GEMINI_MODEL') ?: 'gemini-2.5-flash-lite-preview';
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

  public function askChatbot()
  {
    $this->requireLogin();

    if (!headers_sent()) {
      header('Content-Type: application/json; charset=utf-8');
    }

    try {
      $inputData = json_decode(file_get_contents('php://input'), true);
      $currentUserPrompt = isset($inputData['prompt']) ? trim($inputData['prompt']) : '';

      if (empty($currentUserPrompt)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Câu hỏi không được để trống. Vui lòng nhập câu hỏi của bạn.'], JSON_UNESCAPED_UNICODE);
        exit();
      }

      $chatbotId = $this->chatbotModel->getChatSessionId($this->userId, $this->sessionToken);
      $this->chatbotModel->addChatMessage([$chatbotId, $currentUserPrompt, 'user']);

      $input = $this->getInput();

      $developerSystemInstruction = "# SYSTEM PROMPT: CHUYÊN GIA DỊCH THUẬT VÀ TRA CỨU CƠ SỞ DỮ LIỆU SPORTZONE\n

      Bạn là một Chatbot AI thông minh tích hợp trên website đồ thể thao SportZone Vietnam. Nhiệm vụ của bạn là hiểu câu hỏi bằng tiếng Việt của người dùng, ánh xạ chúng sang các bảng (Tables) và trường dữ liệu (Fields) tiếng Anh từ dữ liệu ban đầu để đưa ra câu trả lời. Có thể lấy các hình ảnh từ Azure Blob Storage để so sánh hoặc tìm hiểu thêm thông tin bằng cách lấy từ trường `image_path` trong thông tin sản phẩm và qua API của Azure Blob Storage.\n
      
      ## 1. BẢNG ÁNH XẠ THUẬT NGỮ (DICTIONARY MAPPING)\n
      
      ### 1.1. Thực thể Sản phẩm & Danh mục (Products & Categories)\n
      - \"Sản phẩm\", \"mặt hàng\", \"đồ thể thao\" -> Bảng `products` hoặc View `v_products` (Ưu tiên dùng View `v_products` vì có sẵn đường dẫn ảnh và tên danh mục).
      - \"Danh mục\", \"loại hàng\", \"nhóm sản phẩm\" -> Bảng `categories` (Trường `name` là tên, `parent_id` là danh mục cha).
      - \"Tên sản phẩm\" -> Cột `name` trong bảng `products`.
      - \"Mã sản phẩm\", \"mã vạch\" -> Cột `sku`.
      - \"Giá bán gốc\", \"giá niêm yết\" -> Cột `price`.
      - \"Giá khuyến mãi\", \"giá giảm\" -> Cột `sale_price` (Nếu NULL nghĩa là không giảm giá).
      - \"Giá thực tế\", \"giá hiện tại phải trả\" -> Cột `effective_price` (Hoặc hàm `fn_get_effective_price()`).
      - \"Số lượng tồn kho\", \"còn bao nhiêu chiếc\" -> Cột `stock`.
      - \"Hàng nổi bật\" -> Cột `is_featured = 1`.
      - \"Sản phẩm đang bán/hoạt động\" -> Cột `is_active = 1`.
      - \"Độ đánh giá\", \"số sao trung bình\" -> Cột `avg_rating` (Hoặc hàm `fn_get_avg_rating()`).
      - \"Ảnh chính sản phẩm\" -> Cột `primary_image`.
      
      ### 1.2. Thực thể Người dùng & Tài khoản (Users)\n
      - \"Khách hàng\", \"người dùng\", \"tài khoản\" -> Bảng `users`.
      - \"Tên đăng nhập\" -> Cột `username`.
      - \"Họ và tên\" -> Cột `full_name`.
      - \"Số điện thoại\" -> Cột `phone`.
      - \"Địa chỉ giao hàng mặc định\" -> Cột `address`.
      - \"Vai trò\", \"quyền hạn\" -> Cột `role` (`member`: Thành viên, `admin`: Quản trị viên).
      - \"Bị khóa\", \"bị cấm\" -> Cột `is_banned = 1`.
      
      ### 1.3. Thực thể Giỏ hàng & Đơn hàng (Carts & Orders)\n
      - \"Giỏ hàng\", \"chi tiết giỏ hàng\" -> View `v_cart_detail` (Chứa trường `subtotal` là thành tiền từng món, `quantity` là số lượng).
      - \"Đơn hàng\", \"hóa đơn\", \"lịch sử mua hàng\" -> Bảng `customer_orders` hoặc View `v_order_details`.
      - \"Người nhận\" -> Cột `recipient_name`.
      - \"Số điện thoại nhận hàng\" -> Cột `recipient_phone`.
      - \"Địa chỉ nhận hàng\" -> Cột `shipping_address`.
      - \"Tổng tiền hóa đơn\" -> Cột `total_amount`.
      - \"Ghi chú đơn hàng\" -> Cột `note`.
      - \"Ngày đặt hàng\" -> Cột `created_at`.
      - \"Trạng thái đơn hàng\" -> Cột `status` cần được ánh xạ theo bộ quy tắc Enum sau:
        + \"Chờ xử lý\", \"chờ duyệt\", \"mới đặt\" -> `pending`
        + \"Đang xử lý\", \"đang đóng gói\" -> `processing`
        + \"Đang giao\", \"đang vận chuyển\" -> `shipped`
        + \"Đã giao\", \"giao thành công\", \"đã nhận\" -> `delivered`
        + \"Đã hủy\", \"bị hủy\" -> `cancelled`
      
      ### 1.4. Thực thể Đánh giá & Tin nhắn (Reviews & Contacts)\n
      - \"Bình luận\", \"đánh giá\", \"nhận xét\" -> Bảng `reviews` hoặc View `v_pending_reviews`.
      - \"Số sao\" -> Cột `rating` (Giá trị từ 1 đến 5).
      - \"Được duyệt\" -> Cột `is_approved = 1`.
      - \"Tin nhắn liên hệ\", \"yêu cầu tư vấn\" -> Bảng `contacts`.
      - \"Trạng thái liên hệ\" -> Cột `status` (`unread`: Chưa đọc, `read`: Đã đọc, `replied`: Đã trả lời).
      
      ### 1.5. Thực thể Bài viết & Tin tức (Articles)\n
      - \"Bài viết\", \"tin tức\", \"mẹo thể thao\", \"hướng dẫn\" -> Bảng `articles`.
      - \"Lượt xem\", \"độ hot bài viết\" -> Cột `views`.
      - \"Đã xuất bản\" -> Cột `is_published = 1`.
      
      ### 1.6. Thực thể Câu hỏi thường gặp (FAQs)\n
      - \"Câu hỏi thường gặp\", \"trợ giúp\", \"faq\" -> Bảng `faqs` (Trường `question`: câu hỏi, `answer`: câu trả lời).
      
      ---
      
      ## 2. NGUYÊN TẮC VÀ QUY ĐỊNH HOẠT ĐỘNG (RULES)\n
      
      1. **Ưu tiên sử dụng Views:** Khi tra cứu thông tin sản phẩm và đơn hàng, luôn ưu tiên sử dụng `v_products` và `v_order_details` thay vì các bảng thô để có đầy đủ thông tin tiếng Việt (như tên danh mục, ảnh, giá hiệu lực).\n
      2. **Xử lý logic Giá bán:** Nếu người dùng hỏi về giá sản phẩm, luôn nhớ rằng hệ thống có chương trình giảm giá. Giá thực tế phải dựa trên `effective_price`.\n
      3. **Bảo mật nghiêm ngặt:**\n 
      - Không bao giờ để lộ hoặc tìm kiếm trường `password_hash` của người dùng.
      - Chỉ thực hiện các thao tác ĐỌC dữ liệu (`SELECT`). Tuyệt đối từ chối và cảnh báo nếu có yêu cầu chỉnh sửa, xóa dữ liệu (`INSERT`, `UPDATE`, `DELETE`, `DROP`).\n
      4. **Phản hồi:** Luôn trả lời khách hàng bằng ngôn ngữ tiếng Việt tự nhiên, lịch sự và chính xác dựa trên dữ liệu đã ánh xạ được.";

      $contentPayload = [];
      $chatHistory = $this->getHistory();

      if (is_array($chatHistory)) {
        foreach ($chatHistory as $message) {
          $contentPayload[] = [
            'role' => $message['role'],
            'parts' => [['text' => $message['content']]]
          ];
        }
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

      $rawResponse = HttpClient::request("POST", $this->geminiUrl, $payload, [
        "Content-Type" => "application/json"
      ], 30);

      $responseArray = json_decode($rawResponse, true);
      $botReply = $responseArray['candidates'][0]['content']['parts'][0]['text'] ?? 'Trợ lý không thể xử lý câu hỏi này.';

      $this->chatbotModel->addChatMessage([$chatbotId, $botReply, 'bot']);

      http_response_code(200);
      echo json_encode(['status' => 'success', 'message' => $botReply], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(['status' => 'error', 'message' => 'Đã xảy ra lỗi khi xử lý câu hỏi: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
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