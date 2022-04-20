<?php
$title = USERS_ROLE;
$description = "";
?>

<?php ob_start(); ?>
    <!-- main-content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label for="role" class="mb-0">Sélectionner un rôle et modifier les permissions :</label>
                                <select name="role" id="role">
                                    <?php foreach ($rolesList as $role) : ?>
                                        <option value="<?= $role['role_id'] ?>"><?= $role['role_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Groupe (Total/Accordé)</h3>
                        </div>
                        <div class="card-body">
                        </div>
                    </div>
                </div>
                <div class="col-9">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Filtre rapide : <input type="text"></h3>
                        </div>
                        <div class="card-body">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $content = ob_get_clean(); ?>