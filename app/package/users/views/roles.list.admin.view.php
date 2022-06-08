<?php

$title = USERS_ROLE;
$description = USERS_LIST_DESC; ?>

<?php $styles = '<link rel="stylesheet" href="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/datatables-responsive/css/responsive.bootstrap4.min.css">';?>

<?php $scripts = '<script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/datatables/jquery.dataTables.min.js"></script>
<script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script>
    $(function () {
        $("#roles_table").DataTable({
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            language: {
                processing:     "'.CORE_DATATABLES_LIST_PROCESSING.'",
                search:         "'.CORE_DATATABLES_LIST_SEARCH.'",
                lengthMenu:    "'.CORE_DATATABLES_LIST_LENGTHMENU.'",
                info:           "'.CORE_DATATABLES_LIST_INFO.'",
                infoEmpty:      "'.CORE_DATATABLES_LIST_INFOEMPTY.'",
                infoFiltered:   "'.CORE_DATATABLES_LIST_INFOFILTERED.'",
                infoPostFix:    "'.CORE_DATATABLES_LIST_INFOPOSTFIX.'",
                loadingRecords: "'.CORE_DATATABLES_LIST_LOADINGRECORDS.'",
                zeroRecords:    "'.CORE_DATATABLES_LIST_ZERORECORDS.'",
                emptyTable:     "'.CORE_DATATABLES_LIST_EMPTYTABLE.'",
                paginate: {
                    first:      "'.CORE_DATATABLES_LIST_FIRST.'",
                    previous:   "'.CORE_DATATABLES_LIST_PREVIOUS.'",
                    next:       "'.CORE_DATATABLES_LIST_NEXT.'",
                    last:       "'.CORE_DATATABLES_LIST_LAST.'"
                },
                aria: {
                    sortAscending:  "'.CORE_DATATABLES_LIST_SORTASCENDING.'",
                    sortDescending: "'.CORE_DATATABLES_LIST_SORTDESCENDING.'"
                }
            },
        });
    });
</script>';?>

<?php ob_start(); ?>
    <!-- main-content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Contenu ici -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?=USERS_ROLES_LIST_CARD_TITLE?></h3>
                            <div class="float-right">
                                <div class="mx-auto">
                                    <a href="add" class="btn btn-success"><?= USERS_ROLE_ADD ?></a>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="roles_table" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th><?= USERS_ROLE_NAME ?></th>
                                    <th><?= USERS_ROLE_DESCRIPTION ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php /* @var $rolesList */
                                foreach ($rolesList as $role) : ?>
                                    <tr>
                                        <td><?= $role['role_name'] ?></td>
                                        <td><?= $role['role_description'] ?></td>
                                        <td><a href="../roles/edit/<?=$role['role_id']?>"><i class="fa fa-cog"></i></a>
                                            <a href="../roles/delete/<?= $role['role_id'] ?>" class="text-danger float-right"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th><?= USERS_ROLE_NAME ?></th>
                                    <th><?= USERS_ROLE_DESCRIPTION ?></th>
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
<?php $content = ob_get_clean(); ?>