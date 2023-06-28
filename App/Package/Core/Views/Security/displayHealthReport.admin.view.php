<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("core.security.healthReport.title");
$description = LangManager::translate("core.security.healthReport.title");

/* @var $report */
/* @var $reportName */ ?>

<div class="d-flex flex-wrap justify-content-between" style="width: 100%">
    <h3><i class="fa-solid fa-suitcase-medical"></i> <?= LangManager::translate("core.security.healthReport.title") ?>
    </h3>
    <div class="d-flex flex-wrap justify-content-end gap-4 align-items-center">
        <button class="btn btn-primary"
                onclick="copyReportToClipboard()"><?= LangManager::translate('core.security.healthReport.copy') ?></button>
        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/security/delete/report/health"
           class="btn btn-warning"><?= LangManager::translate('core.btn.delete') ?></a>
    </div>
</div>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <p class="mt-4"><?= LangManager::translate('core.security.healthReport.emplacement') ?>
                => <?= "App/Storage/Reports/$reportName" ?></p>
        </div>

    </div>
</div>


<div class="container mt-5 card">
    <pre id="report">
        <?= $report ?>
    </pre>
</div>


<script>
    function copyReportToClipboard() {
        var range = document.createRange();
        range.selectNode(document.getElementById("report"));
        window.getSelection().removeAllRanges(); // clear current selection
        window.getSelection().addRange(range); // to select text
        document.execCommand("copy");
        window.getSelection().removeAllRanges();// to deselect
        launchAlert();
    }

    const launchAlert = () => {
        iziToast.show(
            {
                titleSize: '16',
                messageSize: '14',
                icon: 'fa-solid fa-info',
                title  : "HealthReport",
                message: `<?= LangManager::translate('core.toaster.security.healthReport.copied') ?>`,
                color: "#41435F",
                iconColor: '#22E445',
                titleColor: '#22E445',
                messageColor: '#fff',
                balloon: false,
                close: false,
                position: 'bottomRight',
                timeout: 5000,
                animateInside: false,
                progressBar: false,
                transitionIn: 'fadeInLeft',
                transitionOut: 'fadeOutRight',
            });
    }
</script>
