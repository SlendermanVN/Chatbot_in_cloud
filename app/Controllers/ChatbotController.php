<?php
require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../Models/Order.php";
require_once __DIR__ . "/../Models/Product.php";
require_once __DIR__ . "/../Models/Review.php";
require_once __DIR__ . "/../Models/Setting.php";
require_once __DIR__ . "/../Models/Faq.php";
require_once __DIR__ . "/../Models/Chatbot.php";

class ChatbotController extends BaseController
{
  private $chatbotModel;
  private $orderModel;
  private $productModel;
  private $reviewModel;
  private $userModel;
  private $faqModel;

  public function __construct($pdo, $pdo2)
  {
    parent::__construct($pdo);
    $this->orderModel = new Order($pdo);
    $this->productModel = new Product($pdo);
    $this->reviewModel = new Review($pdo);
    $this->userModel = new User($pdo);
    $this->faqModel = new Faq($pdo);
    $this->chatbotModel = new Chatbot($pdo2);
  }

  public function index()
  {
    $this->requireLogin();

    $userId = $_SESSION['user_id'];
    $sessionToken = $_SESSION['chatbot_session_token'] ?? null;

    if (!$sessionToken) {
      $sessionToken = $this->chatbotModel->createChatSession($userId);
      $_SESSION['chatbot_session_token'] = $sessionToken;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $question = trim($_POST['message_text'] ?? '');

      if ($question !== '') {
        $normalizedQuestion = $this->normalizeQuestion($question);
        $cachedReply = $this->getCachedReply($userId, $normalizedQuestion);
        $aiContext = $this->buildAiContext($userId);
        $chatbotId = $this->chatbotModel->getChatSessionId($userId, $sessionToken);

        if (!$chatbotId) {
          $sessionToken = $this->chatbotModel->createChatSession($userId);
          $_SESSION['chatbot_session_token'] = $sessionToken;
          $chatbotId = $this->chatbotModel->getChatSessionId($userId, $sessionToken);
        }

        if ($chatbotId) {
          $this->chatbotModel->addChatMessage([
            'chatbot_id' => $chatbotId,
            'message_text' => $question,
            'sender' => 'user'
          ]);

          if ($cachedReply !== null) {
            $botReply = $cachedReply;
          } else {
            $botReply = $this->generateGeminiReply($question, $aiContext);
            $this->setCachedReply($userId, $normalizedQuestion, $botReply);
          }

          $this->chatbotModel->addChatMessage([
            'chatbot_id' => $chatbotId,
            'message_text' => $botReply,
            'sender' => 'bot'
          ]);

          if ($this->isAjaxRequest()) {
            $this->json([
              'success' => true,
              'question' => $question,
              'reply' => $botReply,
              'cached' => $cachedReply !== null,
            ]);
          }
        }
      }

      if ($this->isAjaxRequest()) {
        $this->json([
          'success' => false,
          'message' => 'Câu hỏi trống hoặc không thể xử lý yêu cầu.',
        ], 400);
      }

      $this->redirect('chatbot');
    }

    $chatHistory = $this->chatbotModel->getChatHistory($userId, $sessionToken);
    $knowledgeBase = $this->chatbotModel->getKnowledgeBase();

    $this->render('chatbot/index', compact('chatHistory', 'knowledgeBase'), 'Chatbot - SportZone');
  }

  private function generateGeminiReply($question, array $aiContext = [])
  {
    $apiKey = $this->getGeminiApiKey();
    $modelCandidates = $this->getGeminiModelCandidates();

    if (empty($apiKey)) {
      return 'Chưa cấu hình GEMINI_API_KEY nên mình chưa thể gọi Gemini. Hãy thêm biến môi trường này rồi thử lại.';
    }

    $prompt = $this->buildGeminiPrompt($question, $aiContext);

    $payload = [
      'contents' => [
        [
          'role' => 'user',
          'parts' => [
            ['text' => $prompt]
          ]
        ]
      ],
      'generationConfig' => [
        'temperature' => 0.4,
        'maxOutputTokens' => 512,
      ]
    ];

    foreach ($modelCandidates as $model) {
      $result = $this->callGeminiModel($apiKey, $model, $payload);

      if (($result['ok'] ?? false) === true) {
        return $result['text'];
      }

      if (($result['statusCode'] ?? 0) === 429) {
        continue;
      }

      if (($result['statusCode'] ?? 0) !== 404) {
        return $this->buildFallbackReply($question, $aiContext, $result['message'] ?? '');
      }
    }

    return $this->buildFallbackReply($question, $aiContext, 'Gemini đang bị giới hạn hoặc không tìm thấy model phù hợp.');
  }

  private function callGeminiModel($apiKey, $model, array $payload)
  {
    $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent?key=' . urlencode($apiKey);
    $ch = curl_init($endpoint);

    curl_setopt_array($ch, [
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
      ],
      CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
      CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
      $error = curl_error($ch);
      return [
        'ok' => false,
        'message' => 'Không thể gọi Gemini lúc này. Lỗi kết nối: ' . $error,
      ];
    }

    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($statusCode >= 400) {
      return [
        'ok' => false,
        'statusCode' => $statusCode,
        'message' => 'Gemini trả về lỗi HTTP ' . $statusCode . ' với model ' . $model . '.',
      ];
    }

    $data = json_decode($response, true);
    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (trim($text) === '') {
      return [
        'ok' => false,
        'message' => 'Mình chưa nhận được phản hồi hợp lệ từ Gemini. Hãy thử đặt câu hỏi khác.',
      ];
    }

    return [
      'ok' => true,
      'text' => trim($text),
    ];
  }

  private function buildFallbackReply($question, array $aiContext, $reason = '')
  {
    $normalizedQuestion = mb_strtolower($question, 'UTF-8');
    $contextFaqs = $this->extractFaqItems($aiContext['faq_groups'] ?? []);
    $matchedFaq = $this->findBestFaqMatch($normalizedQuestion, $contextFaqs);

    if ($matchedFaq) {
      return $matchedFaq['answer'];
    }

    if (preg_match('/\b(đơn|order|order\s|trạng thái|tình trạng|vận đơn|shipping|giao hàng)\b/u', $normalizedQuestion)) {
      $recentOrders = $aiContext['recent_orders'] ?? [];
      if (!empty($recentOrders)) {
        $latestOrder = $recentOrders[0];
        $itemNames = [];
        foreach (($latestOrder['items'] ?? []) as $item) {
          if (!empty($item['product_name'])) {
            $itemNames[] = $item['product_name'];
          }
        }

        $summary = 'Đơn hàng gần nhất của bạn #' . ($latestOrder['id'] ?? '') . ' hiện ở trạng thái ' . ($latestOrder['status'] ?? 'không rõ') . '.';
        if (!empty($itemNames)) {
          $summary .= ' Sản phẩm trong đơn: ' . implode(', ', array_slice($itemNames, 0, 3)) . '.';
        }
        return $summary . ' Nếu bạn muốn, mình có thể giúp bạn xem thêm các đơn gần đây.';
      }
    }

    if (preg_match('/\b(sản phẩm|product|mua|gợi ý|nổi bật|featured)\b/u', $normalizedQuestion)) {
      $featuredProducts = $aiContext['featured_products'] ?? [];
      $featuredNames = [];
      foreach (array_slice($featuredProducts, 0, 4) as $product) {
        if (!empty($product['name'])) {
          $featuredNames[] = $product['name'];
        }
      }

      if (!empty($featuredNames)) {
        return 'Một số sản phẩm nổi bật hiện tại: ' . implode(', ', $featuredNames) . '. Nếu bạn cần, mình có thể hỗ trợ chọn theo nhu cầu.';
      }
    }

    $userName = $aiContext['user']['full_name'] ?? $aiContext['user']['username'] ?? 'bạn';
    $fallback = 'Hiện tại hệ thống đang gặp sự cố, ';
    return trim($fallback);
  }

  private function extractFaqItems(array $faqGroups)
  {
    $items = [];
    foreach ($faqGroups as $groupItems) {
      if (!is_array($groupItems)) {
        continue;
      }
      foreach ($groupItems as $faq) {
        $items[] = $faq;
      }
    }
    return $items;
  }

  private function findBestFaqMatch($normalizedQuestion, array $faqItems)
  {
    foreach ($faqItems as $faq) {
      $question = mb_strtolower((string) ($faq['question'] ?? ''), 'UTF-8');
      $answer = (string) ($faq['answer'] ?? '');

      if ($question !== '' && (str_contains($normalizedQuestion, $question) || str_contains($question, $normalizedQuestion))) {
        return ['question' => $faq['question'] ?? '', 'answer' => $answer];
      }

      $keywords = preg_split('/\s+/', trim($question));
      foreach ($keywords as $keyword) {
        if (mb_strlen($keyword, 'UTF-8') >= 4 && str_contains($normalizedQuestion, $keyword)) {
          return ['question' => $faq['question'] ?? '', 'answer' => $answer];
        }
      }
    }

    return null;
  }

  private function buildAiContext($userId)
  {
    $user = $this->userModel->getFullUserById($userId);
    $orders = $this->orderModel->getOrdersByUser($userId);
    $featuredProducts = $this->productModel->getFeatured(6);
    $faqGroups = $this->faqModel->getAllGrouped('');

    $recentOrders = [];
    $reviewSourceProducts = [];

    foreach (array_slice($orders, 0, 3) as $order) {
      $orderItems = $this->orderModel->getOrderItems($order['id']);
      $recentOrders[] = [
        'id' => $order['id'],
        'status' => $order['status'] ?? 'unknown',
        'total_amount' => $order['total_amount'] ?? null,
        'created_at' => $order['created_at'] ?? null,
        'items' => array_slice($orderItems, 0, 5),
      ];

      foreach ($orderItems as $item) {
        if (!empty($item['product_id'])) {
          $reviewSourceProducts[(int) $item['product_id']] = $item['product_id'];
        }
      }
    }

    $recentReviews = [];
    foreach (array_slice(array_values($reviewSourceProducts), 0, 3) as $productId) {
      $recentReviews = array_merge($recentReviews, array_slice($this->reviewModel->getByProductId($productId), 0, 2));
    }

    return [
      'user' => [
        'id' => $user['id'] ?? null,
        'username' => $user['username'] ?? null,
        'full_name' => $user['full_name'] ?? null,
        'email' => $user['email'] ?? null,
        'phone' => $user['phone'] ?? null,
        'address' => $user['address'] ?? null,
        'role' => $user['role'] ?? null,
      ],
      'recent_orders' => $recentOrders,
      'featured_products' => $featuredProducts,
      'faq_groups' => array_slice($faqGroups, 0, 5, true),
      'recent_reviews' => array_slice($recentReviews, 0, 5),
    ];
  }

  private function buildGeminiPrompt($question, array $context)
  {
    $contextJson = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    return "Bạn là trợ lý ảo của SportZone. Trả lời ngắn gọn, rõ ràng, đúng trọng tâm bằng tiếng Việt.\n"
      . "Chỉ dùng thông tin trong phần NGUỒN nếu phù hợp. Nếu thiếu dữ liệu, nói rõ là chưa có đủ thông tin thay vì bịa.\n\n"
      . "NGUỒN DỮ LIỆU:\n" . $contextJson . "\n\n"
      . "CÂU HỎI CỦA NGƯỜI DÙNG: " . $question;
  }

  private function normalizeQuestion($question)
  {
    $question = mb_strtolower(trim((string) $question), 'UTF-8');
    $question = preg_replace('/\s+/u', ' ', $question);
    return $question ?: '';
  }

  private function getCachedReply($userId, $normalizedQuestion)
  {
    if ($normalizedQuestion === '') {
      return null;
    }

    $cache = $_SESSION['chatbot_answer_cache'][$userId][$normalizedQuestion] ?? null;
    if (!is_array($cache) || empty($cache['reply'])) {
      return null;
    }

    $createdAt = (int) ($cache['created_at'] ?? 0);
    if ($createdAt > 0 && (time() - $createdAt) > 86400) {
      unset($_SESSION['chatbot_answer_cache'][$userId][$normalizedQuestion]);
      return null;
    }

    return (string) $cache['reply'];
  }

  private function setCachedReply($userId, $normalizedQuestion, $reply)
  {
    if ($normalizedQuestion === '' || trim((string) $reply) === '') {
      return;
    }

    if (!isset($_SESSION['chatbot_answer_cache'])) {
      $_SESSION['chatbot_answer_cache'] = [];
    }

    if (!isset($_SESSION['chatbot_answer_cache'][$userId])) {
      $_SESSION['chatbot_answer_cache'][$userId] = [];
    }

    $_SESSION['chatbot_answer_cache'][$userId][$normalizedQuestion] = [
      'reply' => (string) $reply,
      'created_at' => time(),
    ];

    if (count($_SESSION['chatbot_answer_cache'][$userId]) > 20) {
      uasort($_SESSION['chatbot_answer_cache'][$userId], function ($left, $right) {
        return ($left['created_at'] ?? 0) <=> ($right['created_at'] ?? 0);
      });
      $_SESSION['chatbot_answer_cache'][$userId] = array_slice($_SESSION['chatbot_answer_cache'][$userId], -20, null, true);
    }
  }

  private function getGeminiApiKey()
  {
    $apiKey = getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? '');

    if (!empty($apiKey)) {
      return trim($apiKey);
    }

    $envPath = dirname(__DIR__, 2) . '/.env';
    if (!is_file($envPath)) {
      return '';
    }

    $envValues = parse_ini_file($envPath, false, INI_SCANNER_RAW);
    if (!is_array($envValues) || empty($envValues['GEMINI_API_KEY'])) {
      return '';
    }

    return trim((string) $envValues['GEMINI_API_KEY'], " \t\n\r\0\x0B\"'");
  }

  private function getGeminiModelCandidates()
  {
    $preferredModel = getenv('GEMINI_MODEL') ?: ($_ENV['GEMINI_MODEL'] ?? '');
    $candidates = [];

    if (!empty($preferredModel)) {
      $candidates[] = trim($preferredModel);
    }

    $fallbackModels = [
      'gemini-2.5-flash',
      'gemini-2.0-flash',
      'gemini-1.5-flash',
      'gemini-1.5-pro',
    ];

    foreach ($fallbackModels as $model) {
      if (!in_array($model, $candidates, true)) {
        $candidates[] = $model;
      }
    }

    return $candidates;
  }

  private function isAjaxRequest()
  {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
      && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
  }
}
