<?php /* View: 404 - Trang không tìm thấy */ ?>
<div class="min-h-[60vh] flex items-center justify-center bg-[#0b0f19] px-4">
    <div class="max-w-xl w-full text-center">
        <!-- Icon & Title -->
        <div class="mb-8">
            <i class="fa-solid fa-circle-exclamation text-8xl text-primary mb-6 opacity-80"></i>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Trang không tồn tại</h1>
            <div class="h-1 w-20 bg-primary mx-auto rounded-full"></div>
        </div>
        
        <p class="text-gray-400 text-lg mb-10 leading-relaxed">
            Xin lỗi, đường dẫn bạn đang truy cập không tồn tại hoặc đã bị gỡ bỏ khỏi hệ thống SportZone Vietnam.
        </p>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= BASE_URL ?>/index.php" class="bg-primary hover:bg-primary-dark text-white font-bold py-3 px-8 rounded-lg transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-house"></i> Quay về Trang chủ
            </a>
            <a href="<?= BASE_URL ?>/index.php?route=products" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-lg border border-gray-700 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-cart-shopping"></i> Tiếp tục mua sắm
            </a>
        </div>

        <!-- Support Info -->
        <div class="mt-16 text-gray-500 text-sm">
            <p>Nếu bạn cho rằng đây là một lỗi kỹ thuật, vui lòng liên hệ bộ phận hỗ trợ.</p>
        </div>
    </div>
</div>
