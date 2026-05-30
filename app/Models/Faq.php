<?php
class Faq
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function getAllGrouped($search = ''): array
    {
        if ($search !== '') {
            $stmt = $this->db->prepare("
                SELECT f.*, fc.name AS category
                FROM faqs f
                LEFT JOIN faq_categories fc ON f.category_id = fc.id
                WHERE f.is_active = 1
                  AND (f.question LIKE :q1 OR f.answer LIKE :q2)
                ORDER BY fc.name ASC, f.sort_order ASC, f.id ASC
            ");
            $stmt->execute([
                'q1' => '%' . $search . '%',
                'q2' => '%' . $search . '%',
            ]);
        } else {
            $stmt = $this->db->query("
                SELECT f.*, fc.name AS category
                FROM faqs f
                LEFT JOIN faq_categories fc ON f.category_id = fc.id
                WHERE f.is_active = 1
                ORDER BY fc.name ASC, f.sort_order ASC, f.id ASC
            ");
        }

        $rows    = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $grouped = [];
        foreach ($rows as $row) {
            $cat = $row['category'] ?? 'Khác';
            $grouped[$cat][] = $row;
        }
        return $grouped;
    }

    public function getAllForAdmin($search = ''): array
    {
        if ($search !== '') {
            $stmt = $this->db->prepare("
                SELECT f.*, fc.name AS category
                FROM faqs f
                LEFT JOIN faq_categories fc ON f.category_id = fc.id
                WHERE f.question LIKE :q1 OR f.answer LIKE :q2
                ORDER BY f.is_active ASC, f.id DESC
            ");
            $stmt->execute([
                'q1' => '%' . $search . '%',
                'q2' => '%' . $search . '%',
            ]);
        } else {
            $stmt = $this->db->query("
                SELECT f.*, fc.name AS category
                FROM faqs f
                LEFT JOIN faq_categories fc ON f.category_id = fc.id
                ORDER BY f.is_active ASC, f.id DESC
            ");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id)
    {
        $stmt = $this->db->prepare("
            SELECT f.*, fc.name AS category
            FROM faqs f
            LEFT JOIN faq_categories fc ON f.category_id = fc.id
            WHERE f.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCategories(): array
    {
        $stmt = $this->db->query("
            SELECT id, name FROM faq_categories
            WHERE is_active = 1
            ORDER BY sort_order ASC, name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(int $categoryId, string $question, string $answer, int $sortOrder = 0, int $isActive = 1): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO faqs (category_id, question, answer, sort_order, is_active)
            VALUES (:cat, :q, :a, :s, :act)
        ");
        return $stmt->execute([
            'cat' => $categoryId,
            'q'   => $question,
            'a'   => $answer,
            's'   => $sortOrder,
            'act' => $isActive,
        ]);
    }

    public function update(int $id, int $categoryId, string $question, string $answer, int $sortOrder = 0, int $isActive = 1): bool
    {
        $stmt = $this->db->prepare("
            UPDATE faqs
            SET category_id = :cat,
                question    = :q,
                answer      = :a,
                sort_order  = :s,
                is_active   = :act
            WHERE id = :id
        ");
        return $stmt->execute([
            'cat' => $categoryId,
            'q'   => $question,
            'a'   => $answer,
            's'   => $sortOrder,
            'act' => $isActive,
            'id'  => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM faqs WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function createCategory(string $name): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO faq_categories (name, is_active)
            VALUES (:name, 1)
        ");
        $stmt->execute(['name' => $name]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * LƯU CÂU HỎI MỚI TỪ NGƯỜI DÙNG (TRẠNG THÁI CHỜ DUYỆT)
     */
    public function insertPendingQuestion(string $name, string $email, string $question): bool
    {
        // 1. Tìm 1 category_id mặc định để gán (vì khoá ngoại DB bắt buộc có)
        $stmtCat = $this->db->query("SELECT id FROM faq_categories LIMIT 1");
        $defaultCat = $stmtCat->fetchColumn();
        $categoryId = $defaultCat ? $defaultCat : 1;

        // 2. Gắn thông tin người gửi vào đầu câu hỏi
        $fullQuestion = "[Từ: {$name} - {$email}]\n\n{$question}";
        
        // 3. Insert với is_active = 0 (Ẩn/Chờ duyệt) và answer rỗng
        $stmt = $this->db->prepare("
            INSERT INTO faqs (category_id, question, answer, sort_order, is_active)
            VALUES (:cat, :q, '', 0, 0)
        ");
        
        return $stmt->execute([
            'cat' => $categoryId,
            'q'   => $fullQuestion
        ]);
    }
}
