-- =============================================================
--  DATABASE : chatbot_db
--  PROJECT  : SportZone Vietnam - Website Dụng cụ Thể thao
--  SUBJECT  : Lập trình Web (HK2 2025-2026)
--  ENGINE   : InnoDB   (FK, transaction)
--  CHARSET  : utf8mb4  (tiếng Việt + emoji)
--  NORM     : 3NF
-- =============================================================

DROP DATABASE IF EXISTS chatbot_db;
CREATE DATABASE chatbot_db; 
USE chatbot_db;

SET FOREIGN_KEY_CHECKS = 0;
-- BẢNG 1: QUẢN LÝ MỘT CHATBOT CỦA MỘT USER (ONE USER - ONE  BOT)
CREATE TABLE chat_session (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  session_token VARCHAR(255) NOT NULL COMMENT 'Token định danh phiên làm việc của chatbot (có thể dùng để xác thực API)',
  user_id VARCHAR(50) NOT NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uk_session_token(session_token) COMMENT 'Đảm bảo mỗi phiên làm việc có một token duy nhất'
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý chatbot của từng user, đảm bảo mỗi user chỉ có một chatbot duy nhất';

-- BẢNG 2: LƯU TRỮ CHI TIẾT TIN NHẮN (CHAT MESSAGES)
CREATE TABLE chat_messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  chatbot_id INT UNSIGNED NOT NULL,
  message_text TEXT NOT NULL,
  sender ENUM('user', 'bot') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  FOREIGN KEY (chatbot_id) REFERENCES chat_session(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu trữ chi tiết tin nhắn giữa user và chatbot, liên kết với chat_session qua chatbot_id';

-- BẢNG 3: TRI THỨC BOT / TRẢ LỜI NHANH (BOT_KNOWLEDGE_BASE)
CREATE TABLE bot_knowledge_base (
  id INT AUTO_INCREMENT,
  keyword VARCHAR(255) NOT NULL COMMENT 'Từ khóa (Ví dụ: "hoàn tiền", "bảng giá", "liên hệ")',
  response_text TEXT NOT NULL COMMENT 'Câu trả lời tương ứng',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uk_keyword(keyword) COMMENT 'Đảm bảo mỗi từ khóa là duy nhất'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng tri thức của bot, chứa các cặp từ khóa và câu trả lời để bot có thể phản hồi nhanh dựa trên từ khóa';

-- Bật lại kiểm tra khóa ngoại sau khi cấu trúc được dựng xong
SET FOREIGN_KEY_CHECKS = 1;