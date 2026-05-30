<?php
// Đảm bảo BASE_URL luôn tồn tại để tránh gãy link
if (!defined('BASE_URL')) {
    define('BASE_URL', '/main-repo/public');
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($title) ? htmlspecialchars($title) . ' | SportZone Vietnam' : (isset($data['title']) ? htmlspecialchars($data['title']) . ' | SportZone Vietnam' : 'SportZone Vietnam - High Performance') ?>
    </title>

    <!-- SEO Meta Tags -->
    <?php if (!empty($metaDescription)): ?>
        <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <?php else: ?>
        <meta name="description"
            content="SportZone Vietnam - Chuyên cung cấp dụng cụ thể thao chính hãng, chất lượng cao. Giày chạy bộ, bóng đá, bơi lội và nhiều hơn nữa.">
    <?php endif; ?>
    <?php if (!empty($metaKeywords)): ?>
        <meta name="keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
    <?php endif; ?>
    <!-- Open Graph -->
    <meta property="og:site_name" content="SportZone Vietnam">
    <meta property="og:type" content="website">
    <meta property="og:title"
        content="<?= isset($title) ? htmlspecialchars($title) . ' | SportZone Vietnam' : 'SportZone Vietnam' ?>">
    <?php if (!empty($metaDescription)): ?>
        <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <?php endif; ?>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,900;1,800;1,900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#ff6600', // Cam Neon
                        'primary-dark': '#cc5200',
                        surface: '#121212',
                        background: '#0a0a0a',
                    },
                    boxShadow: {
                        'glow': '0 0 15px rgba(255, 102, 0, 0.3)',
                    }
                }
            }
        }
    </script>
    <style>
        /* Vẽ nền lưới (Grid Background) */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b0f19;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 30px 30px;
            color: #e5e7eb;
        }

        .hover-lift {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        /* Hiệu ứng gạch chân cho menu active/hover */
        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #ff6600;
            transition: width 0.3s;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen">

    <header class="bg-[#0b0f19]/90 backdrop-blur-md sticky top-0 z-50 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-[80px]">

                <div class="flex-shrink-0 flex items-center">
                    <a href="<?= BASE_URL ?>/index.php"
                        class="text-2xl md:text-3xl font-black italic tracking-widest text-primary hover:text-white transition-colors duration-300">
                        SportZone
                    </a>
                </div>

                <nav class="hidden md:flex space-x-8 mt-2">
                    <a href="<?= BASE_URL ?>/index.php"
                        class="nav-link text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-white transition-colors">Trang
                        chủ</a>
                    <a href="<?= BASE_URL ?>/index.php?route=products"
                        class="nav-link text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-white transition-colors">Sản
                        phẩm</a>
                    <a href="<?= BASE_URL ?>/index.php?route=news"
                        class="nav-link text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-white transition-colors">Tin
                        tức</a>
                    <a href="<?= BASE_URL ?>/index.php?route=about"
                        class="nav-link text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-white transition-colors">Giới
                        thiệu</a>
                    <a href="<?= BASE_URL ?>/index.php?route=faqs"
                        class="nav-link text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-white transition-colors">FAQ</a>
                    <a href="<?= BASE_URL ?>/index.php?route=contact"
                        class="nav-link text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-white transition-colors">Liên
                        hệ</a>
                </nav>

                <div class="hidden md:flex items-center space-x-6">

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= BASE_URL ?>/index.php?route=chatbot"
                            class="text-primary hover:text-white relative transition-colors group">
                            <i class="fa-solid fa-robot text-xl group-hover:scale-110 transition-transform"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= BASE_URL ?>/index.php?route=cart"
                            class="text-primary hover:text-white relative transition-colors group">
                            <i class="fa-solid fa-cart-shopping text-xl group-hover:scale-110 transition-transform"></i>
                            <span id="cartCountSpan"
                                class="absolute -top-2 -right-2 bg-white text-primary text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">
                                <?= $_SESSION['cart_count'] ?? 0 ?>
                            </span>
                        </a>

                        <div class="h-5 w-px bg-gray-700 mx-1"></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="relative group cursor-pointer flex items-center gap-3">
                            <a href="<?= BASE_URL ?>/index.php?route=profile"
                                class="text-gray-300 font-medium flex items-center gap-2 hover:text-primary transition-colors">
                                <?php
                                $avatar = $_SESSION['avatar'] ?? '';
                                $username = $_SESSION['username'] ?? 'User';
                                $avatarUrl = !empty($avatar) && $avatar !== 'uploads/avatars/default.png'
                                    ? BASE_URL . '/' . htmlspecialchars($avatar)
                                    : "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=ff6600&color=fff&bold=true";
                                ?>
                                <img src="<?= $avatarUrl ?>" alt="Avatar"
                                    class="w-8 h-8 rounded-full border border-gray-700 object-cover">
                                <span
                                    class="text-sm font-bold tracking-wide"><?= htmlspecialchars($_SESSION['username']) ?></span>
                            </a>

                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <a href="<?= BASE_URL ?>/index.php?route=admin_dashboard"
                                    class="text-[10px] text-yellow-500 border border-yellow-500/50 hover:bg-yellow-500 hover:text-black font-bold px-2 py-1 rounded tracking-widest uppercase transition-colors">Admin</a>
                            <?php endif; ?>

                            <a href="<?= BASE_URL ?>/index.php?route=logout"
                                class="px-3 py-1.5 border border-gray-700 rounded text-xs tracking-wider uppercase text-gray-400 hover:text-white hover:border-primary transition-all font-bold ml-2">Đăng
                                xuất</a>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-4">
                            <a href="<?= BASE_URL ?>/index.php?route=login"
                                class="text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-primary transition-colors">
                                Đăng nhập
                            </a>
                            <a href="<?= BASE_URL ?>/index.php?route=register"
                                class="px-5 py-2 bg-primary hover:bg-primary-dark text-white rounded text-xs tracking-wider uppercase font-bold transition-all shadow-glow">
                                Đăng ký
                            </a>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button"
                        class="text-primary hover:text-white focus:outline-none transition-colors">
                        <i class="fa-solid fa-bars text-2xl" id="menu-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu"
            class="hidden md:hidden bg-[#0b0f19] border-t border-gray-800 animate-slideDown overflow-hidden">
            <div class="px-4 pt-4 pb-6 space-y-2">
                <a href="<?= BASE_URL ?>/index.php"
                    class="block px-4 py-3 text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl transition-all">Trang
                    chủ</a>
                <a href="<?= BASE_URL ?>/index.php?route=products"
                    class="block px-4 py-3 text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl transition-all">Sản
                    phẩm</a>
                <a href="<?= BASE_URL ?>/index.php?route=news"
                    class="block px-4 py-3 text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl transition-all">Tin
                    tức</a>
                <a href="<?= BASE_URL ?>/index.php?route=about"
                    class="block px-4 py-3 text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl transition-all">Giới
                    thiệu</a>
                <a href="<?= BASE_URL ?>/index.php?route=faqs"
                    class="block px-4 py-3 text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl transition-all">FAQ</a>
                <a href="<?= BASE_URL ?>/index.php?route=contact"
                    class="block px-4 py-3 text-sm font-bold tracking-widest uppercase text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl transition-all">Liên
                    hệ</a>

                <div class="pt-4 border-t border-gray-800 mt-4 flex flex-col gap-3">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center gap-3 px-4 py-2 mb-2">
                            <img src="<?= $avatarUrl ?>" alt="Avatar"
                                class="w-10 h-10 rounded-full border border-gray-700 object-cover">
                            <div>
                                <div class="text-white font-bold text-sm"><?= htmlspecialchars($_SESSION['username']) ?>
                                </div>
                                <div class="text-gray-500 text-[10px] uppercase tracking-widest">
                                    <?= $_SESSION['role'] ?? 'MEMBER' ?>
                                </div>
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>/index.php?route=cart"
                            class="flex items-center justify-between px-4 py-3 bg-primary/10 text-primary rounded-xl font-bold text-sm">
                            <span>Giỏ hàng</span>
                            <span
                                class="bg-primary text-white text-[10px] px-2 py-0.5 rounded-full"><?= $_SESSION['cart_count'] ?? 0 ?></span>
                        </a>
                        <a href="<?= BASE_URL ?>/index.php?route=chatbot"
                            class="block px-4 py-3 text-sm font-bold text-gray-400 hover:text-white rounded-xl">
                            Trợ lý ảo
                        </a>
                        <a href="<?= BASE_URL ?>/index.php?route=profile"
                            class="block px-4 py-3 text-sm font-bold text-gray-400 hover:text-white rounded-xl">Hồ sơ cá
                            nhân</a>
                        <a href="<?= BASE_URL ?>/index.php?route=logout"
                            class="block px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-500/10 rounded-xl">Đăng
                            xuất</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/index.php?route=login"
                            class="block w-full text-center py-3 text-sm font-bold tracking-widest uppercase text-gray-400 border border-gray-800 rounded-xl">Đăng
                            nhập</a>
                        <a href="<?= BASE_URL ?>/index.php?route=register"
                            class="block w-full text-center py-3 text-sm font-bold tracking-widest uppercase bg-primary text-white rounded-xl shadow-glow">Đăng
                            ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const menuBtn = document.getElementById('mobile-menu-button');
                const mobileMenu = document.getElementById('mobile-menu');
                const menuIcon = document.getElementById('menu-icon');

                menuBtn.addEventListener('click', function () {
                    mobileMenu.classList.toggle('hidden');
                    const isHidden = mobileMenu.classList.contains('hidden');

                    // Toggle icon between bars and xmark
                    menuIcon.classList.toggle('fa-bars', isHidden);
                    menuIcon.classList.toggle('fa-xmark', !isHidden);
                });
            });
        </script>

        <style>
            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-slideDown {
                animation: slideDown 0.3s ease-out forwards;
            }
        </style>
    </header>

    <main class="flex-grow w-full">
        <!-- Global Flash Messages -->
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="max-w-7xl mx-auto px-4 mt-6">
                <?php
                $fType = $_SESSION['flash']['type'] ?? 'success';
                $fMsg = $_SESSION['flash']['message'] ?? '';
                unset($_SESSION['flash']);

                $bgColor = $fType === 'success' ? 'bg-green-500/10 border-green-500/30 text-green-400' :
                    ($fType === 'error' || $fType === 'danger' ? 'bg-red-500/10 border-red-500/30 text-red-400' : 'bg-yellow-500/10 border-yellow-500/30 text-yellow-400');
                $icon = $fType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
                ?>
                <div class="<?= $bgColor ?> border px-6 py-4 rounded-2xl flex items-center gap-3 shadow-lg transition-all">
                    <i class="fa-solid <?= $icon ?> text-lg"></i>
                    <span class="font-bold text-sm"><?= htmlspecialchars($fMsg) ?></span>
                </div>
            </div>
        <?php endif; ?>