<?php use CMW\Model\Pages\pagesModel;

$title = PAGES_LIST_TITLE;
$description = PAGES_LIST_DESC; ?>

<?php $styles = '<link rel="stylesheet" href="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/datatables-responsive/css/responsive.bootstrap4.min.css">';?>

<?php $scripts = '<script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/datatables/jquery.dataTables.min.js"></script>
<script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script>
    $(function () {
        $("#users_table").DataTable({
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            language: {
                processing:     "'.DATATABLES_LIST_PROCESSING.'",
                search:         "'.DATATABLES_LIST_SEARCH.'",
                lengthMenu:    "'.DATATABLES_LIST_LENGTHMENU.'",
                info:           "'.DATATABLES_LIST_INFO.'",
                infoEmpty:      "'.DATATABLES_LIST_INFOEMPTY.'",
                infoFiltered:   "'.DATATABLES_LIST_INFOFILTERED.'",
                infoPostFix:    "'.DATATABLES_LIST_INFOPOSTFIX.'",
                loadingRecords: "'.DATATABLES_LIST_LOADINGRECORDS.'",
                zeroRecords:    "'.DATATABLES_LIST_ZERORECORDS.'",
                emptyTable:     "'.DATATABLES_LIST_EMPTYTABLE.'",
                paginate: {
                    first:      "'.DATATABLES_LIST_FIRST.'",
                    previous:   "'.DATATABLES_LIST_PREVIOUS.'",
                    next:       "'.DATATABLES_LIST_NEXT.'",
                    last:       "'.DATATABLES_LIST_LAST.'"
                },
                aria: {
                    sortAscending:  "'.DATATABLES_LIST_SORTASCENDING.'",
                    sortDescending: "'.DATATABLES_LIST_SORTDESCENDING.'"
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
                            <h3 class="card-title"><?=USERS_LIST_CARD_TITLE?></h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="users_table" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Auteur</th>
                                    <th>Date de création</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php /** @var pagesModel[] $pagesList */
                                foreach ($pagesList as $page) : ?>
                                    <tr>
                                        <td><?=$page->pageTitle?></td>
                                        <td><?=$page->user->userPseudo?></td>
                                        <td><?=$page->pageCreated?></td>
                                        <td><a href="../pages/edit/<?=$page->pageId?>"><i class="fa fa-cog"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Titre</th>
                                    <th>Auteur</th>
                                    <th>Date de création</th>
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