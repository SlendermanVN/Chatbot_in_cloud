<?php
class Review {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getPending() {
        $stmt = $this->db->prepare("
            SELECT r.*, u.username, u.full_name, u.email, 
                   p.name as product_name, p.id as product_id,
                   a.title as article_title, a.id as article_id
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            LEFT JOIN products p ON r.product_id = p.id 
            LEFT JOIN articles a ON r.article_id = a.id 
            WHERE r.is_approved = 0
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPending() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM reviews WHERE is_approved = 0");
        return (int)$stmt->fetchColumn();
    }

    public function countApproved() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM reviews WHERE is_approved = 1");
        return (int)$stmt->fetchColumn();
    }

    public function updateStatus($id, $is_approved) {
        $stmt = $this->db->prepare("UPDATE reviews SET is_approved = :is_approved WHERE id = :id");
        return $stmt->execute(['is_approved' => $is_approved, 'id' => $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getByArticleId($articleId) {
        $stmt = $this->db->prepare("
            SELECT r.*, u.username, u.full_name 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.article_id = :article_id AND r.is_approved = 1 
            ORDER BY r.created_at DESC
        ");
        $stmt->execute(['article_id' => $articleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByProductId($productId) {
        $stmt = $this->db->prepare("
            SELECT r.*, u.username, u.full_name 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.product_id = :product_id AND r.is_approved = 1 
            ORDER BY r.created_at DESC
        ");
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addArticleComment($userId, $articleId, $content) {
        $stmt = $this->db->prepare("
            INSERT INTO reviews (user_id, article_id, content, is_approved, created_at) 
            VALUES (:user_id, :article_id, :content, 0, NOW())
        ");
        return $stmt->execute([
            'user_id' => $userId,
            'article_id' => $articleId,
            'content' => $content
        ]);
    }

    public function addProductReview($userId, $productId, $rating, $content) {
        $stmt = $this->db->prepare("
            INSERT INTO reviews (user_id, product_id, rating, content, is_approved, created_at) 
            VALUES (:user_id, :product_id, :rating, :content, 0, NOW())
        ");
        return $stmt->execute([
            'user_id'    => $userId,
            'product_id' => $productId,
            'rating'     => $rating,
            'content'    => $content
        ]);
    }
}
