<?php $ls = $landingSettings ?? []; ?>
</div><!-- /pt-16 wrapper -->

<!-- Footer mini -->
<footer class="border-t border-gray-800/60 py-6 px-4">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="text-primary font-black italic tracking-widest"><?= htmlspecialchars($ls['site_name'] ?? 'SportZone') ?></div>
        <div class="flex items-center gap-5 text-xs text-gray-700">
            <a href="<?= BASE_URL ?>/index.php?route=login"   class="hover:text-gray-400 transition-colors">Đăng nhập</a>
            <a href="<?= BASE_URL ?>/index.php?route=register" class="hover:text-gray-400 transition-colors">Đăng ký</a>
        </div>
        <div class="text-xs text-gray-700">&copy; <?= date('Y') ?> <?= htmlspecialchars($ls['site_name'] ?? 'SportZone Vietnam') ?>.</div>
    </div>
</footer>

</body>
</html>
