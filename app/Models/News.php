<?php
class News
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Lấy danh sách bài viết (Articles)
     */
    public function getAll($limit = 10, $offset = 0, $onlyPublished = false)
    {
        $where = $onlyPublished ? "WHERE is_published = 1" : "";
        $stmt = $this->db->prepare("SELECT * FROM articles {$where} ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM articles WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo bài viết mới
     * Mapping: image -> thumbnail, status -> is_published, meta_desc -> meta_description
     */
    public function create($title, $slug, $content, $image, $status, $meta_desc, $meta_keywords = '')
    {
        $sql = "INSERT INTO articles (title, slug, content, thumbnail, is_published, meta_description, meta_keywords, created_at) 
                VALUES (:title, :slug, :content, :image, :status, :meta_desc, :meta_keywords, NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'image' => $image,
            'status' => $status, 
            'meta_desc' => $meta_desc,
            'meta_keywords' => $meta_keywords
        ]);
    }

    /**
     * Cập nhật bài viết
     */
    public function update($id, $title, $slug, $content, $image, $status, $meta_desc, $meta_keywords = '')
    {
        $query = "UPDATE articles SET title = :title, slug = :slug, content = :content, is_published = :status, meta_description = :meta_desc, meta_keywords = :meta_keywords";
        $params = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'status' => $status,
            'meta_desc' => $meta_desc,
            'meta_keywords' => $meta_keywords,
            'id' => $id
        ];

        if ($image) {
            $query .= ", thumbnail = :image";
            $params['image'] = $image;
        }
        $query .= " WHERE id = :id";

        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM articles WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Phân trang và tìm kiếm bài viết (Đã sửa từ bảng news sang articles)
     */
    public function getPaginatedNews($keyword, $limit, $offset, $onlyPublished = false)
    {
        $where = "WHERE (title LIKE :k1 OR content LIKE :k2)";
        if ($onlyPublished) {
            $where .= " AND is_published = 1";
        }
        $sql = "SELECT * FROM articles {$where} ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $searchTag = "%" . $keyword . "%";
        $stmt->bindValue(":k1", $searchTag, PDO::PARAM_STR);
        $stmt->bindValue(":k2", $searchTag, PDO::PARAM_STR);
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số bài viết để làm phân trang
     */
    public function countTotalNews($keyword, $onlyPublished = false)
    {
        $where = "WHERE (title LIKE :k1 OR content LIKE :k2)";
        if ($onlyPublished) {
            $where .= " AND is_published = 1";
        }
        $sql = "SELECT COUNT(*) as total FROM articles {$where}";
        $stmt = $this->db->prepare($sql);
        $searchTag = "%" . $keyword . "%";
        $stmt->bindValue(":k1", $searchTag, PDO::PARAM_STR);
        $stmt->bindValue(":k2", $searchTag, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row["total"] ?? 0;
    }

    /**
     * Tìm bài viết theo Slug (Dùng cho trang chi tiết tin tức)
     */
    public function findBySlug($slug)
    {
        $sql = "SELECT * FROM articles WHERE slug = :slug LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":slug", $slug, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}