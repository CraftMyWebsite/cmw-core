<?php use CMW\Manager\Lang\LangManager; ?>

<!-- Main Footer -->
<footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
        <?= LangManager::translate("core.footer.right", lineBreak: true) ?>
    </div>
    <strong><?= LangManager::translate("core.footer.left", lineBreak: true) ?></strong>
</footer>