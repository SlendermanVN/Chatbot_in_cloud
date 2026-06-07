<?php
// ============================================================
// ENTRY POINT - Router (Cổng trung tâm)
// ============================================================

try {

    session_start();
    ob_start();

    // Ensure UTF-8 for all output and internal string handling
    ini_set('default_charset', 'UTF-8');
    if (function_exists('mb_internal_encoding')) {
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
    }
    header('Content-Type: text/html; charset=utf-8');

    // Định nghĩa Hằng số gốc (Rất quan trọng cho file View/Header/Footer)
    if (!defined('BASE_URL')) {
        $baseUrl = getenv('BASE_URL') ?: '';
        define('BASE_URL', rtrim($baseUrl, '/'));
    }

    // Autoload: tự động require class khi cần
    spl_autoload_register(function ($class) {
        $paths = [
            __DIR__ . '/app/Controllers/' . $class . '.php',
            __DIR__ . '/app/Controllers/Admin/' . $class . '.php',
            __DIR__ . '/app/Models/' . $class . '.php',
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                require_once $path;
                return;
            }
        }
    });

    // Khởi tạo DB một lần duy nhất
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/cloud/azure.php';
    // Khởi tạo azure instance nếu có cấu hình
    $cloud = new AzureCloud();

    $db = new Database($cloud, 'sportzone_db');
    $pdo = $db->pdo;

    $db2 = new Database($cloud, 'chatbot_db');
    $pdo2 = $db2->pdo;


    // Lấy route từ query string: ?route=products
    $route = isset($_GET['route']) ? trim($_GET['route']) : 'home';

    // Lightweight health endpoint for container healthchecks
    if ($route === 'health') {
        http_response_code(200);
        echo "OK";
        exit;
    }

    // ✅ BẢO MẬT: Kiểm tra quyền Admin tập trung cho mọi route bắt đầu bằng "admin_"
    if (str_starts_with($route, 'admin_')) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Bạn không có quyền truy cập khu vực quản trị.'];
            header('Location: ' . BASE_URL . '/index.php?route=login');
            exit;
        }
    }

    // ============================================================
    // ROUTE MAP (Đã dọn dẹp và gom nhóm)
    // ============================================================
    switch ($route) {

        // ---------- 1. TRANG CHỦ & CÁC TRANG TĨNH ----------
        case 'home':
        case '':
            $ctrl = new HomeController($pdo);
            $ctrl->index();
            break;
        case 'about':
            $ctrl = new AboutController($pdo);
            $ctrl->index();
            break;
        case 'contact':
            $ctrl = new ContactController($pdo);
            $ctrl->index();
            break;
        case 'contact_submit':
            $ctrl = new ContactController($pdo);
            $ctrl->store();
            break;
        case 'faqs':
            $ctrl = new FaqController($pdo);
            $ctrl->index();
            break;
        case 'chatbot':
            $ctrl = new ChatbotController($pdo, $pdo2);
            $ctrl->index();
            break;

        // ---------- 2. XÁC THỰC (AUTH) ----------
        case 'login':
            $ctrl = new AuthController($pdo);
            $ctrl->login();
            break;
        case 'post_login': // Đã bổ sung
            $ctrl = new AuthController($pdo);
            $ctrl->postLogin();
            break;
        case 'register':
            $ctrl = new AuthController($pdo);
            $ctrl->register();
            break;
        case 'post_register': // Đã bổ sung
            $ctrl = new AuthController($pdo);
            $ctrl->postRegister();
            break;
        case 'logout':
            $ctrl = new AuthController($pdo);
            $ctrl->logout();
            break;

        // ---------- 3. SẢN PHẨM (PRODUCTS) ----------
        case 'products':
            $ctrl = new ProductController($pdo, $cloud);
            $ctrl->index();
            break;
        case 'product_detail':
            $slug = $_GET['slug'] ?? null;
            $id = $_GET['id'] ?? null;
            $target = $slug ?: $id;
            $ctrl = new ProductController($pdo, $cloud);
            $ctrl->detail($target);
            break;
        case 'product_review_store':
            $ctrl = new ProductController($pdo, $cloud);
            $ctrl->storeReview();
            break;

        // ---------- 4. TIN TỨC (NEWS) ----------
        case 'news':
            $ctrl = new NewsController($pdo);
            $ctrl->index();
            break;
        case 'news_detail':
            $slug = $_GET['slug'] ?? null;
            $id = $_GET['id'] ?? null;
            $target = $slug ?: $id;
            $ctrl = new NewsController($pdo);
            $ctrl->detail($target);
            break;
        case 'news_comment_store':
            $ctrl = new NewsController($pdo);
            $ctrl->storeComment();
            break;

        // ---------- 5. GIỎ HÀNG & THANH TOÁN ----------
        case 'cart':
            $ctrl = new CartController($pdo);
            $ctrl->index();
            break;
        case 'cart_add':
            $ctrl = new CartController($pdo);
            $ctrl->add();
            break;
        case 'cart_update':
            $ctrl = new CartController($pdo);
            $ctrl->update();
            break;
        case 'cart_remove':
            $ctrl = new CartController($pdo);
            $ctrl->remove();
            break;
        case 'checkout':
            $ctrl = new CheckoutController($pdo);
            $ctrl->index();
            break;
        case 'checkout_process':
            $ctrl = new CheckoutController($pdo);
            $ctrl->process();
            break;
        case 'profile':
            $ctrl = new ProfileController($pdo);
            $ctrl->index();
            break;
        case 'profile_update':
            $ctrl = new ProfileController($pdo);
            $ctrl->update();
            break;
        case 'change_password':
            $ctrl = new ProfileController($pdo);
            $ctrl->showChangePassword();
            break;
        case 'post_change_password':
            $ctrl = new ProfileController($pdo);
            $ctrl->postChangePassword();
            break;
        case 'orders':
        case 'order_history':
            $ctrl = new OrderController($pdo);
            $ctrl->history();
            break;
        case 'order_detail':
            $ctrl = new OrderController($pdo);
            $ctrl->detail();
            break;
        case 'order_success':
            $ctrl = new OrderController($pdo);
            $ctrl->success();
            break;

        // ---------- 6. Chatbot ----------
        case 'ask_chatbot':
            ob_clean();

            $ctrl = new ChatbotController($pdo, $pdo2);
            $ctrl->askChatbot();

            ob_end_flush();
            break;

        // ============================================================
        // KHU VỰC DÀNH CHO ADMIN
        // ============================================================
        case 'admin_dashboard':
            header('Location: ' . BASE_URL . '/index.php?route=admin_products');
            exit;

        // --- Admin: Products ---
        case 'admin_products':
            $ctrl = new ProductController($pdo, $cloud);
            $ctrl->adminIndex();
            break;
        case 'admin_product_create':
            $ctrl = new ProductController($pdo, $cloud);
            $ctrl->create();
            break;
        case 'admin_product_store':
            $ctrl = new ProductController($pdo, $cloud);
            $ctrl->store();
            break;
        case 'admin_product_edit':
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $ctrl = new ProductController($pdo, $cloud);
            $ctrl->edit($id);
            break;
        case 'admin_product_update':
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $ctrl = new ProductController($pdo, $cloud);
            $ctrl->update($id);
            break;
        case 'admin_product_delete':
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $ctrl = new ProductController($pdo, $cloud);
            $ctrl->delete($id);
            break;
        case 'admin_product_delete_image':
            $ctrl = new ProductController($pdo, $cloud);
            $ctrl->deleteImage();
            break;
        case 'admin_product_set_primary':
            $ctrl = new ProductController($pdo, $cloud);
            $ctrl->setPrimaryImage();
            break;

        // --- Admin: News & Comments ---
        case 'admin_news':
            $ctrl = new AdminNewsController($pdo);
            $ctrl->index();
            break;
        case 'admin_news_create':
            $ctrl = new AdminNewsController($pdo);
            $ctrl->create();
            break;
        case 'admin_news_store':
            $ctrl = new AdminNewsController($pdo);
            $ctrl->store();
            break;
        case 'admin_news_edit':
            $ctrl = new AdminNewsController($pdo);
            $ctrl->edit();
            break;
        case 'admin_news_update':
            $ctrl = new AdminNewsController($pdo);
            $ctrl->update();
            break;
        case 'admin_news_delete':
            $ctrl = new AdminNewsController($pdo);
            $ctrl->delete();
            break;
        case 'admin_comments':
            $ctrl = new AdminCommentController($pdo); // Đã thêm $pdo
            $ctrl->index();
            break;
        case 'admin_comment_approve':
            $ctrl = new AdminCommentController($pdo); // Đã thêm $pdo
            $ctrl->approve();
            break;
        case 'admin_comment_delete':
            $ctrl = new AdminCommentController($pdo); // Đã thêm $pdo
            $ctrl->delete();
            break;

        // --- Admin: Orders, Users, Settings, Contacts ---
        case 'admin_orders':
            $ctrl = new OrderController($pdo);
            $ctrl->index();
            break;
        case 'admin_order_status':
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $ctrl = new OrderController($pdo);
            $ctrl->updateStatus($id);
            break;
        case 'admin_users':
            $ctrl = new UserController($pdo);
            $ctrl->index();
            break;
        case 'admin_settings':
            $ctrl = new SettingController($pdo);
            $ctrl->index();
            break;
        case 'admin_settings_save':
            $ctrl = new SettingController($pdo);
            $ctrl->saveAll();
            break;
        case 'admin_contacts':
            $ctrl = new ContactController($pdo);
            $ctrl->adminIndex();
            break;
        case 'admin_contact_read':
            $ctrl = new ContactController($pdo);
            $ctrl->markAsRead($_GET['id'] ?? 0);
            break;
        case 'admin_contact_reply':
            $ctrl = new ContactController($pdo);
            $ctrl->reply($_GET['id'] ?? 0);
            break;
        case 'admin_contact_delete':
            $ctrl = new ContactController($pdo);
            $ctrl->delete($_GET['id'] ?? 0);
            break;

        // --- FAQs ---
        case 'faq_submit':
            // Xử lý gửi câu hỏi từ người dùng
            $ctrl = new FaqController($pdo);
            $ctrl->submitQuestion();
            break;
        case 'admin_faqs':
            $ctrl = new FaqController($pdo);
            $ctrl->adminIndex();
            break;
        case 'admin_faq_create':
            $ctrl = new FaqController($pdo);
            $ctrl->create();
            break;
        case 'admin_faq_store':
            $ctrl = new FaqController($pdo);
            $ctrl->store();
            break;
        case 'admin_faq_edit':
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $ctrl = new FaqController($pdo);
            $ctrl->edit($id);
            break;
        case 'admin_faq_update':
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $ctrl = new FaqController($pdo);
            $ctrl->update($id);
            break;
        case 'admin_faq_delete':
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $ctrl = new FaqController($pdo);
            $ctrl->delete($id);
            break;
        // --- KHÓA / MỞ KHÓA USER ---
        case 'admin_user_ban':
            $ctrl = new UserController($pdo);
            $ctrl->banUser($_GET['id'] ?? 0);
            break;
        case 'admin_user_unban':
            $ctrl = new UserController($pdo);
            $ctrl->unbanUser($_GET['id'] ?? 0);
            break;

        // ---------- 404 NOT FOUND ----------
        default:
            http_response_code(404);
            echo '<div style="text-align: center; margin-top: 50px; font-family: sans-serif;">';
            echo '<h1 style="color: #ff6600; font-size: 50px;">404</h1>';
            echo '<h2>Trang không tồn tại</h2>';
            echo '<p>Xin lỗi, chức năng bạn tìm kiếm không có hoặc đang được cập nhật.</p>';
            echo '<a href="' . BASE_URL . '/index.php" style="padding: 10px 20px; background: #ff6600; color: #fff; text-decoration: none; border-radius: 5px;">Về trang chủ</a>';
            echo '</div>';
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo '<div style="text-align: center; margin-top: 50px; font-family: sans-serif;">';
    echo '<h1 style="color: #ff6600; font-size: 50px;">500</h1>';
    echo '<h2>Lỗi máy chủ</h2>';
    echo '<p>Xin lỗi, đã xảy ra lỗi. Vui lòng thử lại sau.</p>';
    echo '<a href="' . BASE_URL . '/index.php" style="padding: 10px 20px; background: #ff6600; color: #fff; text-decoration: none; border-radius: 5px;">Về trang chủ</a>';
    echo '</div>';
}
?>