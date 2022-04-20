<?php
$title = USERS_ADD_TITLE;
$description = USERS_ADD_DESC;

$scripts = '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/js/main.js"></script>';

?>

<?php ob_start(); ?>
    <!-- main-content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="" method="post">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><?=USERS_ADD_CARD_TITLE?> :</h3>
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" name="email" class="form-control" placeholder="<?=USERS_MAIL?>" required>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                    </div>
                                    <input type="text" name="pseudo" class="form-control" placeholder="<?=USERS_PSEUDO?>" required>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    </div>
                                    <input type="text" name="name" class="form-control" placeholder="<?=USERS_FIRSTNAME?>">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    </div>
                                    <input type="text" name="lastname" class="form-control" placeholder="<?=USERS_SURNAME?>">
                                </div>
                                <div class="form-group">
                                    <label><?=USERS_ROLE?></label>
                                    <select name="role" class="form-control">
                                        <?php
                                        foreach ($roles as $role) : ?>
                                            <option value="<?=$role['role_id']?>"><?=$role['role_name']?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?=USERS_PASS?></label>
                                    <div class="input-group mb-3" id="showHidePassword">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" name="pass" class="form-control" placeholder="******">
                                        <div class="input-group-append">
                                            <a class="input-group-text" href="#"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary float-right"><?=USERS_LIST_BUTTON_SAVE?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.row -->
        </div>
    </div>
    <!-- /.main-content -->
<?php $content = ob_get_clean(); ?>