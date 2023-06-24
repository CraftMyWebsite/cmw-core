<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("core.security.healthReport.title");
$description = LangManager::translate("core.security.healthReport.title");

/* @var $report */
/* @var $reportName */ ?>


<main>
    <div class="row">
        <h3><?= LangManager::translate("core.security.healthReport.title") ?>.</h3>

        <button class="btn btn-success" onclick="copyToClipboard('<?= $report ?>')">
            <?= LangManager::translate('core.security.healthReport.copy') ?>
        </button>

        <a class="btn btn-danger mt-2" style="float: left"
           href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/security/delete/report/health">
            <?= LangManager::translate('core.btn.delete') ?>
        </a>

        <pre class="mt-4"><?= LangManager::translate('core.security.healthReport.emplacement') ?>=> <?= "App/Storage/Reports/$reportName" ?></pre>
    </div>

    <div class="container mt-5">
<pre>
<?= $report ?>
</pre>
    </div>
</main>


<script>
    const copyToClipboard = (content) => {
        navigator.clipboard.writeText(content).then(() => {
            alert('Health Report copié !')
        }, () => {
            alert('Error !')
        });

    }
</script>
