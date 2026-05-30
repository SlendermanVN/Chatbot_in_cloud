            <!-- END PAGE CONTENT -->

        </div><!-- /main-content-inner -->

        <!-- FOOTER AREA (Srtdash .footer-area) -->
        <div class="footer-area">
            <p class="sz-footer-text">
                <span class="sz-footer-brand"><i class="fa-solid fa-bolt"></i> SportZone</span>
                <span class="sz-footer-sep">·</span>
                <span>Admin Panel &copy; <?= date('Y') ?></span>
                <span class="sz-footer-sep">·</span>
                <span>PHP <?= PHP_MAJOR_VERSION ?>.<?= PHP_MINOR_VERSION ?></span>
                <span class="sz-footer-sep">·</span>
                <a href="<?= BASE_URL ?>/index.php" target="_blank" class="sz-footer-link">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem trang web
                </a>
            </p>
        </div>

    </div><!-- /main-content -->

</div><!-- /page-container -->

<!-- SCRIPTS (Srtdash order) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/metismenujs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/SlickNav/1.0.10/jquery.slicknav.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/scripts.js"></script>

<script>
/* Auto-dismiss flash alerts */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.sz-flash-alert').forEach(function (el) {
        setTimeout(function () {
            el.classList.remove('show');
            setTimeout(function () { el.style.display = 'none'; }, 300);
        }, 5000);
    });
});
</script>
</body>
</html>
