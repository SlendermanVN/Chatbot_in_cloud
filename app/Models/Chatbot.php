<?php
// Chỉ dùng cho mục đích quản lý các phiên chat và lịch sử trò chuyện của người dùng với chatbot, chỉ hiển thị khi người dùng đã đăng nhập
class Chatbot
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  public function createChatSession($userId)
  {
    $sessionToken = bin2hex(random_bytes(16)); // Tạo token ngẫu nhiên cho phiên làm việc
    $stmt = $this->pdo->prepare("INSERT INTO chat_session (user_id, session_token) VALUES (:user_id, :session_token)");
    $stmt->execute([
      'user_id' => $userId,
      'session_token' => $sessionToken
    ]);
    return $sessionToken;
  }

  public function getChatHistory($userId, $sessionToken)
  {
    $stmt = $this->pdo->prepare("SELECT * FROM chat_messages WHERE chatbot_id = (SELECT id FROM chat_session WHERE user_id = :user_id AND session_token = :session_token) ORDER BY created_at ASC");
    $stmt->execute([
      'user_id' => $userId,
      'session_token' => $sessionToken
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function addChatMessage($data)
  {
    $stmt = $this->pdo->prepare("INSERT INTO chat_messages (chatbot_id, message_text, sender) VALUES (:chatbot_id, :message_text, :sender)");
    return $stmt->execute($data);
  }

  public function getKnowledgeBase()
  {
    $stmt = $this->pdo->query("SELECT keyword, response_text FROM bot_knowledge_base");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getChatSessionId($userId, $sessionToken)
  {
    $stmt = $this->pdo->prepare("SELECT id FROM chat_session WHERE user_id = :user_id AND session_token = :session_token LIMIT 1");
    $stmt->execute([
      'user_id' => $userId,
      'session_token' => $sessionToken
    ]);

    $chatSession = $stmt->fetch(PDO::FETCH_ASSOC);
    return $chatSession['id'] ?? null;
  }

  public function createKnowledgeEntry($data)
  {
    $stmt = $this->pdo->prepare("INSERT INTO bot_knowledge_base (keyword, response_text) VALUES (:keyword, :response_text)");
    return $stmt->execute($data);
  }
}
