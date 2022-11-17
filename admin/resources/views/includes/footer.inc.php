<?php use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils; ?>
</div>
<footer>
    <div class="footer clearfix mb-0 text-muted">
        <div class="float-start">
            <p><?= LangManager::translate("core.footer.left", lineBreak: true) ?></p>
        </div>
        <?php if (Utils::getVersion() != Utils::getLatestVersion()): ?>
            <div class="float-end">
                <p class="text-center">
                    <a href="/cmw-admin/updates/cms"><?= LangManager::translate("core.footer.used") . "<span class='text-danger font-bold'>" . Utils::getVersion() ?></span> !<br>
                    <?= LangManager::translate("core.footer.upgrade") . "<span class='text-success font-bold'>" . Utils::getLatestVersion() ?></span> !</a>
                </p>
            </div>
        <?php else: ?>
            <div class="float-end">
                <p>
                    <?= LangManager::translate("core.footer.right") . " " . Utils::getVersion() ?>
                </p>
            </div>
        <?php endif; ?>
        

    </div>
</footer>
<!--IMPORTANT : Fermetures des DIV de sidebar et contenue-->
</div>
</div>
</div>
<!--IMPORTANT : Fermetures des DIV de sidebar et contenue-->


<script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/js/bootstrap.js"></script>
<script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/js/app.js"></script>

<!--EXTENSION select choice-->
<script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/vendors/choices.js/public/assets/scripts/choices.js"></script>
<script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/js/pages/form-element-select.js"></script>
<!--EXTENSION summernote-->
<script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/vendors/jquery/jquery.min.js"></script>
<script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/vendors/summernote/summernote-lite.min.js"></script>
<script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/js/pages/summernote.js"></script>
<!--EXTENSION summernote-->
<script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/vendors/simple-datatables/umd/simple-datatables.js"></script>
<script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/js/pages/simple-datatables.js"></script>
<!-- Need: Apexcharts -->
<script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/vendors/apexcharts/apexcharts.min.js"></script>
<script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/js/pages/dashboard.js"></script>
</body>
</html>