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
    private $chatbotModel;
    private $orderModel;
    private $productModel;
    private $reviewModel;
    private $userModel;
    private $faqModel;
    private $newsModel;
    private $settingModel; // Đã bổ sung

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
        $this->settingModel = new Setting($pdo); // Đã khởi tạo
        $this->newsModel = new News($pdo);
        $this->chatbotModel = new Chatbot($pdo2);

        $this->userId = $_SESSION['user_id'] ?? null;
        $this->sessionToken = $_SESSION['chat_session_token'] ?? null;

        $this->geminiApiKey = getenv('GEMINI_API_KEY');
        $this->geminiModel = getenv('GEMINI_MODEL') ?: 'gemini-1.5-flash';
        $this->geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key={$this->geminiApiKey}";
    }

    public function index()
    {
        $this->requireLogin();
        if (!$this->sessionToken) {
            $this->createSession();
        }

        $rawSettings = $this->settingModel->getAll();
        $settings = is_array($rawSettings) ? array_column($rawSettings, 'setting_value', 'setting_key') : [];

        $this->render("chatbot/index", [
            'chatHistory' => $this->getHistory(),
            'setting' => $settings
        ]);
    }

    public function getInput()
    {
        // CẢNH BÁO: Kéo tất cả dữ liệu ở đây là nguy hiểm cho bộ nhớ. Tạm thời giữ nguyên logic hiện tại để hệ thống hoạt động.
        return [
            'Đơn hàng của người dùng' => $this->userId ? $this->orderModel->getOrdersByUser($this->userId) : [],
            'Tất cả sản phẩm' => $this->productModel->getAll(),
            'Bảng đánh giá của người dùng' => $this->reviewModel->getPending(),
            'Câu hỏi thường gặp' => $this->faqModel->getAllGrouped(),
            'Thông tin người dùng' => $this->userId ? $this->userModel->findById($this->userId) : [],
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
            // 1. Validate Input
            $inputData = json_decode(file_get_contents('php://input'), true);
            $currentUserPrompt = isset($inputData['prompt']) ? trim($inputData['prompt']) : '';

            if (empty($currentUserPrompt)) {
                throw new InvalidArgumentException('Câu hỏi không được để trống.');
            }

            if (empty($this->geminiApiKey)) {
                throw new RuntimeException('Chưa cấu hình API Key cho hệ thống AI.');
            }

            // 2. Chuẩn bị Context
            $chatbotId = $this->chatbotModel->getChatSessionId($this->userId, $this->sessionToken);
            $input = $this->getInput();
            $clientInfo = $input['Thông tin người dùng'] ?? [];
            $customerName = $clientInfo['full_name'] ?? ($clientInfo['username'] ?? 'Khách hàng');

            // System Instruction (Tách biệt theo chuẩn Gemini API)
            $developerSystemInstruction = "Bạn là một Chatbot AI thông minh tích hợp trên website đồ thể thao SportZone Vietnam. Nhiệm vụ của bạn là hiểu câu hỏi bằng tiếng Việt của người dùng, ánh xạ chúng sang các bảng (Tables) và trường dữ liệu (Fields) tiếng Anh từ dữ liệu ban đầu để đưa ra câu trả lời. Có thể lấy các hình ảnh từ Azure Blob Storage.\n\nNguyên tắc:\n1. Ưu tiên sử dụng Views v_products.\n2. Giá thực tế dùng effective_price.\n3. Bảo mật: Không tiết lộ password_hash, chỉ thực hiện READ, cấm UPDATE/DELETE.\n4. Trả lời bằng tiếng Việt lịch sự, tự nhiên.";

            $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
            
            // Grounding Context (Được đẩy vào phần User Request)
            $groundingContext = "[DỮ LIỆU HỆ THỐNG THỜI GIAN THỰC]\n";
            $groundingContext .= "- Tên khách hàng hiện tại: " . $customerName . "\n";
            $groundingContext .= "- Đơn hàng của họ: " . json_encode($input['Đơn hàng của người dùng'], $jsonFlags) . "\n";
            
            // Lịch sử Chat
            $historyContextString = "[LỊCH SỬ TRÒ CHUYỆN]\n";
            $chatHistory = $input['Lịch sử trò chuyện'];
            if (is_array($chatHistory)) {
                foreach ($chatHistory as $message) {
                    $senderLabel = (strtolower($message['sender'] ?? 'user') === 'user') ? 'User' : 'Model';
                    $historyContextString .= "- {$senderLabel}: " . ($message['message_text'] ?? '') . "\n";
                }
            }

            $finalUserText = $groundingContext . "\n" . $historyContextString . "\n[CÂU HỎI MỚI CỦA NGƯỜI DÙNG]: " . $currentUserPrompt;

            // 3. Đóng gói Payload chuẩn Google Gemini (Tách System Instruction)
            $payloadArray = [
                "systemInstruction" => [
                    "parts" => [
                        ["text" => $developerSystemInstruction]
                    ]
                ],
                "contents" => [
                    [
                        "role" => "user",
                        "parts" => [
                            ["text" => $finalUserText]
                        ]
                    ]
                ]
            ];

            // CHUYỂN PAYLOAD THÀNH JSON STRING (Khắc phục lỗi 500 chính)
            $jsonPayload = json_encode($payloadArray, $jsonFlags);
            if ($jsonPayload === false) {
                throw new RuntimeException('Lỗi đóng gói dữ liệu JSON: ' . json_last_error_msg());
            }

            $headers = [
                "Content-Type: application/json"
            ];

            // 4. Giao tiếp API & Lưu tin nhắn User
            $this->chatbotModel->addChatMessage([
                'chatbot_id' => $chatbotId,
                'message_text' => $currentUserPrompt,
                'sender' => 'user'
            ]);

            $rawResponse = HttpClient::request("POST", $this->geminiUrl, $jsonPayload, $headers, 30);

            if (!$rawResponse) {
                throw new RuntimeException('Không nhận được phản hồi từ API Service.');
            }

            $responseArray = json_decode($rawResponse, true);

            // 5. Kiểm tra lỗi trả về từ Google
            if (isset($responseArray['error'])) {
                error_log("Gemini API Error: " . json_encode($responseArray['error']));
                throw new RuntimeException('AI Service đang tạm thời gián đoạn. Vui lòng thử lại sau.');
            }

            // 6. Bóc tách và Lưu kết quả
            $botReply = $responseArray['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if (!$botReply) {
                throw new RuntimeException('Cấu trúc dữ liệu trả về từ AI không hợp lệ.');
            }

            $this->chatbotModel->addChatMessage([
                'chatbot_id' => $chatbotId,
                'message_text' => $botReply,
                'sender' => 'bot'
            ]);

            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => $botReply], JSON_UNESCAPED_UNICODE);

        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            // Ghi log lỗi vào file hệ thống thay vì ném ra frontend (Bảo mật nguyên tắc)
            error_log("Chatbot Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Hệ thống đang gặp sự cố khi xử lý câu hỏi. Hãy thử lại sau ít phút.'], JSON_UNESCAPED_UNICODE);
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