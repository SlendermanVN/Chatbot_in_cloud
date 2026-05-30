<?php
$search = $search ?? '';
$grouped = $grouped ?? [];
$categories = $categories ?? [];

// Xử lý Flash Message (Thông báo nhanh)
$flash = null;
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash']['message'];
    $flashType = $_SESSION['flash']['type'] ?? 'success';
    unset($_SESSION['flash']);
}

// Cấu hình icon cho từng danh mục
$catIcons = [
    'Vận chuyển' => 'fa-truck',
    'Đơn hàng' => 'fa-box',
    'Bảo hành' => 'fa-shield-halved',
    'Sản phẩm' => 'fa-tag',
    'Thanh toán' => 'fa-credit-card',
    'Tài khoản' => 'fa-user-gear',
];
?>

<?php if ($flash): ?>
    <div class="mx-auto max-w-4xl px-4 pt-6">
        <div
            class="flex items-center gap-3 px-5 py-4 rounded-xl <?= ($flashType ?? 'success') === 'success' ? 'bg-green-500/10 border border-green-600/30 text-green-400' : 'bg-red-500/10 border border-red-600/30 text-red-400' ?>">
            <i
                class="fa-solid <?= ($flashType ?? 'success') === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
            <span><?= htmlspecialchars($flash) ?></span>
        </div>
    </div>
<?php endif; ?>

<section class="relative overflow-hidden py-20 px-4">
    <div class="absolute inset-0 bg-gradient-to-b from-primary/10 via-transparent to-transparent pointer-events-none">
    </div>
    <div class="max-w-3xl mx-auto text-center relative z-10">
        <div
            class="inline-flex items-center gap-2 bg-primary/10 border border-primary/30 text-primary text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-6">
            <i class="fa-solid fa-circle-question"></i> Support & FAQ
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight">
            Mọi thắc mắc về<br><span class="text-primary">SportZone Vietnam</span>
        </h1>
        <p class="text-gray-400 text-lg max-w-xl mx-auto mb-10">
            Tất cả những gì bạn cần biết về sản phẩm, vận chuyển và bảo hành thiết bị thể thao.
        </p>

        <form method="GET" action="<?= BASE_URL ?>/index.php" class="relative max-w-xl mx-auto">
            <input type="hidden" name="route" value="faqs">
            <div
                class="flex items-center bg-[#0c1220] border border-gray-700 rounded-2xl px-5 py-3 gap-3 focus-within:border-primary transition-colors shadow-2xl">
                <i class="fa-solid fa-search text-gray-500"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    placeholder="Tìm kiếm (ví dụ: vận chuyển, đổi trả...)"
                    class="flex-1 bg-transparent text-white placeholder-gray-500 text-sm focus:outline-none">
                <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white text-sm font-bold px-4 py-1.5 rounded-xl transition-colors">
                    Tìm
                </button>
            </div>
        </form>
        <?php if ($search): ?>
            <p class="mt-4 text-sm text-gray-500">
                Kết quả cho "<span class="text-white font-semibold"><?= htmlspecialchars($search) ?></span>" —
                <a href="<?= BASE_URL ?>/index.php?route=faqs" class="text-primary font-semibold hover:underline">Xóa</a>
            </p>
        <?php endif; ?>
    </div>
</section>

<?php if (!$search && !empty($grouped)): ?>
    <div class="max-w-5xl mx-auto px-4 mb-8">
        <div class="flex flex-wrap justify-center gap-3 md:gap-4" id="faqCatTabs">
            <?php $first = true;
            foreach (array_keys($grouped) as $cat): ?>
                <button type="button" data-cat="<?= htmlspecialchars($cat) ?>"
                    class="faq-tab flex-1 sm:flex-none min-w-[140px] max-w-[200px] flex flex-col items-center gap-2 p-4 rounded-2xl border transition-all text-sm font-medium
                   <?= $first ? 'bg-primary/10 border-primary text-primary' : 'bg-gray-900 border-gray-800 text-gray-400 hover:text-white hover:border-gray-600' ?>">
                    <i class="fa-solid <?= $catIcons[$cat] ?? 'fa-circle-question' ?> text-xl"></i>
                    <span class="text-center"><?= htmlspecialchars($cat) ?></span>
                </button>
                <?php $first = false; endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="max-w-3xl mx-auto px-4 pb-16">
    <h2 class="text-white font-bold text-xl mb-6 text-center md:text-left">
        <?= $search ? 'Kết quả tìm kiếm' : 'Câu hỏi thường gặp' ?>
    </h2>

    <?php if (empty($grouped)): ?>
        <div class="text-center py-16 text-gray-500 bg-gray-900 border border-gray-800 rounded-2xl">
            <i class="fa-solid fa-magnifying-glass-minus text-5xl text-gray-700 block mb-4"></i>
            <?= $search ? 'Không tìm thấy kết quả cho "' . htmlspecialchars($search) . '".' : 'Chưa có FAQ nào.' ?>
        </div>
    <?php else: ?>
        <?php $firstGroup = true;
        foreach ($grouped as $category => $faqs): ?>
            <div class="faq-panel mb-6" data-panel="<?= htmlspecialchars($category) ?>" <?= (!$firstGroup && !$search) ? 'style="display:none;"' : '' ?>>
                <?php if ($search): ?>
                    <div class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3 flex items-center gap-2">
                        <i class="fa-solid <?= $catIcons[$category] ?? 'fa-folder' ?>"></i>
                        <?= htmlspecialchars($category) ?>
                    </div>
                <?php endif; ?>

                <div class="space-y-3">
                    <?php foreach ($faqs as $faq): ?>
                        <div class="faq-item bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden hover:border-gray-700 transition-colors"
                            id="faq-<?= (int) $faq['id'] ?>">
                            <button type="button"
                                class="faq-item__header w-full flex items-center justify-between gap-4 px-6 py-5 text-left text-white font-medium text-sm group"
                                aria-expanded="false">
                                <span class="flex-1"><?= htmlspecialchars($faq['question']) ?></span>
                                <i
                                    class="fa-solid fa-chevron-down text-gray-500 transition-transform duration-300 group-[.open]:rotate-180 flex-shrink-0"></i>
                            </button>
                            <div class="faq-item__body hidden px-6 pb-6">
                                <p class="text-gray-400 text-sm leading-relaxed border-t border-gray-800 pt-4">
                                    <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                                </p>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <div class="flex gap-2 mt-4">
                                        <a href="<?= BASE_URL ?>/index.php?route=admin_faq_edit&id=<?= (int) $faq['id'] ?>"
                                            class="text-xs text-blue-400 hover:text-blue-300 flex items-center gap-1"><i
                                                class="fa-solid fa-pen-to-square"></i> Sửa</a>
                                        <a href="<?= BASE_URL ?>/index.php?route=admin_faq_delete&id=<?= (int) $faq['id'] ?>"
                                            onclick="return confirm('Xóa câu hỏi này?')"
                                            class="text-xs text-red-400 hover:text-red-300 flex items-center gap-1"><i
                                                class="fa-solid fa-trash"></i> Xóa</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php $firstGroup = false; endforeach; ?>
    <?php endif; ?>
</div>

<div class="max-w-3xl mx-auto px-4 pb-16">
    <div
        class="bg-gradient-to-br from-gray-900 to-[#0c1220] border border-gray-800 rounded-2xl p-10 text-center shadow-xl">
        <i class="fa-solid fa-headset text-4xl text-primary mb-4 block"></i>
        <h2 class="text-2xl font-extrabold text-white mb-2">Vẫn cần hỗ trợ?</h2>
        <p class="text-gray-400 mb-6">Đội ngũ SportZone sẵn sàng hỗ trợ mọi thắc mắc của bạn.</p>
        <div class="flex flex-col sm:flex-row flex-wrap items-center justify-center gap-3">
            <a href="<?= BASE_URL ?>/index.php?route=contact"
                class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-primary/30">
                <i class="fa-solid fa-comment-dots"></i> Liên hệ hỗ trợ
            </a>
            <a href="#faq-ask-section"
                class="inline-flex items-center justify-center gap-2 bg-gray-800 hover:bg-gray-700 text-white font-bold px-6 py-3 rounded-xl border border-gray-700 transition-all">
                <i class="fa-solid fa-circle-question"></i> Đặt câu hỏi
            </a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="<?= BASE_URL ?>/index.php?route=admin_faqs"
                    class="inline-flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 text-gray-300 font-bold px-6 py-3 rounded-xl border border-gray-700 transition-all">
                    <i class="fa-solid fa-gear"></i> Quản lý FAQ
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<section class="max-w-2xl mx-auto px-4 pb-20" id="faq-ask-section">
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 shadow-2xl">
        <h2 class="text-xl font-bold text-white mb-1">Gửi câu hỏi của bạn</h2>
        <p class="text-gray-500 text-sm mb-6">Không tìm thấy câu trả lời? Hãy gửi câu hỏi, đội ngũ chúng tôi sẽ phản hồi
            sớm nhất!</p>

        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="bg-red-500/10 border border-red-600/30 text-red-400 rounded-xl p-4 mb-5">
                <?php foreach ($_SESSION['errors'] as $e):
                    unset($_SESSION['errors']); ?>
                    <div class="text-sm"><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/index.php?route=faq_submit" id="askFaqForm" novalidate>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1.5">Họ & Tên <span
                            class="text-red-500">*</span></label>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <input type="text"
                            class="w-full bg-[#0b0f19] border border-gray-700 text-gray-400 rounded-xl px-4 py-2.5 cursor-not-allowed"
                            value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" readonly>
                    <?php else: ?>
                        <input type="text" name="name" required maxlength="100" placeholder="Nguyễn Văn A"
                            value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>"
                            class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-2.5 focus:outline-none focus:border-primary transition-colors">
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1.5">Email <span
                            class="text-red-500">*</span></label>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <input type="email"
                            class="w-full bg-[#0b0f19] border border-gray-700 text-gray-400 rounded-xl px-4 py-2.5 cursor-not-allowed"
                            value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" readonly>
                    <?php else: ?>
                        <input type="email" name="email" required maxlength="150" placeholder="email@example.com"
                            value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>"
                            class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-2.5 focus:outline-none focus:border-primary transition-colors">
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1.5">
                        Câu hỏi <span class="text-red-500">*</span>
                        <span class="float-right text-gray-600 text-xs font-normal"><span
                                id="askQLen">0</span>/300</span>
                    </label>
                    <textarea name="question" id="ask_question" rows="4" required maxlength="300"
                        placeholder="Câu hỏi của bạn là gì?"
                        class="w-full bg-[#0b0f19] border border-gray-700 text-white rounded-xl px-4 py-2.5 focus:outline-none focus:border-primary transition-colors resize-none"><?= htmlspecialchars($_SESSION['old']['question'] ?? '') ?></textarea>
                    <?php unset($_SESSION['old']); ?>
                </div>
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg shadow-primary/30">
                    <i class="fa-solid fa-paper-plane"></i> Gửi câu hỏi
                </button>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <p class="text-center text-sm text-gray-600">
                        <a href="<?= BASE_URL ?>/index.php?route=login"
                            class="text-primary font-semibold hover:underline">Đăng nhập</a> để điền thông tin tự động.
                    </p>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<script>
    (function () {
        // 1. Chuyển tab danh mục
        var tabs = document.querySelectorAll('.faq-tab');
        var panels = document.querySelectorAll('.faq-panel');
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var cat = this.dataset.cat;
                tabs.forEach(function (t) {
                    t.classList.remove('bg-primary/10', 'border-primary', 'text-primary');
                    t.classList.add('bg-gray-900', 'border-gray-800', 'text-gray-400');
                });
                this.classList.remove('bg-gray-900', 'border-gray-800', 'text-gray-400');
                this.classList.add('bg-primary/10', 'border-primary', 'text-primary');
                panels.forEach(function (p) {
                    p.style.display = p.dataset.panel === cat ? '' : 'none';
                });
            });
        });

        // 2. Accordion mở / đóng
        document.querySelectorAll('.faq-item__header').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var item = this.closest('.faq-item');
                var isOpen = !item.querySelector('.faq-item__body').classList.contains('hidden');

                // Đóng các item khác trong cùng panel
                var panel = item.closest('.faq-panel');
                if (panel) {
                    panel.querySelectorAll('.faq-item').forEach(function (i) {
                        i.querySelector('.faq-item__body').classList.add('hidden');
                        i.querySelector('.faq-item__header').setAttribute('aria-expanded', 'false');
                        var icon = i.querySelector('.faq-item__header .fa-chevron-down');
                        if (icon) icon.style.transform = '';
                    });
                }

                if (!isOpen) {
                    item.querySelector('.faq-item__body').classList.remove('hidden');
                    btn.setAttribute('aria-expanded', 'true');
                    var icon = btn.querySelector('.fa-chevron-down');
                    if (icon) icon.style.transform = 'rotate(180deg)';
                }
            });
        });

        // 3. Đếm ký tự
        var qArea = document.getElementById('ask_question');
        var qLen = document.getElementById('askQLen');
        if (qArea && qLen) {
            qArea.addEventListener('input', function () { qLen.textContent = this.value.length; });
            qLen.textContent = qArea.value.length;
        }
    })();
</script>