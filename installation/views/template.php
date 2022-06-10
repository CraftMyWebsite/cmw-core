<?php require_once("include/header.php") ?>
<?php require_once("include/aside.php") ?>

<?php /* @var string $content */ ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= INSTALL_MAIN_TITLE ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="<?= getenv("PATH_SUBFOLDER") ?>/cmw-admin">CraftMyWebsite</a>
                        </li>
                        <li class="breadcrumb-item active"><?= INSTALL_MAIN_TITLE ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-7">
                <?= $content ?>
            </div>
            <?php require_once("include/right.php") ?>
        </div>
    </div>
</div>

<?php require_once("include/footer.php") ?>
