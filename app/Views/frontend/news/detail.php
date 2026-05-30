<?php
$news = $data['news'] ?? [];
$setting = $data['setting'] ?? [];
?>

<div class="bg-[#0b0f19] min-h-screen py-10 lg:py-16 text-gray-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <?php if (empty($news)): ?>
            <div class="py-24 text-center bg-gray-900 rounded-3xl border border-gray-800 shadow-xl">
                <i class="fa-solid fa-triangle-exclamation text-6xl text-red-500 mb-6 drop-shadow"></i>
                <h2 class="text-2xl font-bold text-white mb-2">Bài viết không tồn tại</h2>
                <p class="text-gray-400 mb-8 max-w-sm mx-auto">Đường dẫn có thể đã bị thay đổi hoặc bài viết đã được người quản trị gỡ bỏ!</p>
                <a href="<?= BASE_URL ?>/index.php?route=news" class="bg-primary hover:bg-primary-dark hover:-translate-y-1 text-white font-bold py-3 px-8 rounded-lg shadow-glow mt-4 transition-all">
                    Về Trang Tin Tức
                </a>
            </div>
        <?php else: ?>
            <!-- Breadcrumb -->
            <nav class="flex text-sm text-gray-500 mb-8 border-b border-gray-800 pb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="<?= BASE_URL ?>/index.php?route=home" class="hover:text-primary transition-colors inline-flex items-center gap-2">
                            <i class="fa-solid fa-house"></i> Trang chủ
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-[10px] mx-2 text-gray-600"></i>
                            <a href="<?= BASE_URL ?>/index.php?route=news" class="hover:text-primary transition-colors">Tin tức</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-[10px] mx-2 text-gray-600"></i>
                            <span class="text-white font-medium max-w-[150px] sm:max-w-xs truncate"><?= htmlspecialchars($news['title']) ?></span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Khối Bài viết -->
            <article class="bg-gray-900 rounded-3xl shadow-xl overflow-hidden border border-gray-800 mb-12">
                <!-- Hero Cover -->
                <div class="w-full bg-gray-800 relative max-h-[500px] overflow-hidden aspect-[21/9]">
                    <?php $coverImg = !empty($news['thumbnail']) ? BASE_URL . '/' . htmlspecialchars($news['thumbnail']) : BASE_URL . '/images/no-image.png'; ?>
                    <img src="<?= $coverImg ?>" 
                         alt="<?= htmlspecialchars($news['title']) ?>" 
                         class="w-full h-full object-cover">
                    <!-- Blur Overlay bottom gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/40 to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-12 z-10 flex flex-col gap-4">
                        <div class="flex flex-wrap items-center text-sm mb-2 gap-4">
                            <span class="bg-primary text-white border border-primary/50 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider drop-shadow">Tin Nổi Bật</span>
                            <div class="flex items-center gap-2 text-gray-300">
                                <div class="w-6 h-6 bg-gray-700/80 backdrop-blur rounded-full flex items-center justify-center font-bold text-[10px] text-white"><i class="fa-solid fa-crown"></i></div>
                                <span class="font-medium text-white shadow-black drop-shadow">Bởi Admin</span>
                            </div>
                            <time datetime="<?= date('Y-m-d', strtotime($news['created_at'])) ?>" class="flex items-center gap-1.5 text-gray-300 drop-shadow">
                                <i class="fa-regular fa-clock"></i> <?= date('d/m/Y - H:i', strtotime($news['created_at'])) ?>
                            </time>
                        </div>
                        <h1 class="text-3xl md:text-5xl font-black text-white leading-tight drop-shadow-md">
                            <?= htmlspecialchars($news['title']) ?>
                        </h1>
                    </div>
                </div>

                <div class="p-8 md:p-12 space-y-8">
                    <!-- Meta Description Lead -->
                    <?php if (!empty($news['meta_description'])): ?>
                        <p class="text-xl text-primary font-medium italic border-l-4 border-primary pl-5 py-1">
                            <?= htmlspecialchars($news['meta_description']) ?>
                        </p>
                    <?php endif; ?>

                    <!-- Content (Áp dụng prose-invert là bắt buộc để chữ không bị ẩn) -->
                    <div class="prose prose-lg prose-invert max-w-none text-gray-300 leading-relaxed font-sans prose-headings:font-bold prose-headings:text-white prose-a:text-primary hover:prose-a:text-primary-dark prose-img:rounded-xl">
                        <?= $news['content'] ?>
                    </div>
                </div>
                
                <!-- Share Footer -->
                <div class="px-8 md:px-12 py-6 bg-gray-800/30 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-400">Chia sẻ bài viết:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" target="_blank" class="w-10 h-10 rounded-full bg-[#1877f2]/10 text-[#1877f2] border border-[#1877f2]/20 flex items-center justify-center hover:bg-[#1877f2] hover:text-white transition-colors" title="Chia sẻ Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" target="_blank" class="w-10 h-10 rounded-full bg-[#1da1f2]/10 text-[#1da1f2] border border-[#1da1f2]/20 flex items-center justify-center hover:bg-[#1da1f2] hover:text-white transition-colors" title="Chia sẻ Twitter"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                    <a href="<?= BASE_URL ?>/index.php?route=news" class="text-sm font-medium bg-gray-800 border border-gray-700 text-gray-300 hover:text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition-colors inline-flex items-center gap-2">
                        <i class="fa-solid fa-list-ul"></i> Về danh sách tin tức
                    </a>
                </div>
            </article>

            <!-- BÌNH LUẬN -->
            <?php 
            $comments = $data['comments'] ?? []; 
            ?>
            <div class="bg-gray-900 rounded-3xl shadow-xl border border-gray-800 p-8 md:p-12 mb-12">
                <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <i class="fa-regular fa-comments text-primary"></i> Bình luận/Đánh giá (<?= count($comments) ?>)
                </h3>

                <!-- Form Bình luận -->
                <div class="mb-10 bg-gray-800/30 p-6 rounded-2xl border border-gray-800">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg shrink-0 uppercase">
                                <?= substr($_SESSION['username'], 0, 1) ?>
                            </div>
                            <form action="<?= BASE_URL ?>/index.php?route=news_comment_store" method="POST" class="flex-1">
                                <input type="hidden" name="article_id" value="<?= $news['id'] ?>">
                                <textarea name="content" rows="3" required placeholder="Viết bình luận của bạn..." class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-colors placeholder-gray-600 text-sm resize-none mb-3"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-6 rounded-lg transition-all shadow-glow text-sm">
                                        Gửi bình luận
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-6">
                            <i class="fa-solid fa-lock text-3xl text-gray-600 mb-3"></i>
                            <p class="text-gray-400 mb-4">Vui lòng đăng nhập để gửi bình luận.</p>
                            <a href="<?= BASE_URL ?>/index.php?route=login" class="inline-block bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition-colors border border-gray-600 text-sm">
                                Đăng nhập ngay
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Danh sách bình luận -->
                <div class="space-y-6">
                    <?php if (empty($comments)): ?>
                        <div class="text-center text-gray-500 py-8 italic border border-dashed border-gray-700 rounded-xl bg-gray-800/20">
                            Chưa có bình luận nào. Hãy là người đầu tiên bình luận!
                        </div>
                    <?php else: ?>
                        <?php foreach ($comments as $cmt): ?>
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-full bg-gray-700 text-white flex items-center justify-center font-bold text-lg shrink-0 uppercase border border-gray-600">
                                    <?= substr($cmt['username'], 0, 1) ?>
                                </div>
                                <div class="flex-1 bg-gray-800/50 p-4 rounded-2xl rounded-tl-none border border-gray-700/50">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-gray-200"><?= htmlspecialchars($cmt['full_name'] ?: $cmt['username']) ?></h4>
                                        <span class="text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i><?= date('d/m/Y H:i', strtotime($cmt['created_at'])) ?></span>
                                    </div>
                                    <p class="text-gray-300 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($cmt['content'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
</div>
