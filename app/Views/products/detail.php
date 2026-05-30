<?php
$product = $data['product'] ?? [];
$relatedProducts = $data['relatedProducts'] ?? [];
$reviews = $data['reviews'] ?? [];
?>

<?php /* View: Chi tiết sản phẩm — URL: ?route=product_detail&id=... */ ?>
<div class="product-detail-page bg-[#0b0f19] min-h-screen text-gray-300 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex text-sm text-gray-500 mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="<?= BASE_URL ?>/index.php" class="hover:text-primary transition-colors">
                        <i class="fa-solid fa-house mr-2"></i>Trang chủ
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
                        <a href="<?= BASE_URL ?>/index.php?route=products" class="hover:text-primary transition-colors">Sản phẩm</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
                        <span class="text-gray-300 truncate max-w-[200px]"><?= htmlspecialchars($product['name'] ?? 'Chi tiết') ?></span>
                    </div>
                </li>
            </ol>
        </nav>

        <?php if (empty($product)): ?>
            <div class="text-center py-20 bg-gray-900 rounded-2xl shadow-lg border border-gray-800">
                <i class="fa-solid fa-triangle-exclamation text-6xl text-yellow-500/80 mb-4 drop-shadow"></i>
                <h2 class="text-2xl font-bold text-white mb-2">Không tìm thấy sản phẩm</h2>
                <p class="text-gray-400 mb-6">Sản phẩm này không tồn tại hoặc đã bị ẩn.</p>
                <a href="<?= BASE_URL ?>/index.php?route=products" class="btn btn-primary bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded shadow-glow transition-all">Quay lại danh sách</a>
            </div>
        <?php else: ?>

            <div class="bg-gray-900 rounded-2xl shadow-lg border border-gray-800 overflow-hidden mb-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:gap-8 p-6 lg:p-10">
                    
                    <!-- Phần Hình ảnh (Left) -->
                    <div class="product-gallery space-y-4">
                        <?php $mainImg = !empty($product['images']) ? $product['images'][0]['image_path'] : (!empty($product['primary_image']) ? $product['primary_image'] : ''); ?>
                        <div class="relative rounded-xl overflow-hidden bg-gray-800 aspect-square border border-gray-800 group">
                            <img id="mainProductImg" src="<?= $mainImg ? BASE_URL . '/' . htmlspecialchars($mainImg) : BASE_URL . '/images/no-image.png' ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            
                            <?php if ($product['stock'] <= 0): ?>
                                <div class="absolute top-4 right-4 bg-red-600/90 backdrop-blur text-white text-sm font-bold px-3 py-1.5 rounded-lg shadow-lg border border-red-500">Hết hàng</div>
                            <?php elseif ($product['sale_price']): ?>
                                <?php $discount = round((1 - $product['sale_price'] / $product['price']) * 100); ?>
                                <div class="absolute top-4 left-4 bg-red-500/90 backdrop-blur text-white font-bold px-3 py-1.5 rounded-lg shadow-lg border border-red-400">Giảm <?= $discount ?>%</div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Thumbnail images -->
                        <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
                            <div class="grid grid-cols-4 sm:grid-cols-5 gap-3">
                                <?php foreach ($product['images'] as $img): ?>
                                    <div class="cursor-pointer rounded-lg overflow-hidden border-2 border-transparent hover:border-primary opacity-70 hover:opacity-100 transition-all duration-200 aspect-square bg-gray-800" onclick="document.getElementById('mainProductImg').src = '<?= BASE_URL ?>/<?= htmlspecialchars($img['image_path']) ?>'">
                                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($img['image_path']) ?>" alt="" class="w-full h-full object-cover">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Phần Thông tin (Right) -->
                    <div class="product-info flex flex-col pt-6 md:pt-0">
                        <div class="mb-2">
                            <a href="<?= BASE_URL ?>/index.php?route=products&categories[]=<?= $product['category_id'] ?>" class="text-sm text-primary hover:text-primary-dark font-semibold uppercase tracking-wider transition-colors inline-block bg-primary/10 px-3 py-1 rounded-full border border-primary/20">
                                <?= htmlspecialchars($product['category_name']) ?>
                            </a>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-white leading-tight mb-4"><?= htmlspecialchars($product['name']) ?></h1>
                        
                        <!-- Đánh giá snippet -->
                        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-800">
                            <div class="flex items-center text-yellow-400 text-sm">
                                <?php
                                $rating = $product['avg_rating'] ?: 5;
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $rating ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star text-gray-600"></i>';
                                }
                                ?>
                                <span class="text-gray-400 ml-2 font-medium cursor-pointer hover:text-primary transition-colors" onclick="document.querySelectorAll('.tab-btn')[1].click(); document.getElementById('description').scrollIntoView({behavior: 'smooth'});">(<?= count($reviews) ?> nhận xét)</span>
                            </div>
                            <div class="h-4 w-px bg-gray-700"></div>
                            <div class="text-sm font-medium">
                                <?php if ($product['stock'] > 0): ?>
                                    <span class="text-emerald-400"><i class="fa-solid fa-check-circle mr-1"></i>Còn <?= $product['stock'] ?> SP</span>
                                <?php else: ?>
                                    <span class="text-red-400"><i class="fa-solid fa-xmark-circle mr-1"></i>Hết kho</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Giá -->
                        <div class="mb-8">
                            <?php if ($product['sale_price']): ?>
                                <div class="flex items-end gap-3 mb-2">
                                    <span class="text-4xl font-black text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.4)]"><?= number_format($product['effective_price'], 0, ',', '.') ?> VNĐ</span>
                                    <span class="text-xl line-through text-gray-500 font-medium pb-1"><?= number_format($product['price'], 0, ',', '.') ?> VNĐ</span>
                                </div>
                            <?php else: ?>
                                <div class="text-4xl font-black text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.4)] mb-2">
                                    <?= number_format($product['price'], 0, ',', '.') ?> VNĐ
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Mô tả ngắn -->
                        <div class="text-gray-400 text-base leading-relaxed mb-8 prose prose-invert max-w-none">
                            <?= nl2br(htmlspecialchars(substr($product['description'] ?? 'Đang cập nhật', 0, 300) . '...')) ?>
                        </div>

                        <!-- Form thêm vào giỏ -->
                        <div class="mt-auto p-5 bg-gray-800/40 rounded-xl border border-gray-800">
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <div class="text-center py-2">
                                    <p class="text-sm text-gray-400 mb-4">Vui lòng đăng nhập để bắt đầu mua sắm.</p>
                                    <a href="<?= BASE_URL ?>/index.php?route=login" class="block w-full bg-primary/20 hover:bg-primary/30 text-primary font-bold py-3.5 rounded-lg border border-primary/30 transition-all flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-right-to-bracket text-lg"></i> Đăng nhập ngay
                                    </a>
                                </div>
                            <?php elseif ($product['stock'] > 0): ?>
                                <form id="addToCartForm" class="flex flex-col sm:flex-row gap-4">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    
                                    <!-- Số lượng -->
                                    <div class="flex items-center justify-between sm:justify-start bg-gray-900 border border-gray-700 rounded-lg p-1 w-full sm:w-auto shrink-0 select-none">
                                        <button type="button" class="w-10 h-10 flex text-lg font-bold items-center justify-center text-gray-400 hover:text-white hover:bg-gray-800 rounded transition-colors" onclick="var qty = document.getElementById('qty'); if(qty.value > 1) qty.value--">-</button>
                                        <input type="number" id="qty" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="w-14 h-10 text-center font-bold bg-transparent text-white focus:outline-none border-none ring-0 p-0 appearance-none">
                                        <button type="button" class="w-10 h-10 flex text-lg font-bold items-center justify-center text-gray-400 hover:text-white hover:bg-gray-800 rounded transition-colors" onclick="var qty = document.getElementById('qty'); qty.value++">+</button>
                                    </div>
                                    
                                    <button type="button" id="btnAddToCart" class="flex-1 bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-glow flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-cart-plus text-xl"></i> Thêm vào giỏ
                                    </button>
                                </form>
                                <div id="cartMessage" class="mt-3 text-sm font-medium hidden"></div>
                            <?php else: ?>
                                <button type="button" class="w-full bg-gray-800 text-gray-500 font-bold py-4 rounded-lg cursor-not-allowed border border-gray-700 flex items-center justify-center gap-2" disabled>
                                    <i class="fa-solid fa-store-slash text-xl"></i> Tạm hết hàng
                                </button>
                                <p class="text-sm text-center text-gray-500 mt-3">Vui lòng quay lại sau hoặc liên hệ hỗ trợ.</p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Features list -->
                        <div class="grid grid-cols-2 gap-4 mt-8 pt-6 border-t border-gray-800 text-sm">
                            <div class="flex items-center text-gray-400"><i class="fa-solid fa-shield-halved text-gray-500 text-lg mr-3"></i> Bảo hành chính hãng</div>
                            <div class="flex items-center text-gray-400"><i class="fa-solid fa-rotate-left text-gray-500 text-lg mr-3"></i> 30 ngày đổi trả</div>
                            <div class="flex items-center text-gray-400"><i class="fa-solid fa-truck-fast text-gray-500 text-lg mr-3"></i> Giao hàng siêu tốc</div>
                            <div class="flex items-center text-gray-400"><i class="fa-solid fa-headset text-gray-500 text-lg mr-3"></i> Hỗ trợ 24/7</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <div class="bg-gray-900 rounded-2xl shadow-lg border border-gray-800 overflow-hidden mb-12">
                <div class="flex border-b border-gray-800 bg-gray-950/50">
                    <button class="tab-btn active px-8 py-4 font-bold text-white border-b-2 border-primary bg-gray-800/30 transition-all" onclick="openTab(event, 'description')">
                        Mô tả sản phẩm
                    </button>
                    <button class="tab-btn px-8 py-4 font-bold text-gray-500 hover:text-white transition-colors border-b-2 border-transparent transition-all" onclick="openTab(event, 'reviews')">
                        Bình luận/Đánh giá (<?= count($reviews) ?>)
                    </button>
                </div>
                
                <!-- Tab Content: Description -->
                <div id="description" class="tab-content p-8">
                    <div class="prose prose-invert max-w-none text-gray-300 leading-loose">
                        <?= !empty($product['description']) ? nl2br(htmlspecialchars($product['description'])) : 'Chưa có thông tin mô tả chi tiết cho sản phẩm này.' ?>
                    </div>
                </div>

                <!-- Tab Content: Reviews -->
                <div id="reviews" class="tab-content p-8 hidden">
                    <div class="">
                        <h3 class="text-xl font-bold text-white mb-8 flex items-center gap-3">
                            <i class="fa-solid fa-comments text-primary"></i>
                            Khách hàng nói gì về sản phẩm này?
                        </h3>

                        <?php if (empty($reviews)): ?>
                            <div class="bg-gray-800/30 rounded-xl p-8 text-center border border-dashed border-gray-700">
                                <p class="text-gray-500">Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên!</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-6">
                                <?php foreach ($reviews as $rev): ?>
                                    <div class="bg-gray-800/20 rounded-xl p-6 border border-gray-800 hover:border-gray-700 transition-colors">
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold">
                                                    <?= strtoupper(substr($rev['full_name'] ?? $rev['username'] ?? 'K', 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-200"><?= htmlspecialchars($rev['full_name'] ?? $rev['username'] ?? 'Khách hàng') ?></div>
                                                    <div class="text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></div>
                                                </div>
                                            </div>
                                            <div class="flex text-yellow-500 text-xs">
                                                <?php for($i=1; $i<=5; $i++): ?>
                                                    <i class="<?= $i <= ($rev['rating'] ?? 5) ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <div class="text-gray-400 leading-relaxed">
                                            <?= nl2br(htmlspecialchars($rev['content'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Form gửi đánh giá -->
                        <div class="mt-12 pt-10 border-t border-gray-800">
                            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                                <i class="fa-solid fa-pen-to-square text-primary"></i>
                                Viết đánh giá của bạn
                            </h3>
                            
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form action="<?= BASE_URL ?>/index.php?route=product_review_store" method="POST" class="bg-gray-800/40 rounded-xl p-6 border border-gray-800">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-bold text-gray-400 mb-2">Xếp hạng của bạn</label>
                                        <div class="flex gap-2 text-2xl text-gray-600" id="ratingStars">
                                            <?php for($i=1; $i<=5; $i++): ?>
                                                <i class="fa-solid fa-star cursor-pointer hover:text-yellow-400 transition-colors" data-value="<?= $i ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <input type="hidden" name="rating" id="ratingInput" value="5">
                                    </div>

                                    <div class="mb-4">
                                        <label for="reviewContent" class="block text-sm font-bold text-gray-400 mb-2">Nội dung nhận xét</label>
                                        <textarea id="reviewContent" name="content" rows="4" required 
                                            class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                            placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
                                    </div>

                                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-8 rounded-lg transition-all duration-300">
                                        Gửi đánh giá
                                    </button>
                                </form>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const stars = document.querySelectorAll('#ratingStars i');
                                        const input = document.getElementById('ratingInput');
                                        
                                        stars.forEach(star => {
                                            star.addEventListener('click', function() {
                                                const val = this.getAttribute('data-value');
                                                input.value = val;
                                                
                                                stars.forEach(s => {
                                                    if(s.getAttribute('data-value') <= val) {
                                                        s.classList.remove('text-gray-600');
                                                        s.classList.add('text-yellow-400');
                                                    } else {
                                                        s.classList.add('text-gray-600');
                                                        s.classList.remove('text-yellow-400');
                                                    }
                                                });
                                            });
                                        });
                                        // Set default to 5
                                        if(stars.length > 0) stars[4].click();
                                    });
                                </script>
                            <?php else: ?>
                                <div class="bg-gray-800/30 rounded-xl p-8 text-center border border-gray-800">
                                    <p class="text-gray-400 mb-4">Bạn cần đăng nhập để viết đánh giá.</p>
                                    <a href="<?= BASE_URL ?>/index.php?route=login" class="text-primary font-bold hover:underline">Đăng nhập ngay</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Switching Script -->
            <script>
                function openTab(evt, tabName) {
                    var i, tabcontent, tablinks;
                    tabcontent = document.getElementsByClassName("tab-content");
                    for (i = 0; i < tabcontent.length; i++) {
                        tabcontent[i].classList.add("hidden");
                    }
                    tablinks = document.getElementsByClassName("tab-btn");
                    for (i = 0; i < tablinks.length; i++) {
                        tablinks[i].classList.remove("text-white", "border-primary", "bg-gray-800/30");
                        tablinks[i].classList.add("text-gray-500", "border-transparent");
                    }
                    document.getElementById(tabName).classList.remove("hidden");
                    evt.currentTarget.classList.add("text-white", "border-primary", "bg-gray-800/30");
                    evt.currentTarget.classList.remove("text-gray-500", "border-transparent");
                }
            </script>

            <!-- Related Products -->
            <?php if (!empty($relatedProducts)): ?>
                <div class="mb-12">
                    <h3 class="text-2xl font-bold text-white mb-6 border-l-4 border-primary pl-4">Sản Phẩm Cùng Danh Mục</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <?php foreach ($relatedProducts as $rp): ?>
                            <?php 
                            $rpImg = !empty($rp['primary_image']) ? BASE_URL . '/' . htmlspecialchars($rp['primary_image']) : BASE_URL . '/images/no-image.png'; 
                            ?>
                            <div class="group relative bg-gray-900 border border-gray-800 rounded-xl overflow-hidden hover:-translate-y-1 hover:shadow-glow transition-all duration-300 flex flex-col">
                                <a href="<?= BASE_URL ?>/index.php?route=product_detail&slug=<?= $rp['slug'] ?>" class="block relative aspect-square bg-gray-800">
                                    <img src="<?= $rpImg ?>" alt="<?= htmlspecialchars($rp['name']) ?>" class="w-full h-full object-cover">
                                </a>
                                <div class="p-4 flex flex-col flex-1">
                                    <h4 class="font-medium text-gray-200 mb-2 truncate">
                                        <a href="<?= BASE_URL ?>/index.php?route=product_detail&slug=<?= $rp['slug'] ?>" class="hover:text-primary transition-colors">
                                            <?= htmlspecialchars($rp['name']) ?>
                                        </a>
                                    </h4>
                                    <div class="mt-auto">
                                        <?php if ($rp['sale_price']): ?>
                                            <span class="text-primary font-bold mr-2"><?= number_format($rp['effective_price'], 0, ',', '.') ?> VNĐ</span>
                                            <span class="text-gray-500 line-through text-sm"><?= number_format($rp['price'], 0, ',', '.') ?> VNĐ</span>
                                        <?php else: ?>
                                            <span class="text-primary font-bold"><?= number_format($rp['price'], 0, ',', '.') ?> VNĐ</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- AJAX Script -->
            <script>
                document.getElementById('btnAddToCart')?.addEventListener('click', function () {
                    const form = document.getElementById('addToCartForm');
                    const productId = form.querySelector('[name="product_id"]').value;
                    const quantity = form.querySelector('[name="quantity"]').value;
                    const token = form.querySelector('[name="csrf_token"]').value;

                    const msgBox = document.getElementById('cartMessage');
                    msgBox.className = 'mt-3 text-sm font-medium';
                    msgBox.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
                    msgBox.style.display = 'block';

                    fetch('<?= BASE_URL ?>/index.php?route=cart_add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: 'product_id=' + productId + '&quantity=' + quantity + '&csrf_token=' + token
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                msgBox.className = 'mt-3 text-sm font-bold text-emerald-400 bg-emerald-900/30 p-2 rounded';
                                msgBox.innerHTML = '<i class="fa-solid fa-check mr-2"></i> Đã thêm vào giỏ hàng!';
                                if (data.cart_count !== undefined) {
                                    const c = document.getElementById('cartCountSpan');
                                    if (c) c.innerText = data.cart_count;
                                }
                            } else {
                                if (data.require_login) {
                                    window.location.href = '<?= BASE_URL ?>/index.php?route=login';
                                    return;
                                }
                                msgBox.className = 'mt-3 text-sm font-bold text-red-400 bg-red-900/30 p-2 rounded';
                                msgBox.innerHTML = '<i class="fa-solid fa-xmark mr-2"></i> ' + (data.message || 'Có lỗi xảy ra.');
                            }
                        })
                        .catch(e => {
                            msgBox.className = 'mt-3 text-sm font-bold text-red-400 bg-red-900/30 p-2 rounded';
                            msgBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-2"></i> Lỗi kết nối!';
                        });
                });
            </script>
        <?php endif; ?>
    </div>
</div>