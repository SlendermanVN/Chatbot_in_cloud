<?php
class Cart
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getByUser($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM v_cart_detail WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotal($userId)
    {
        $items = $this->getByUser($userId);
        $total = 0;
        foreach ($items as $item) {
            $total += $item['quantity'] * $item['effective_price'];
        }
        return $total;
    }

    public function clearCart($userId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }

    public function addItem($userId, $productId, $quantity)
    {
        // Truy vấn 1: SELECT kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $stmtCheck = $this->pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = :user_id AND product_id = :product_id");
        $stmtCheck->execute([
            'user_id' => $userId,
            'product_id' => $productId
        ]);
        $existingItem = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            // Truy vấn 2: Nếu có rồi, thực hiện UPDATE số lượng
            $newQuantity = $existingItem['quantity'] + $quantity;
            $stmtUpdate = $this->pdo->prepare("UPDATE cart_items SET quantity = :new_quantity WHERE id = :cart_item_id");
            return $stmtUpdate->execute([
                'new_quantity' => $newQuantity,
                'cart_item_id' => $existingItem['id']
            ]);
        } else {
            // Truy vấn 3: Nếu chưa có, thực hiện INSERT mới
            $stmtInsert = $this->pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
            return $stmtInsert->execute([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }

    public function updateQuantity($userId, $productId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeItem($userId, $productId);
        }
        $stmt = $this->pdo->prepare("
            UPDATE cart_items SET quantity = :quantity
            WHERE user_id = :user_id AND product_id = :product_id
        ");
        return $stmt->execute([
            'quantity' => $quantity,
            'user_id' => $userId,
            'product_id' => $productId
        ]);
    }

    public function removeItem($userId, $productId)
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM cart_items
            WHERE user_id = :user_id AND product_id = :product_id
        ");
        return $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId
        ]);
    }

    public function countItems($userId)
    {
        $stmt = $this->pdo->prepare("SELECT SUM(quantity) FROM cart_items WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }
}
