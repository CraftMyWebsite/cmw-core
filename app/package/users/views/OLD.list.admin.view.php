<?php

use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("users.list.title");
$description = LangManager::translate("users.list.desc"); ?>

<?php $styles = '<link rel="stylesheet" href="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/datatables-responsive/css/responsive.bootstrap4.min.css">'; ?>

<?php $scripts = '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/datatables/jquery.dataTables.min.js"></script>
<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script>
    $(function () {
        $("#users_table").DataTable({
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            language: {
                processing:     "' . LangManager::translate("core.datatables.list.processing") . '",
                search:         "' . LangManager::translate("core.datatables.list.search") . '",
                lengthMenu:    "' . LangManager::translate("core.datatables.list.lenghtmenu") . '",
                info:           "' . LangManager::translate("core.datatables.list.info") . '",
                infoEmpty:      "' . LangManager::translate("core.datatables.list.info_empty") . '",
                infoFiltered:   "' . LangManager::translate("core.datatables.list.info_filtered") . '",
                infoPostFix:    "' . LangManager::translate("core.datatables.list.info_postfix") . '",
                loadingRecords: "' . LangManager::translate("core.datatables.list.loadingrecords") . '",
                zeroRecords:    "' . LangManager::translate("core.datatables.list.zerorecords") . '",
                emptyTable:     "' . LangManager::translate("core.datatables.list.emptytable") . '",
                paginate: {
                    first:      "' . LangManager::translate("core.datatables.list.first") . '",
                    previous:   "' . LangManager::translate("core.datatables.list.previous") . '",
                    next:       "' . LangManager::translate("core.datatables.list.next") . '",
                    last:       "' . LangManager::translate("core.datatables.list.last") . '"
                },
                aria: {
                    sortAscending:  "' . LangManager::translate("core.datatables.list.sort.ascending") . '",
                    sortDescending: "' . LangManager::translate("core.datatables.list.sort.descending") . '"
                }
            },
        });
    });
</script>'; ?>

<!-- main-content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Contenu ici -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?= LangManager::translate("users.list.card_title") ?></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="users_table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?= LangManager::translate("users.users.mail") ?></th>
                                <th><?= LangManager::translate("users.users.pseudo") ?></th>
                                <th><?= LangManager::translate("users.users.firstname") ?></th>
                                <th><?= LangManager::translate("users.users.surname") ?></th>
                                <th><?= LangManager::translate("users.users.role") ?></th>
                                <th><?= LangManager::translate("users.users.creation") ?></th>
                                <th><?= LangManager::translate("users.users.last_edit") ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php /** @var \CMW\Entity\Users\UserEntity[] $userList */
                            foreach ($userList as $user) : ?>
                                <tr>
                                    <td><?= $user->getMail() ?></td>
                                    <td><?= $user->getUsername() ?></td>
                                    <td><?= $user->getFirstName() ?></td>
                                    <td><?= $user->getLastName() ?></td>
                                    <td><?php $i = 1;
                                        foreach ($user->getRoles() as $role): ?>
                                            <?= $i !== 1 ? ", {$role->getName()}" : $role->getName() ?>
                                            <?php $i++;
                                        endforeach; ?>
                                    </td>
                                    <td><?= $user->getCreated() ?></td>
                                    <td><?= $user->getUpdated() ?></td>
                                    <td><a href="../users/edit/<?= $user->getId() ?>"><i
                                                    class="fa fa-cog"></i></a></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th><?= LangManager::translate("users.users.mail") ?></th>
                                <th><?= LangManager::translate("users.users.pseudo") ?></th>
                                <th><?= LangManager::translate("users.users.firstname") ?></th>
                                <th><?= LangManager::translate("users.users.surname") ?></th>
                                <th><?= LangManager::translate("users.users.role") ?></th>
                                <th><?= LangManager::translate("users.users.creation") ?></th>
                                <th><?= LangManager::translate("users.users.last_edit") ?></th>
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    </div>
</div>
<!-- /.main-content -->