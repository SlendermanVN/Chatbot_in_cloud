<?php
$keyword = $data['keyword'] ?? '';
$newsList = $data['newsList'] ?? [];
$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
?>

<div class="bg-[#0b0f19] min-h-screen py-10 lg:py-16 text-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-white tracking-tight sm:text-4xl">Tập chí Thể thao</h1>
            <p class="mt-3 max-w-2xl mx-auto text-lg text-gray-400 sm:mt-4">
                Cập nhật những xu hướng, kiến thức luyện tập chuẩn khoa học và tin tức thể thao mới nhất từ cộng đồng
                SportZone.
            </p>
        </div>

        <!-- Filter / Tabs Toolbar & Search -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-10 pb-4 border-b border-gray-800">
            <div
                class="inline-flex bg-gray-900 border border-gray-800 rounded-lg p-1 overflow-x-auto w-full md:w-auto custom-scrollbar">
                <a href="<?= BASE_URL ?>/index.php?route=news"
                    class="px-5 py-2 rounded-md text-sm font-medium bg-primary text-white transition-all whitespace-nowrap shadow-glow">Mới
                    nhất</a>
                <!-- Future features: Categories filter -->
                <a href="#"
                    class="px-5 py-2 rounded-md text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 transition-all whitespace-nowrap">Tập
                    luyện</a>
                <a href="#"
                    class="px-5 py-2 rounded-md text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 transition-all whitespace-nowrap">Dinh
                    dưỡng</a>
            </div>

            <form method="GET" action="<?= BASE_URL ?>/index.php" class="w-full md:w-auto relative group">
                <input type="hidden" name="route" value="news">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i
                        class="fa-solid fa-magnifying-glass text-gray-500 group-focus-within:text-primary transition-colors"></i>
                </div>
                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>"
                    placeholder="Tìm bài viết..."
                    class="block w-full md:w-80 pl-11 pr-4 py-3 border border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all bg-gray-900 text-white placeholder-gray-500 shadow-sm">
            </form>
        </div>

        <?php if (empty($newsList)): ?>
            <!-- Empty State -->
            <div
                class="py-24 text-center bg-gray-900/50 rounded-3xl border border-dashed border-gray-700 flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-regular fa-newspaper text-5xl text-gray-500 drop-shadow"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">Chưa có bài viết nào!</h3>
                <p class="text-gray-400 max-w-md mx-auto">Không tìm thấy tin tức hoặc bài biết đang bị tạm ẩn.
                    <?= !empty($keyword) ? 'Vui lòng thử từ khóa tìm kiếm khác.' : 'Xin quay lại sau nhé.' ?></p>
                <?php if (!empty($keyword)): ?>
                    <a href="<?= BASE_URL ?>/index.php?route=news"
                        class="mt-6 inline-flex items-center bg-primary hover:bg-primary-dark hover:-translate-y-1 hover:shadow-glow text-white font-medium px-6 py-2.5 rounded-lg transition-all duration-300">
                        <i class="fa-solid fa-arrow-left-long mr-2"></i> Quay lại tất cả
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Grid Container: 3 cột -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($newsList as $article): ?>
                    <article
                        class="bg-gray-900 rounded-2xl border border-gray-800 shadow-lg hover:shadow-glow transition-all duration-300 hover:-translate-y-1 overflow-hidden flex flex-col group">
                        <a href="<?= BASE_URL ?>/index.php?route=news_detail&id=<?= $article['id'] ?>"
                            class="block overflow-hidden relative pt-[60%] bg-gray-800">
                            <?php $imgSrc = !empty($article['thumbnail']) ? BASE_URL . '/' . htmlspecialchars($article['thumbnail']) : BASE_URL . '/images/no-image.png'; ?>
                            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($article['title']) ?>" loading="lazy"
                                class="absolute top-0 left-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-90 group-hover:opacity-100">
                        </a>
                        <div class="p-6 flex-grow flex flex-col">
                            <div class="flex items-center text-xs text-gray-500 mb-3 gap-3">
                                <span
                                    class="bg-primary/20 border border-primary/30 text-primary px-3 py-1 rounded-full font-bold uppercase tracking-wider">Tin
                                    Tức</span>
                                <time datetime="<?= date('Y-m-d', strtotime($article['created_at'])) ?>"
                                    class="flex items-center gap-1.5"><i class="fa-regular fa-clock"></i>
                                    <?= date('d/m/Y', strtotime($article['created_at'])) ?></time>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3 line-clamp-2 leading-snug">
                                <a href="<?= BASE_URL ?>/index.php?route=news_detail&slug=<?= $article['slug'] ?>"
                                    class="hover:text-primary transition-colors">
                                    <?= htmlspecialchars($article['title']) ?>
                                </a>
                            </h3>
                            <p class="text-gray-400 text-sm line-clamp-3 mb-5 flex-grow leading-relaxed">
                                <?= htmlspecialchars($article['meta_description']) ?>
                            </p>
                            <div class="mt-auto pt-5 border-t border-gray-800 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-7 h-7 bg-gray-700 text-gray-300 rounded-full flex items-center justify-center font-bold text-xs">
                                        <i class="fa-solid fa-user-pen"></i></div>
                                    <span class="text-xs font-medium text-gray-400">Admin</span>
                                </div>
                                <a href="<?= BASE_URL ?>/index.php?route=news_detail&slug=<?= $article['slug'] ?>"
                                    class="text-primary font-medium text-sm hover:text-white transition-colors inline-flex items-center gap-1.5">
                                    Đọc bài <i
                                        class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination (Phân trang động) -->
            <?php if ($totalPages > 1): ?>
                <div class="mt-16 flex justify-center">
                    <nav class="relative z-0 inline-flex rounded-xl shadow-sm -space-x-px bg-gray-900 border border-gray-800 overflow-hidden"
                        aria-label="Pagination">
                        <!-- Prev btn -->
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= BASE_URL ?>/index.php?route=news&page=<?= $currentPage - 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>"
                                class="relative inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-primary transition-colors border-r border-gray-800">
                                <span class="sr-only">Trang trước</span>
                                <i class="fa-solid fa-chevron-left h-4 w-4 flex items-center justify-center"></i>
                            </a>
                        <?php else: ?>
                            <span
                                class="relative inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 cursor-not-allowed border-r border-gray-800"><i
                                    class="fa-solid fa-chevron-left h-4 w-4 flex items-center justify-center"></i></span>
                        <?php endif; ?>

                        <!-- Page numbers -->
                        <!-- Chỉ hiện max 5 trang cho gọn -->
                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                            <?php if ($i == $currentPage): ?>
                                <span aria-current="page"
                                    class="z-10 bg-primary/20 text-primary relative inline-flex items-center px-5 py-2.5 text-sm font-bold border-r border-gray-800 border-l border-primary/30">
                                    <?= $i ?>
                                </span>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>/index.php?route=news&page=<?= $i ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>"
                                    class="text-gray-400 hover:bg-gray-800 hover:text-white relative inline-flex items-center px-5 py-2.5 text-sm font-medium transition-colors border-r border-gray-800">
                                    <?= $i ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <!-- Next btn -->
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= BASE_URL ?>/index.php?route=news&page=<?= $currentPage + 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>"
                                class="relative inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-primary transition-colors">
                                <span class="sr-only">Trang sau</span>
                                <i class="fa-solid fa-chevron-right h-4 w-4 flex items-center justify-center"></i>
                            </a>
                        <?php else: ?>
                            <span
                                class="relative inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 cursor-not-allowed"><i
                                    class="fa-solid fa-chevron-right h-4 w-4 flex items-center justify-center"></i></span>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>
<style>
    .custom-scrollbar::-webkit-scrollbar {
        height: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #374151;
        border-radius: 10px;
    }
</style>