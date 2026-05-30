<?php
class Contact {
    // TODO Anh Đức: Tạo các hàm xử lý bảng contacts (thêm liên hệ, lấy danh sách, đánh dấu đã đọc...) - Xong
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getAll($limit = 10, $offset = 0) {
        $stmt = $this->db->prepare("SELECT * FROM contacts ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM contacts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($user_id, $name, $email, $phone, $subject, $message) {
        $stmt = $this->db->prepare("INSERT INTO contacts (user_id, name, email, phone, subject, message, status, created_at) VALUES (:user_id, :name, :email, :phone, :subject, :message, 'unread', NOW())");
        
        return $stmt->execute([
            'user_id' => $user_id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message
        ]);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE contacts SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function reply($id, $replied_message) {
        $stmt = $this->db->prepare("UPDATE contacts SET admin_note = :msg, status = 'replied' WHERE id = :id");
        return $stmt->execute(['msg' => $replied_message, 'id' => $id]);
    }

    public function countTotal() {
        return $this->db->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
    }

    public function countUnread() {
        return $this->db->query("SELECT COUNT(*) FROM contacts WHERE status = 'unread' OR status = 'pending'")->fetchColumn();
    }

    function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
