<?php if (!defined('BASE_URL')) define('BASE_URL', '/main-repo/public'); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | SportZone Vietnam' : 'SportZone Vietnam' ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { primary: '#ff6600', 'primary-dark': '#cc5200' },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b0f19;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 32px 32px;
            color: #e5e7eb;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">

<!-- Minimal Navbar (dùng chung cho Landing, Login, Register) -->
<header class="fixed top-0 left-0 right-0 z-50 bg-[#0b0f19]/80 backdrop-blur-lg border-b border-white/5">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <a href="<?= BASE_URL ?>/index.php" class="text-2xl font-black italic tracking-widest text-primary">
            SportZone
        </a>
        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>/index.php?route=login"
               class="text-sm font-bold text-gray-400 hover:text-white transition-colors px-4 py-2">
                Đăng nhập
            </a>
            <a href="<?= BASE_URL ?>/index.php?route=register"
               class="text-sm font-bold bg-primary hover:bg-primary-dark text-white px-5 py-2 rounded-xl transition-all shadow-lg shadow-primary/30 hover:-translate-y-0.5">
                Đăng ký miễn phí
            </a>
        </div>
    </div>
</header>

<!-- Padding for fixed header -->
<div class="pt-16 flex-1 flex flex-col">
