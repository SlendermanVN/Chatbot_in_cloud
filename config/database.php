<?php
class Database
{
    private $cloud_database;
    private $host;
    private $user;
    private $pass;
    private $charset;
    private $port;
    public $pdo;

    public function __construct($cloud, $db)
    {
        $this->cloud_database = $cloud->MySQLDatabase();
        $this->host = $this->cloud_database['host'] ?: 'localhost';
        $this->user = $this->cloud_database['user'] ?: 'root';
        $this->pass = $this->cloud_database['password'] ?: '';
        $this->charset = getenv('DB_CHARSET') ?: 'utf8mb4';
        $this->port = getenv('DB_PORT') ?: '3306';

        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,   // Throw exception khi lỗi SQL
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Fetch array kiểu key=>value
            PDO::ATTR_EMULATE_PREPARES => false,                    // Dùng prepared statement thật (chống SQL Injection)
        ];
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // Không hiện lỗi chi tiết ra ngoài (bảo mật)
            error_log('DB Connection Error: ' . $e->getMessage());
            die('Không thể kết nối database. Vui lòng thử lại sau.');
        }
    }
}
