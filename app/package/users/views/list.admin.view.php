<?php

$title = USERS_LIST_TITLE;
$description = USERS_LIST_DESC; ?>

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
                processing:     "' . CORE_DATATABLES_LIST_PROCESSING . '",
                search:         "' . CORE_DATATABLES_LIST_SEARCH . '",
                lengthMenu:    "' . CORE_DATATABLES_LIST_LENGTHMENU . '",
                info:           "' . CORE_DATATABLES_LIST_INFO . '",
                infoEmpty:      "' . CORE_DATATABLES_LIST_INFOEMPTY . '",
                infoFiltered:   "' . CORE_DATATABLES_LIST_INFOFILTERED . '",
                infoPostFix:    "' . CORE_DATATABLES_LIST_INFOPOSTFIX . '",
                loadingRecords: "' . CORE_DATATABLES_LIST_LOADINGRECORDS . '",
                zeroRecords:    "' . CORE_DATATABLES_LIST_ZERORECORDS . '",
                emptyTable:     "' . CORE_DATATABLES_LIST_EMPTYTABLE . '",
                paginate: {
                    first:      "' . CORE_DATATABLES_LIST_FIRST . '",
                    previous:   "' . CORE_DATATABLES_LIST_PREVIOUS . '",
                    next:       "' . CORE_DATATABLES_LIST_NEXT . '",
                    last:       "' . CORE_DATATABLES_LIST_LAST . '"
                },
                aria: {
                    sortAscending:  "' . CORE_DATATABLES_LIST_SORTASCENDING . '",
                    sortDescending: "' . CORE_DATATABLES_LIST_SORTDESCENDING . '"
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
                            <h3 class="card-title"><?= USERS_LIST_CARD_TITLE ?></h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="users_table" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th><?= USERS_MAIL ?></th>
                                    <th><?= USERS_PSEUDO ?></th>
                                    <th><?= USERS_FIRSTNAME ?></th>
                                    <th><?= USERS_SURNAME ?></th>
                                    <th><?= USERS_ROLE ?></th>
                                    <th><?= USERS_CREATION ?></th>
                                    <th><?= USERS_LAST_EDIT ?></th>
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
                                    <th><?= USERS_MAIL ?></th>
                                    <th><?= USERS_PSEUDO ?></th>
                                    <th><?= USERS_FIRSTNAME ?></th>
                                    <th><?= USERS_SURNAME ?></th>
                                    <th><?= USERS_ROLE ?></th>
                                    <th><?= USERS_CREATION ?></th>
                                    <th><?= USERS_LAST_EDIT ?></th>
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