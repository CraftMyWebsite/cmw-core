<?php

use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("pages.list.title");
$description = LangManager::translate("pages.list.desc"); ?>

<?php $scripts = '
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
                        <h3 class="card-title"><?= LangManager::translate("pages.list.title") ?></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="users_table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?= LangManager::translate("pages.title") ?></th>
                                <th><?= LangManager::translate("pages.author") ?></th>
                                <th><?= LangManager::translate("pages.creation.date") ?></th>
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
                                    <td><a href="../pages/edit/<?= $page->getSlug() ?>"><i class="fa fa-cog"></i></a></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th><?= LangManager::translate("pages.title") ?></th>
                                <th><?= LangManager::translate("pages.author") ?></th>
                                <th><?= LangManager::translate("pages.creation.date") ?></th>
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