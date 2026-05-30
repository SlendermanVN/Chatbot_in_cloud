<?php
class Product
{
    //TODO Văn Phát: Xử lý hiển thị danh sách sản phẩm, chi tiết sản phẩm, và CRUD cho admin
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    // ============================================================
    // PHẦN PUBLIC (trang khách)
    // ============================================================

    /**
     * Lấy tất cả sản phẩm đang active (có phân trang)
     * Dùng view v_products để lấy cả ảnh, category, rating, effective_price
     */
    public function getAll($limit = 12, $offset = 0, $categoryIds = null, $keyword = null)
    {
        $where = ['is_active = 1'];
        $params = [];

        if (!empty($categoryIds)) {
            if (!is_array($categoryIds)) $categoryIds = [$categoryIds];
            $catPlaceholders = [];
            foreach ($categoryIds as $i => $cid) {
                $key = 'cat_' . $i;
                $catPlaceholders[] = ':' . $key;
                $params[$key] = $cid;
            }
            $where[] = 'category_id IN (' . implode(',', $catPlaceholders) . ')';
        }

        if ($keyword) {
            $where[] = 'MATCH(name, description) AGAINST(:keyword IN BOOLEAN MODE)';
            $params['keyword'] = $keyword . '*';
        }

        $whereStr = 'WHERE ' . implode(' AND ', $where);

        $sql = "SELECT id, name, slug, price, sale_price,
                       stock, is_featured,
                       effective_price,
                       category_name,
                       primary_image,
                       avg_rating
                FROM v_products
                {$whereStr}
                ORDER BY is_featured DESC, created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Đếm tổng số SP (dùng cho phân trang)
     */
    public function countAll($categoryIds = null, $keyword = null)
    {
        $where = ['is_active = 1'];
        $params = [];

        if (!empty($categoryIds)) {
            if (!is_array($categoryIds)) $categoryIds = [$categoryIds];
            $catPlaceholders = [];
            foreach ($categoryIds as $i => $cid) {
                $key = 'cat_' . $i;
                $catPlaceholders[] = ':' . $key;
                $params[$key] = $cid;
            }
            $where[] = 'category_id IN (' . implode(',', $catPlaceholders) . ')';
        }

        if ($keyword) {
            $where[] = 'MATCH(name, description) AGAINST(:keyword IN BOOLEAN MODE)';
            $params['keyword'] = $keyword . '*';
        }

        $sql = "SELECT COUNT(*) FROM products WHERE " . implode(' AND ', $where);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Lấy chi tiết 1 sản phẩm theo ID (kèm tất cả ảnh và reviews)
     */
    public function getById($id)
    {
        return $this->getBy('id', $id);
    }

    /**
     * Lấy chi tiết 1 sản phẩm theo Slug
     */
    public function getBySlug($slug)
    {
        return $this->getBy('slug', $slug);
    }

    /**
     * Helper chung để lấy sản phẩm theo cột bất kỳ
     */
    private function getBy($column, $value)
    {
        $stmt = $this->db->prepare("SELECT * FROM v_products WHERE {$column} = :val AND is_active = 1 LIMIT 1");
        $stmt->execute(['val' => $value]);
        $product = $stmt->fetch();

        if (!$product)
            return null;

        $id = $product['id'];

        // Lấy tất cả ảnh của SP
        $stmt2 = $this->db->prepare(
            "SELECT * FROM product_images
             WHERE product_id = :id
             ORDER BY is_primary DESC, sort_order ASC"
        );
        $stmt2->execute(['id' => $id]);
        $product['images'] = $stmt2->fetchAll();

        // Lấy reviews đã duyệt
        $stmt3 = $this->db->prepare(
            "SELECT r.*, u.username, u.avatar
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             WHERE r.product_id = :id AND r.is_approved = 1
             ORDER BY r.created_at DESC"
        );
        $stmt3->execute(['id' => $id]);
        $product['reviews'] = $stmt3->fetchAll();

        return $product;
    }

    /**
     * Lấy SP nổi bật (dùng cho trang chủ)
     */
    public function getFeatured($limit = 8)
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, slug, price, sale_price,
                    effective_price, primary_image, avg_rating
             FROM v_products
             WHERE is_active = 1 AND is_featured = 1
             ORDER BY created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ============================================================
    // PHẦN ADMIN (CRUD)
    // ============================================================

    /**
     * Lấy tất cả SP cho admin (bao gồm cả inactive, có tìm kiếm)
     */
    public function getAllAdmin($limit = 15, $offset = 0, $keyword = null)
    {
        $where = '1=1';
        $params = [];

        if ($keyword) {
            $where .= ' AND (name LIKE :keyword_name OR sku LIKE :keyword_sku)';
            $params['keyword_name'] = '%' . $keyword . '%';
            $params['keyword_sku'] = '%' . $keyword . '%';
        }

        $sql = "SELECT id, name, slug, price, sale_price, stock,
                       is_active, is_featured,
                       category_name,
                       primary_image,
                       created_at
                FROM v_products
                WHERE {$where}
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAllAdmin($keyword = null)
    {
        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (name LIKE :keyword_name OR sku LIKE :keyword_sku)';
            $params['keyword_name'] = '%' . $keyword . '%';
            $params['keyword_sku'] = '%' . $keyword . '%';
        }
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Tạo sản phẩm mới (admin)
     */
    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO products
                (category_id, name, slug, description, price, sale_price, stock, sku,
                 is_active, is_featured, meta_title, meta_description)
             VALUES
                (:category_id, :name, :slug, :description, :price, :sale_price, :stock, :sku,
                 :is_active, :is_featured, :meta_title, :meta_description)"
        );
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    /**
     * Cập nhật sản phẩm (admin)
     */
    public function update($id, $data)
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare(
            "UPDATE products SET
                category_id = :category_id, name = :name, slug = :slug,
                description = :description, price = :price, sale_price = :sale_price,
                stock = :stock, sku = :sku, is_active = :is_active,
                is_featured = :is_featured, meta_title = :meta_title,
                meta_description = :meta_description
             WHERE id = :id"
        );
        return $stmt->execute($data);
    }

    /**
     * Xóa sản phẩm (admin) - soft delete bằng cách set is_active = 0
     * Hoặc hard delete nếu muốn xóa hẳn
     */
    public function delete($id)
    {
        // Soft delete: ẩn sản phẩm thay vì xóa hẳn
        $stmt = $this->db->prepare("UPDATE products SET is_active = 0 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Thêm ảnh cho sản phẩm
     */
    public function addImage($productId, $imagePath, $altText = '', $isPrimary = 0)
    {
        // Nếu set primary, bỏ primary của ảnh cũ
        if ($isPrimary) {
            $this->db->prepare(
                "UPDATE product_images SET is_primary = 0 WHERE product_id = :id"
            )->execute(['id' => $productId]);
        }
        $stmt = $this->db->prepare(
            "INSERT INTO product_images (product_id, image_path, alt_text, is_primary)
             VALUES (:product_id, :image_path, :alt_text, :is_primary)"
        );
        return $stmt->execute([
            'product_id' => $productId,
            'image_path' => $imagePath,
            'alt_text' => $altText,
            'is_primary' => $isPrimary,
        ]);
    }

    /**
     * Xóa ảnh của sản phẩm
     */
    public function deleteImage($imageId, $productId)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM product_images WHERE id = :id AND product_id = :product_id"
        );
        return $stmt->execute(['id' => $imageId, 'product_id' => $productId]);
    }

    /**
     * Lấy tất cả danh mục (cho dropdown khi thêm/sửa SP)
     */
    public function getCategories()
    {
        $stmt = $this->db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY parent_id, sort_order");
        return $stmt->fetchAll();
    }

    public function getTopCategories($limit = 5)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM categories WHERE is_active = 1 AND parent_id IS NULL ORDER BY sort_order LIMIT :limit"
        );
        return $stmt->execute(['limit' => $limit]) ? $stmt->fetchAll() : [];
    }

    /**
     * Tạo slug từ tên sản phẩm
     */
    public static function makeSlug($str)
    {
        $str = mb_strtolower(trim($str), 'UTF-8');
        $str = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $str);
        $str = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $str);
        $str = preg_replace('/[ìíịỉĩ]/u', 'i', $str);
        $str = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $str);
        $str = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $str);
        $str = preg_replace('/[ỳýỵỷỹ]/u', 'y', $str);
        $str = preg_replace('/đ/u', 'd', $str);
        $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
        $str = preg_replace('/[\s-]+/', '-', $str);
        return trim($str, '-');
    }

    public function checkInCart($userId, $productId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM cart_items WHERE user_id = :uid AND product_id = :pid");
        $stmt->execute(['uid' => $userId, 'pid' => $productId]);
        return (bool) $stmt->fetchColumn();
    }

    public function hasPurchased($userId, $productId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM order_items oi
            JOIN customer_orders co ON co.id = oi.order_id
            WHERE co.user_id = :uid AND oi.product_id = :pid
              AND co.status = 'delivered'
        ");
        $stmt->execute(['uid' => $userId, 'pid' => $productId]);
        return (bool) $stmt->fetchColumn();
    }

    public function getRelated($categoryId, $excludeId, $limit = 4)
    {
        $stmt = $this->db->prepare("
            SELECT id, name, slug, price, sale_price, effective_price, primary_image
            FROM v_products
            WHERE category_id = :cat AND id != :id AND is_active = 1
            LIMIT " . (int)$limit
        );
        $stmt->execute(['cat' => $categoryId, 'id' => $excludeId]);
        return $stmt->fetchAll();
    }

    public function getByIdForAdmin($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getImages($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM product_images WHERE product_id = :id ORDER BY is_primary DESC, sort_order ASC");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getImageById($imageId)
    {
        $stmt = $this->db->prepare("SELECT * FROM product_images WHERE id = :id");
        $stmt->execute(['id' => $imageId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setPrimaryImage($productId, $imageId)
    {
        // 1. Reset all images of this product to NOT primary
        $stmt1 = $this->db->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = :pid");
        $stmt1->execute(['pid' => $productId]);

        // 2. Set target image as primary
        $stmt2 = $this->db->prepare("UPDATE product_images SET is_primary = 1 WHERE id = :id");
        return $stmt2->execute(['id' => $imageId]);
    }
}
