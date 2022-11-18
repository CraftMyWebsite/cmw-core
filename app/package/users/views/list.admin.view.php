<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-sliders"></i> <span class="m-lg-auto">Gestion</span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-3">
        <div class="card">
            <div class="card-header">
                <h4>Ajouter un utilisateur</h4>
            </div>
            <div class="card-body">
                <form>
                    <h6>E-Mail :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="email" class="form-control" placeholder="E-Mail">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-at"></i>
                        </div>
                    </div>
                    <h6>Pseudo :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" placeholder="Pseudo">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </div>
                    <h6>Prénom :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" placeholder="Prénom">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                    </div>
                    <h6>Nom :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" placeholder="Nom">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-signature"></i>
                        </div>
                    </div>
                    <h6>Rôles :</h6>
                    <fieldset class="form-group">
                        <select class="form-select" id="basicSelect">
                            <option>Sampler</option>
                            <option>Wipe</option>
                            <option>Vega</option>
                        </select>
                    </fieldset>
                    <h6>Mot de passe :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="password" class="form-control" placeholder="••••">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-unlock"></i>
                        </div>
                    </div>
                    <div class="text-center"><button type="submit" class="btn btn-primary">Ajouter</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-9">
        <div class="card">
            <div class="card-header">
                <h4>Liste des utilisateurs inscrits</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center">E-mail</th>
                        <th class="text-center">Pseudo</th>
                        <th class="text-center">Prénom</th>
                        <th class="text-center">Nom</th>
                        <th class="text-center">Rôles</th>
                        <th class="text-center">Membre depuis</th>
                        <th class="text-center">Profil édité le</th>
                        <th class="text-center">Éditer</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <tr>
                        <td>leo.goigoux@gmail.com</td>
                        <td>Zomb</td>
                        <td>Léo</td>
                        <td>Goigoux</td>
                        <td>Administrateur</td>
                        <td>2022-11-14 19:32:25</td>
                        <td>2022-11-14 19:32:25</td>
                        <td>
                            <i data-bs-toggle="modal" data-bs-target="#userEditModal" class="text-primary fa-solid fa-gears"></i>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>













<!--Modale d'édition-->
<div class="modal fade modal-xl" id="userEditModal" tabindex="-1" role="dialog" aria-labelledby="roleEditModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleEditModalTitle">Édition de Zomb</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <h6>A propos :</h6>
                                            <p><b>Date de création :</b> 2022-11-14 19:32:25</p>
                                            <p><b>Date de modification :</b> 2022-11-14 19:32:25</p>
                                            <p><b>Dernière visite :</b> 2022-11-14 19:32:25</p>
                                        </div>
                                        <div class="d-lg-flex flex-wrap justify-content-between">
                                            <button type="submit" class="btn btn-primary">
                                                <span class="d-sm-block">Reset MDP</span>
                                            </button>
                                            <button type="submit" class="btn btn-warning">
                                                <span class="d-sm-block">Bloquer</span>
                                            </button>
                                            <button type="submit" class="btn btn-danger">
                                                <span class="d-sm-block">Supprimer</span>
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <h6>Image de profil :</h6>
                                            <p>Dernière modification: 2022-11-14 19:32:25</p>
                                            <div class="text-center ">
                                                <img class="w-25 border" src="https://theme.voyza.fr/public/uploads/users/default/defaultImage.jpg" alt="Image introuvable !">
                                            </div>

                                        </div>
                                            <input class="form-control w-75 mx-auto form-control-sm" type="file" id="formFile">
                                        <div class="text-center mt-1">
                                            <button type="submit" class="btn btn-primary">
                                                <span class="d-sm-block">Réinitialiser l'image</span>
                                            </button>
                                        </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <h6>E-Mail :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="email" class="form-control" placeholder="E-Mail">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-at"></i>
                                </div>
                            </div>
                            <h6>Prénom :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" placeholder="Prénom">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-id-card"></i>
                                </div>
                            </div>
                            <h6>Rôles :</h6>
                            <fieldset class="form-group">
                                <select class="form-select" id="basicSelect">
                                    <option>Sampler</option>
                                    <option>Wipe</option>
                                    <option>Vega</option>
                                </select>
                            </fieldset>
                            <h6>Mot de passe :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="password" class="form-control" placeholder="••••">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-unlock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h6>Pseudo :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" placeholder="Pseudo">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            </div>
                            <h6>Nom :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" placeholder="Nom">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-signature"></i>
                                </div>
                            </div>
                            <h6>Repeter mot de passe :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="password" class="form-control" placeholder="••••">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-unlock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-sm-block">Annuler</span>
                </button>
                <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-sm-block">Confirmer</span>
                </button>
            </div>
            </form>
        </div>
    </div>
</div>