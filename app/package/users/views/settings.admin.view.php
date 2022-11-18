<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gears"></i> <span class="m-lg-auto">Réglages</span></h3>
    <div class="buttons"><button type="submit" class="btn btn-primary">Sauvegarder</button></div>
</div>
<section class="row">
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4>Identité visuel</h4>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <h6>Image de profil par défaut :</h6>
                        <div class="text-center ">
                            <img class="w-25 border" src="https://theme.voyza.fr/public/uploads/users/default/defaultImage.jpg" alt="Image introuvable !">
                        </div>
                        <input class="mt-2 form-control form-control-lg" type="file" id="formFile">
                    </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Mot de passe <i data-bs-toggle="tooltip" title="Méthode utiliser pour débloquer les comptes utilisateurs" class="fa-sharp fa-solid fa-circle-question"></i></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <h6>Méthode de réinitialisations du mot de passe :</h6>
                    <fieldset class="form-group">
                        <select class="form-select" id="basicSelect">
                            <option>Mot de passe envoyé par mail</option>
                            <option>Lien unique envoyé par mail</option>
                        </select>
                    </fieldset>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                    <h4>Gestion des rôles</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="">Nom - Titre</th>
                        <th class="">Description</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Visiteur</td>
                        <td>Rôle pour les visiteurs</td>
                        <td class="text-center">
                            <i data-bs-toggle="modal" data-bs-target="#roleEditModal" class="text-primary fa-solid fa-gears"></i>
                            <i data-bs-toggle="modal" data-bs-target="#roleDeleteModal" class="ms-2 text-danger fa-solid fa-trash"></i>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="text-end ">
                    <button data-bs-toggle="modal" data-bs-target="#roleAddModal" type="button" class="btn btn-primary">Ajouter un rôle</button>
                </div>

            </div>
        </div>
    </div>
</section>





<!--Modale d'édition-->
<div class="modal fade modal-xl" id="roleEditModal" tabindex="-1" role="dialog" aria-labelledby="roleEditModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleEditModalTitle">Édition de rôle</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <h6>Nom :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" placeholder="Nom">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-id-card-clip"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h6>Poid : <i data-bs-toggle="tooltip" title="Plus le chiffre est haut plus le rôle est important" class="fa-sharp fa-solid fa-circle-question"></i></h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="number" class="form-control" placeholder="0">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-weight-hanging"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h6>Déscription :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" placeholder="Déscription">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>
                    </div>
                    <h6>Permissions :</h6>
                    <div class="row mx-4">
                        <div class="col-12 col-lg-4">
                            <!-- Cette partie va dans /functions/loadPermissions.php -->
                            <b>Pages :</b>
                            <ul style="list-style-type: none">
                                <li>
                                    <div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">pages.*</label></div>
                                </li>
                                    <ul style="list-style-type: none">
                                        <li>
                                            <div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">show</label></div>
                                        </li>
                                    </ul>
                            </ul>
                            <!-- Fin de partie dans /functions/loadPermissions.php -->
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Annuler</span>
                </button>
                <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Confirmer</span>
                </button>
            </div>
            </form>
        </div>
    </div>
</div>


<!--Modale d'ajout-->
<div class="modal fade modal-xl" id="roleAddModal" tabindex="-1" role="dialog" aria-labelledby="roleAddModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleAddModalTitle">Ajout d'un rôle</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <h6>Nom :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" placeholder="Nom">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-id-card-clip"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h6>Poid : <i data-bs-toggle="tooltip" title="Plus le chiffre est haut plus le rôle est important" class="fa-sharp fa-solid fa-circle-question"></i></h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="number" class="form-control" placeholder="0">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-weight-hanging"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h6>Déscription :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" placeholder="Déscription">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>
                    </div>
                    <h6>Permissions :</h6>
                    <div class="row mx-4">
                        <div class="col-12 col-lg-4">
                            <b>Pages :</b>
                            <ul style="list-style-type: none">
                                <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">pages.*</label></div></li>
                                <ul style="list-style-type: none">
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">show</label></div></li>
                                    </li>
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">add</label></div></li>
                                    </li>
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">edit</label></div></li>
                                    </li>
                                </ul>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-4">
                            <b>Pages :</b>
                            <ul style="list-style-type: none">
                                <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">pages.*</label></div></li>
                                <ul style="list-style-type: none">
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">show</label></div></li>
                                    </li>
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">add</label></div></li>
                                    </li>
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">edit</label></div></li>
                                    </li>
                                </ul>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-4">
                            <b>Pages :</b>
                            <ul style="list-style-type: none">
                                <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">pages.*</label></div></li>
                                <ul style="list-style-type: none">
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">show</label></div></li>
                                    </li>
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">add</label></div></li>
                                    </li>
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">edit</label></div></li>
                                    </li>
                                </ul>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-4">
                            <b>Pages :</b>
                            <ul style="list-style-type: none">
                                <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">pages.*</label></div></li>
                                <ul style="list-style-type: none">
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">show</label></div></li>
                                    </li>
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">add</label></div></li>
                                    </li>
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">edit</label></div></li>
                                    </li>
                                </ul>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-4">
                            <b>Pages :</b>
                            <ul style="list-style-type: none">
                                <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">pages.*</label></div></li>
                                <ul style="list-style-type: none">
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">show</label></div></li>
                                    </li>
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">add</label></div></li>
                                    </li>
                                    <li><div class="form-switch"><input class="me-1 form-check-input" type="checkbox" id=""><label class="form-check-label" for="">edit</label></div></li>
                                    </li>
                                </ul>
                            </ul>
                        </div>

                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Annuler</span>
                </button>
                <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Confirmer</span>
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<!--Modale de supression-->
<div class="modal fade" id="roleDeleteModal" tabindex="-1" role="dialog" aria-labelledby="roleDeleteModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleDeleteModalTitle">Verification</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    Vous êtes sur le point de supprimé un rôle êtes vous sûr ?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Annuler</span>
                </button>
                <button type="button" class="btn btn-danger ml-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Confirmer</span>
                </button>
            </div>
        </div>
    </div>
</div>