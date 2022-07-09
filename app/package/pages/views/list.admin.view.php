<?php

$title = PAGES_LIST_TITLE;
$description = PAGES_LIST_DESC; ?>

<?php $scripts = '
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
                                    <th><?= PAGES_TITLE ?></th>
                                    <th><?= PAGES_AUTHOR ?></th>
                                    <th><?= PAGES_CREATION_DATE ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php /** @var \CMW\Entity\Pages\PageEntity[] $pagesList */
                                foreach ($pagesList as $page) : ?>
                                    <tr>
                                        <td><?= $page->getTitle() ?></td>
                                        <td><?= $page->getUser()->getUsername() ?></td>
                                        <td><?= $page->getCreated() ?></td>
                                        <td><a href="../pages/edit/<?= $page->getId() ?>"><i class="fa fa-cog"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th><?= PAGES_TITLE ?></th>
                                    <th><?= PAGES_AUTHOR ?></th>
                                    <th><?= PAGES_CREATION_DATE ?></th>
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