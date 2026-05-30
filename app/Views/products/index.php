<?php
$categories = $data['categories'] ?? [];
$brands = $data['brands'] ?? [];
$products = $data['products'] ?? [];
$pagination = $data['pagination'] ?? [];

function formatPrice($price)
{
    return number_format($price, 0, ',', '.') . ' VNĐ';
}

function renderCategoryItemCheckbox($item, $selectedIds = [])
{
    $isChecked = in_array($item['id'], $selectedIds) ? 'checked' : '';
    ?>
    <label class="flex items-center gap-3 p-2 rounded hover:bg-gray-800 transition-colors cursor-pointer group">
        <div class="relative flex items-center justify-center">
            <input type="checkbox" id="categories-<?= $item["id"] ?>" name="categories[]" value="<?= $item["id"] ?>" <?= $isChecked ?> class="peer appearance-none w-5 h-5 border-2 border-gray-600 rounded bg-gray-900 checked:bg-primary checked:border-primary transition-all focus:outline-none focus:ring-1 focus:ring-primary focus:ring-offset-1 focus:ring-offset-gray-900 cursor-pointer">
            <i class="fa-solid fa-check absolute text-[10px] text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></i>
        </div>
        <span class="text-gray-300 group-hover:text-white transition-colors cursor-pointer select-none"><?= htmlspecialchars($item["name"]) ?></span>
    </label>
    <?php
}

function renderBrandItemCheckbox($list)
{
    foreach ($list as $item) {
        ?>
        <label class="flex items-center gap-3 p-2 rounded hover:bg-gray-800 transition-colors cursor-pointer group">
            <div class="relative flex items-center justify-center">
                <input type="checkbox" id="brands-<?= $item["id"] ?>" name="brands[]" value="<?= $item["id"] ?>" class="peer appearance-none w-5 h-5 border-2 border-gray-600 rounded bg-gray-900 checked:bg-primary checked:border-primary transition-all focus:outline-none focus:ring-1 focus:ring-primary focus:ring-offset-1 focus:ring-offset-gray-900 cursor-pointer">
                <i class="fa-solid fa-check absolute text-[10px] text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></i>
            </div>
            <span class="text-gray-300 group-hover:text-white transition-colors cursor-pointer select-none"><?= htmlspecialchars($item["name"]) ?></span>
        </label>
        <?php
    }
}

function renderProductCard($product)
{
    $imgSrc = !empty($product['primary_image']) 
        ? BASE_URL . '/' . htmlspecialchars($product['primary_image']) 
        : BASE_URL . '/images/no-image.png'; 
    ?>
    <div class="group relative bg-gray-900 rounded-2xl border border-gray-800 overflow-hidden flex flex-col hover:-translate-y-1 hover:shadow-glow transition-all duration-300">
        <a href="<?= BASE_URL ?>/index.php?route=product_detail&slug=<?= $product['slug'] ?>" class="block relative aspect-square bg-gray-800 overflow-hidden">
            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <?php if ($product['stock'] <= 0): ?>
                <div class="absolute top-3 right-3 bg-red-600/90 backdrop-blur text-white text-xs font-bold px-2 py-1 rounded">Trống kho</div>
            <?php elseif ($product['sale_price']): ?>
                <?php $discount = round((1 - $product['sale_price'] / $product['price']) * 100); ?>
                <div class="absolute top-3 left-3 bg-red-500/90 backdrop-blur text-white text-xs font-bold px-2 py-1 rounded shadow-sm">-<?= $discount ?>%</div>
            <?php endif; ?>
        </a>
        <div class="p-4 sm:p-5 flex flex-col flex-1 relative z-10">
            <p class="text-xs text-primary/80 mb-2 font-bold uppercase tracking-widest"><?= htmlspecialchars($product['category_name']) ?></p>
            <h3 class="font-bold text-gray-200 hover:text-white mb-3 text-[15px] leading-snug line-clamp-2 min-h-[44px] transition-colors">
                <a href="<?= BASE_URL ?>/index.php?route=product_detail&slug=<?= $product['slug'] ?>" class="after:absolute after:inset-0">
                    <?= htmlspecialchars($product['name']) ?>
                </a>
            </h3>
            
            <div class="mt-auto pt-4 border-t border-gray-800/80">
                <div class="min-h-[52px] flex flex-col justify-end">
                    <?php if ($product['sale_price']): ?>
                        <span class="text-white font-black text-lg drop-shadow mb-0.5"><?= formatPrice($product['effective_price']) ?></span>
                        <span class="text-gray-500 line-through text-xs font-medium"><?= formatPrice($product['price']) ?></span>
                    <?php else: ?>
                        <span class="text-white font-black text-lg drop-shadow"><?= formatPrice($product['price']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Custom Add to Cart overlay behavior on hover -->
            <div class="absolute bottom-4 right-4 z-20">
                <?php if ($product['stock'] > 0): ?>
                    <a href="<?= BASE_URL ?>/index.php?route=product_detail&id=<?= $product['id'] ?>" class="w-10 h-10 bg-gray-800 hover:bg-primary text-gray-300 hover:text-white rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-primary shadow-lg" title="Xem & Thêm vào giỏ">
                        <i class="fa-solid fa-cart-plus text-sm"></i>
                    </a>
                <?php else: ?>
                    <div class="w-10 h-10 bg-gray-800/50 text-gray-600 rounded-full flex items-center justify-center border border-gray-800 cursor-not-allowed">
                        <i class="fa-solid fa-store-slash text-sm"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
?>

<?php /* View: Danh sách sản phẩm — URL: ?route=products */ ?>
<div class="products-page bg-[#0b0f19] min-h-screen text-gray-300 py-8 lg:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="mb-10 text-center lg:text-left">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">Sản Phẩm Thể Thao</h1>
            <p class="text-gray-400 max-w-2xl">Trang bị hoàn chỉnh cho đam mê của bạn với các sản phẩm chính hãng, chất lượng cao từ những thương hiệu hàng đầu thế giới.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <!-- Bộ lọc (Sidebar) -->
            <div class="products-sidebar w-full lg:w-72 flex-shrink-0">
                <div class="bg-gray-900 p-6 rounded-2xl shadow-lg border border-gray-800 sticky top-24">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2 pb-4 border-b border-gray-800">
                        <i class="fa-solid fa-filter text-primary"></i> Bộ lọc
                    </h2>
                    <form action="<?= BASE_URL ?>/index.php" method="GET" class="space-y-8">
                        <input type="hidden" name="route" value="products">
                        
                        <!-- Search Keyword -->
                        <div class="filter-group">
                            <label class="block text-sm font-bold text-gray-200 mb-3 uppercase tracking-wider">Từ khóa</label>
                            <div class="relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                                <input type="text" name="search" placeholder="Nhập tên..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="w-full bg-[#0b0f19] border border-gray-700 text-gray-200 rounded-lg pl-10 pr-4 py-2.5 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                            </div>
                        </div>

                        <!-- Lọc danh mục -->
                        <?php if (!empty($categories)): ?>
                            <div class="filter-group">
                                <label class="block text-sm font-bold text-gray-200 mb-3 uppercase tracking-wider">Danh mục</label>
                                <div class="space-y-1 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                    <?php foreach ($categories as $cat) { renderCategoryItemCheckbox($cat, $data['categoryIds'] ?? []); } ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Nút thao tác -->
                        <div class="pt-4 border-t border-gray-800 grid grid-cols-2 gap-3">
                            <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-semibold py-2.5 rounded-lg transition-colors shadow-glow text-sm">
                                Áp dụng
                            </button>
                            <a href="<?= BASE_URL ?>/index.php?route=products" class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-semibold py-2.5 rounded-lg transition-colors text-center text-sm border border-gray-700">
                                Đặt lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách sản phẩm (Main Content) -->
            <div class="products-content flex-1 w-full">
                <div class="flex flex-col sm:flex-row justify-between items-center bg-gray-900 p-4 rounded-xl border border-gray-800 shadow-sm mb-6 gap-4">
                    <p class="text-gray-400 font-medium">Tìm thấy <span class="text-white font-bold"><?= count($products) ?></span> sản phẩm</p>
                    
                    <form action="<?= BASE_URL ?>/index.php" method="GET" class="flex items-center gap-3 w-full sm:w-auto" id="sortForm">
                        <input type="hidden" name="route" value="products">
                        <?php if (isset($_GET['search'])): ?><input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>"><?php endif; ?>
                        
                        <label for="sort_by" class="text-sm font-medium text-gray-400 whitespace-nowrap">Sắp xếp:</label>
                        <div class="relative w-full sm:w-48">
                            <select id="sort_by" name="sort" class="w-full bg-[#0b0f19] border border-gray-700 text-gray-300 rounded-lg pl-3 pr-8 py-2 appearance-none focus:outline-none focus:border-primary transition-colors cursor-pointer text-sm" onchange="document.getElementById('sortForm').submit()">
                                <option value="">Mặc định</option>
                                <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
                                <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
                                <option value="newest" <?= (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none text-xs"></i>
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Sản phẩm sẽ hiển thị ở đây -->
                    <?php if (empty($products)): ?>
                        <div class="col-span-full py-24 text-center bg-gray-900/50 rounded-2xl border border-dashed border-gray-700">
                            <i class="fa-solid fa-box-open text-6xl text-gray-600 border-gray-700 mb-6 drop-shadow"></i>
                            <h3 class="text-2xl font-bold text-gray-300 mb-3">Không có sản phẩm!</h3>
                            <p class="text-gray-500 mb-6 max-w-md mx-auto">Không tìm thấy sản phẩm nào khớp với tiêu chí tìm kiếm của bạn. Hãy thử thay đổi bộ lọc hoặc từ khóa nhé.</p>
                            <a href="<?= BASE_URL ?>/index.php?route=products" class="inline-flex items-center text-primary font-bold hover:text-primary-dark transition-colors px-6 py-2 border border-primary/20 bg-primary/10 rounded-full hover:bg-primary hover:border-primary">
                                Xóa tất cả bộ lọc
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product) {
                            renderProductCard($product);
                        } ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($pagination)): ?>
                    <div class="mt-12 flex justify-center">
                        <nav aria-label="Page navigation">
                            <ul class="inline-flex items-center gap-1">
                                <!-- Previous -->
                                <li>
                                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded bg-gray-900 border border-gray-800 text-gray-500 hover:text-white hover:border-gray-600 transition-colors">
                                        <i class="fa-solid fa-angle-left"></i>
                                    </a>
                                </li>
                                <!-- Current / Pages -->
                                <li>
                                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded bg-primary text-white font-bold shadow-glow border border-primary">1</a>
                                </li>
                                <li>
                                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded bg-gray-900 border border-gray-800 text-gray-400 hover:text-white hover:border-gray-600 transition-colors font-medium">2</a>
                                </li>
                                <!-- Next -->
                                <li>
                                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded bg-gray-900 border border-gray-800 text-gray-500 hover:text-white hover:border-gray-600 transition-colors">
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<style>
/* Custom scrollbar for filter */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #374151;
    border-radius: 20px;
}
</style>