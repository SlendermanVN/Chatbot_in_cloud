<?php
class Order
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createOrder($userId, $recipientName, $recipientPhone, $shippingAddress, $note, $totalAmount, $items, $cartModel)
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                INSERT INTO customer_orders 
                    (user_id, recipient_name, recipient_phone, shipping_address, note, total_amount, status) 
                VALUES 
                    (:user_id, :recipient_name, :recipient_phone, :shipping_address, :note, :total_amount, 'pending')
            ");
            $stmt->execute([
                'user_id' => $userId,
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'shipping_address' => $shippingAddress,
                'note' => $note,
                'total_amount' => $totalAmount
            ]);
            $orderId = $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, price_at_order, quantity) 
                VALUES (:order_id, :product_id, :product_name, :price_at_order, :quantity)
            ");
            foreach ($items as $item) {
                $stmtItem->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'price_at_order' => $item['effective_price'],
                    'quantity' => $item['quantity']
                ]);
            }
            
            // Xóa giỏ hàng
            $cartModel->clearCart($userId);

            $this->pdo->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }



    public function getOrders($limit = null, $offset = null, $status = null, $keyword = null)
    {
        $where = '1=1';
        $params = [];

        if ($status && in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
            $where .= ' AND co.status = :status';
            $params['status'] = $status;
        }

        if ($keyword) {
            $where .= ' AND (co.id = :k1 OR co.recipient_name LIKE :k2)';
            $params['k1'] = (int)$keyword;
            $params['k2'] = '%' . $keyword . '%';
        }

        $sql = "SELECT co.*, u.username, u.email
                FROM customer_orders co
                LEFT JOIN users u ON u.id = co.user_id
                WHERE {$where}
                ORDER BY co.created_at DESC";
                
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getOrdersByUser($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM customer_orders WHERE user_id = :user_id ORDER BY created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT co.*, u.username, u.email, u.phone AS user_phone
            FROM customer_orders co
            LEFT JOIN users u ON u.id = co.user_id
            WHERE co.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($orderId)
    {
        $stmt = $this->pdo->prepare("
            SELECT oi.*, p.name AS product_name, pi.image_path as product_image
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
            WHERE oi.order_id = :order_id
        ");
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE customer_orders SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function countOrders($status = null, $keyword = null)
    {
        $where = '1=1';
        $params = [];

        if ($status && in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
            $where .= ' AND status = :status';
            $params['status'] = $status;
        }

        if ($keyword) {
            $where .= ' AND (id = :k1 OR recipient_name LIKE :k2)';
            $params['k1'] = (int)$keyword;
            $params['k2'] = '%' . $keyword . '%';
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM customer_orders WHERE {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function countOrdersByStatus()
    {
        $stmt = $this->pdo->query("SELECT status, COUNT(*) AS cnt FROM customer_orders GROUP BY status");
        $stats = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $stats[$row['status']] = $row['cnt'];
        }
        return $stats;
    }
}
