<?php
class User
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    // Dùng cho Đăng nhập
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Mapping dữ liệu để không làm gãy logic của AuthController
            // Đổi is_banned của DB thành status (0 = khóa, 1 = hoạt động)
            $user['status'] = $user['is_banned'] == 0 ? 1 : 0;
            // Tạo biến giả password để khớp hàm password_verify
            $user['password'] = $user['password_hash'];
        }
        return $user;
    }

    // Tạo user (Admin dùng)
    public function create($username, $password, $email, $avatar = 'uploads/avatars/default.png', $role = 'member')
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password_hash, email, avatar, role, is_banned) VALUES (:username, :password_hash, :email, :avatar, :role, 0)");
        return $stmt->execute([
            'username' => $username,
            'password_hash' => $hash,
            'email' => $email,
            'avatar' => $avatar,
            'role' => $role
        ]);
    }

    public function getAll($limit = 15, $offset = 0, $keyword = null)
    {
        $where = '1=1';
        $params = [];

        if ($keyword) {
            $where .= ' AND (username LIKE :k1 OR email LIKE :k2 OR full_name LIKE :k3)';
            $params['k1'] = '%' . $keyword . '%';
            $params['k2'] = '%' . $keyword . '%';
            $params['k3'] = '%' . $keyword . '%';
        }

        $stmt = $this->db->prepare("
            SELECT id, username, full_name, email, role, is_banned, created_at
            FROM users
            WHERE {$where}
            ORDER BY id DESC
            LIMIT :limit OFFSET :offset
        ");

        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map lại cho view Admin
        foreach ($users as &$u) {
            $u['status'] = $u['is_banned'] == 0 ? 1 : 0;
        }
        return $users;
    }

    public function countTotal($keyword = null)
    {
        $where = '1=1';
        $params = [];

        if ($keyword) {
            $where .= ' AND (username LIKE :k1 OR email LIKE :k2 OR full_name LIKE :k3)';
            $params['k1'] = '%' . $keyword . '%';
            $params['k2'] = '%' . $keyword . '%';
            $params['k3'] = '%' . $keyword . '%';
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT id, username, email, role, is_banned, created_at, avatar
            FROM users
            WHERE id = :id LIMIT 1
        ");
        $stmt->execute(['id' => (int) $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $user['status'] = $user['is_banned'] == 0 ? 1 : 0;
        }
        return $user;
    }

    public function getFullUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => (int) $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Set Status khóa tài khoản
    public function setStatus($id, $status)
    {
        // status truyền vào: 1 = active -> is_banned = 0
        $is_banned = $status == 1 ? 0 : 1;
        $stmt = $this->db->prepare("UPDATE users SET is_banned = :is_banned WHERE id = :id");
        return $stmt->execute(['is_banned' => $is_banned, 'id' => (int) $id]);
    }

    public function updatePassword($id, $plainPassword)
    {
        $hash = password_hash($plainPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :id");
        return $stmt->execute(['password_hash' => $hash, 'id' => (int) $id]);
    }

    // Update thông tin user (Admin dùng)
    public function update($username, $password, $email, $avatar, $role, $status, $id)
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $is_banned = $status == 1 ? 0 : 1;

        $query = "UPDATE users SET username = :username, password_hash = :password_hash, email = :email, role = :role, is_banned = :is_banned";
        $params = [
            'username' => $username,
            'password_hash' => $hash,
            'email' => $email,
            'role' => $role,
            'is_banned' => $is_banned,
            'id' => $id
        ];

        if ($avatar) {
            $query .= ", avatar = :avatar";
            $params['avatar'] = $avatar;
        }
        $query .= " WHERE id = :id";

        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * TẠO NGƯỜI DÙNG TỪ FORM ĐĂNG KÝ (FRONTEND)
     * Đã sửa lỗi Column Not Found và thiếu biến Username
     */
    public function createUser($data)
    {
        $username = $data['username'] ?? '';

        $sql = "INSERT INTO users (username, full_name, email, password_hash, role, is_banned, created_at) 
                VALUES (:username, :full_name, :email, :password_hash, 'member', 0, NOW())";

        $stmt = $this->db->prepare($sql);

        // Gắn biến chuẩn khớp với Database
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':full_name', $data['full_name'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(':password_hash', $data['password'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function updateProfile($id, $username, $email, $avatar = null, $full_name = null)
    {
        $query = "UPDATE users SET username = :username, email = :email";
        $params = [
            'username' => $username,
            'email' => $email,
            'id' => (int) $id
        ];

        if ($full_name) {
            $query .= ", full_name = :full_name";
            $params['full_name'] = $full_name;
        }

        if ($avatar) {
            $query .= ", avatar = :avatar";
            $params['avatar'] = $avatar;
        }

        $query .= " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }
}