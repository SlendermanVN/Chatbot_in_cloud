<?php

class CommentModel
{
    private $db;

    // Nhận $pdo từ Router truyền vào, không tự ý kết nối mới
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Lấy bình luận của một bài viết (Articles)
     */
    public function getCommentsByNews($article_id)
    {
        // Trong SQL của em: bảng reviews, cột article_id, trạng thái là is_approved (0/1)
        $sql = "SELECT r.*, u.full_name, u.avatar 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.article_id = :article_id AND r.is_approved = 1 
                ORDER BY r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":article_id", $article_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy các bình luận đang chờ duyệt (Admin dùng)
     * Sử dụng View v_pending_reviews có sẵn trong SQL của em cho nhanh
     */
    /**
     * Lấy các bình luận đang chờ duyệt (Admin dùng)
     * Đã thay thế View SQL bằng lệnh JOIN trực tiếp để đảm bảo đủ 100% cột cho giao diện
     */
    public function getPendingComments()
    {
        $sql = "SELECT r.id, r.content, r.is_approved, r.created_at, 
                       u.full_name, u.email, 
                       a.title as news_title, a.slug as news_slug
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                JOIN articles a ON r.article_id = a.id
                WHERE r.is_approved = 0 
                ORDER BY r.created_at DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPending()
    {
        // Chỉ đếm những bình luận CÓ LIÊN KẾT với bảng articles (Bài viết)
        $sql = "SELECT COUNT(r.id) 
                FROM reviews r 
                JOIN articles a ON r.article_id = a.id 
                WHERE r.is_approved = 0";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetchColumn();
    }

    public function countApproved()
    {
        // Tương tự, chỉ đếm bình luận Bài viết đã duyệt
        $sql = "SELECT COUNT(r.id) 
                FROM reviews r 
                JOIN articles a ON r.article_id = a.id 
                WHERE r.is_approved = 1";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Thêm bình luận mới cho bài viết
     */
    public function insert($data)
    {
        // Lưu ý: SQL quy định rating chỉ dành cho sản phẩm, bài viết để NULL
        $sql = "INSERT INTO reviews (article_id, user_id, content, rating, is_approved, created_at) 
                VALUES (:article_id, :user_id, :content, NULL, 0, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":article_id", $data['article_id'], PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(":content", $data['content'], PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Duyệt bình luận (Admin)
     */
    public function approve($id)
    {
        $sql = "UPDATE reviews SET is_approved = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Xóa bình luận
     */
    public function delete($id)
    {
        $sql = "DELETE FROM reviews WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}