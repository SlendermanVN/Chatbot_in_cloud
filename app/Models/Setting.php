<?php
class Setting
{
    // TODO Anh Đức: Lấy cấu hình và cập nhật giá trị bảng site_settings (setting_key, setting_value). - Xong
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT setting_key, setting_value FROM site_settings");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSetting($key)
    {
        $stmt = $this->db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['setting_value'] : null;
    }

    public function create($key, $value)
    {
        $stmt = $this->db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :value)");
        return $stmt->execute(['key' => $key, 'value' => $value]);
    }

    public function get($key)
    {
        $query = "SELECT setting_value FROM site_settings WHERE setting_key = :key LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['setting_value'] : null;
    }

    public function set($key, $value)
    {
        // Kiểm tra nếu setting đã tồn tại thì update, nếu chưa thì insert
        $query = "INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value2";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['key' => $key, 'value' => $value, 'value2' => $value]);
    }

    public function delete($key)
    {
        $stmt = $this->db->prepare("DELETE FROM site_settings WHERE setting_key = :key");
        return $stmt->execute(['key' => $key]);
    }

}
