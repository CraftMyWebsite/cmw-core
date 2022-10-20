<?php


use CMW\Utils\SecurityService;

$title = "Connexion";
$description = "Description de votre page"; ?>


<section>
    <div class="container">


        <form action="" method="post" class="mb-4">
            <?php (new SecurityService())->insertHiddenToken() ?>
            <div class="form-group">
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    <input name="login_email" type="email" class="form-control" placeholder="email">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group mb-3" id="showHidePassword">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" name="login_password" class="form-control"
                           placeholder="*****">
                    <div class="input-group-append">
                        <a class="input-group-text" href="#"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-12">
                    <div class="icheck-primary">
                        <input type="checkbox" id="login_keep_connect" name="login_keep_connect">
                        <label for="login_keep_connect">Rester connecter</label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-block">S'enregistrer</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

    </div>
</section>
