<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentRoute = $_GET['route'] ?? 'admin_products';
function isActive(string $key, string $current): string {
    $base = preg_replace('/_create|_edit|_store|_update|_delete|_status|_ban|_unban/', '', $current);
    return ($base === $key || $current === $key) ? 'active' : '';
}
?>
<!doctype html>
<html class="no-js" lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= htmlspecialchars($title ?? 'Admin — SportZone') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="<?= BASE_URL ?>/assets/images/icon/favicon.ico">

    <!-- ═══ Srtdash Template CSS ═══ -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/themify-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/metismenujs.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/typography.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/default-css.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/responsive.css">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>

    <!-- Google Fonts: Be Vietnam Pro & Lexend -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&family=Lexend:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- ═══ SportZone Overrides ═══ -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-theme.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-components.css">
</head>
<body>

<div id="preloader"><div class="loader"></div></div>

<!-- ═══════════════════════════════════════════════
     SRTDASH LAYOUT — CẤU TRÚC GỐC
═══════════════════════════════════════════════ -->
<div class="page-container">

    <!-- SIDEBAR (Srtdash) -->
    <div class="sidebar-menu">
        <div class="sidebar-header">
            <div class="logo">
                <a href="<?= BASE_URL ?>/index.php?route=admin_products">
                    <span class="sz-logo-wrapper">
                        <span class="sz-logo-icon"><i class="fa-solid fa-bolt"></i></span>
                        <span class="sz-logo-text">SportZone</span>
                    </span>
                </a>
            </div>
        </div>

        <div class="main-menu">
            <div class="menu-inner">
                <nav>
                    <ul class="metismenu" id="menu">

                        <li class="<?= isActive('admin_products', $currentRoute) ?>">
                            <a href="<?= BASE_URL ?>/index.php?route=admin_products">
                                <i class="fa-solid fa-box-open"></i>
                                <span>Sản phẩm</span>
                            </a>
                        </li>

                        <li class="<?= isActive('admin_orders', $currentRoute) ?>">
                            <a href="<?= BASE_URL ?>/index.php?route=admin_orders">
                                <i class="fa-solid fa-receipt"></i>
                                <span>Đơn hàng</span>
                            </a>
                        </li>

                        <li class="<?= isActive('admin_news', $currentRoute) ?>">
                            <a href="<?= BASE_URL ?>/index.php?route=admin_news">
                                <i class="fa-solid fa-newspaper"></i>
                                <span>Tin tức</span>
                            </a>
                        </li>

                        <li class="<?= isActive('admin_comments', $currentRoute) ?>">
                            <a href="<?= BASE_URL ?>/index.php?route=admin_comments">
                                <i class="fa-solid fa-comments"></i>
                                <span>Bình luận/Đánh giá</span>
                            </a>
                        </li>

                        <li class="<?= isActive('admin_contacts', $currentRoute) ?>">
                            <a href="<?= BASE_URL ?>/index.php?route=admin_contacts">
                                <i class="fa-solid fa-envelope-open-text"></i>
                                <span>Liên hệ</span>
                            </a>
                        </li>

                        <li class="<?= isActive('admin_faqs', $currentRoute) ?>">
                            <a href="<?= BASE_URL ?>/index.php?route=admin_faqs">
                                <i class="fa-solid fa-circle-question"></i>
                                <span>Hỏi &amp; Đáp</span>
                            </a>
                        </li>

                        <li class="<?= isActive('admin_users', $currentRoute) ?>">
                            <a href="<?= BASE_URL ?>/index.php?route=admin_users">
                                <i class="fa-solid fa-users"></i>
                                <span>Người dùng</span>
                            </a>
                        </li>

                        <li class="<?= isActive('admin_settings', $currentRoute) ?>">
                            <a href="<?= BASE_URL ?>/index.php?route=admin_settings">
                                <i class="fa-solid fa-sliders"></i>
                                <span>Cài đặt hệ thống</span>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </div>
    </div><!-- /sidebar-menu -->

    <!-- MAIN CONTENT (Srtdash) -->
    <div class="main-content">

        <!-- HEADER AREA (Srtdash) -->
        <div class="header-area">
            <div class="row align-items-center" style="margin:0;width:100%;">

                <!-- Left: hamburger toggle -->
                <div class="col clearfix">
                    <div class="nav-btn pull-left">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>

                <!-- Right: user dropdown — col-auto = chỉ rộng bằng nội dung, sát phải -->
                <div class="col-auto" style="padding-right:12px;">
                    <div class="dropdown sz-user-dropdown">
                        <a href="#" class="sz-user-trigger"
                           data-bs-toggle="dropdown"
                           aria-expanded="false">
                            <div class="sz-topbar-avatar">
                                <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                            </div>
                            <span class="sz-topbar-username">
                                <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>
                            </span>
                            <i class="fa-solid fa-chevron-down sz-topbar-caret"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end sz-user-dropdown-menu">
                            <div class="sz-dropdown-header">
                                <div class="sz-dropdown-avatar">
                                    <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="sz-dropdown-name"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div>
                                    <div class="sz-dropdown-role">Super Administrator</div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item sz-dropdown-item"
                               href="<?= BASE_URL ?>/index.php?route=profile">
                                <i class="fa-solid fa-user-gear"></i> Hồ sơ cá nhân
                            </a>
                            <a class="dropdown-item sz-dropdown-item"
                               href="<?= BASE_URL ?>/index.php?route=change_password">
                                <i class="fa-solid fa-lock"></i> Đổi mật khẩu
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item sz-dropdown-item sz-dropdown-danger"
                               href="<?= BASE_URL ?>/index.php?route=logout">
                                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /header-area -->


        <!-- MAIN CONTENT INNER (Srtdash) -->
        <div class="main-content-inner">

            <!-- Flash Message -->
            <?php if (!empty($_SESSION['flash'])): ?>
                <?php
                    $flashType    = $_SESSION['flash']['type'] === 'error' ? 'danger' : $_SESSION['flash']['type'];
                    $flashMessage = $_SESSION['flash']['message'];
                    unset($_SESSION['flash']);
                    $flashIconMap = [
                        'success' => 'fa-circle-check',
                        'danger'  => 'fa-circle-exclamation',
                        'warning' => 'fa-triangle-exclamation',
                        'info'    => 'fa-circle-info',
                    ];
                    $flashIcon = $flashIconMap[$flashType] ?? 'fa-circle-exclamation';
                ?>
                <div class="alert alert-<?= $flashType ?> alert-dismissible fade show sz-flash-alert" role="alert">
                    <i class="fa-solid <?= $flashIcon ?>"></i>
                    <?= htmlspecialchars($flashMessage) ?>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- PAGE CONTENT -->
