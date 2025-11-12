<?php

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class EditAccountAction extends Action {

    public function executeGet(): string {
        $user = AuthnProvider::getSignedInUser();
        
        $firstname = htmlspecialchars($user->getFirstname());
        $name = htmlspecialchars($user->getName());
        $mail = htmlspecialchars($user->getMail());

        return <<<HTML
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="card-title text-center mb-4">Modifier mes informations</h2>
                        <form method="POST" action="?action=edit-account">
                            
                            <div class="mb-3">
                                <label for="mail" class="form-label">Email (non modifiable)</label>
                                <input type="email" class="form-control" name="mail" value="{$mail}" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="firstname" class="form-label">Prénom</label>
                                <input type="text" class="form-control" name="firstname" value="{$firstname}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom</label>
                                <input type="text" class="form-control" name="name" value="{$name}">
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="?action=display-account" class="btn btn-outline-secondary me-md-2">Annuler</a>
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        HTML;
    }

    public function executePost(): string {
        $user = AuthnProvider::getSignedInUser();
        $userId = $user->getId();

        $firstnameBase = htmlspecialchars($user->getFirstname());
        $firstname = !empty($_POST['firstname']) ? $_POST['firstname'] : $firstnameBase;
        $nameBase = htmlspecialchars($user->getName());
        $name = !empty($_POST['name']) ? $_POST['name'] : $nameBase;

        $repo = OnlyFilmsRepository::getInstance();
        $repo->updateUserInfo($userId, $firstname, $name);

        //Ne surtout pas oublier de modifier les infos du user en session
        $user->setFirstname($firstname);
        $user->setName($name);
        $_SESSION['user'] = $user; 

        $action = new DisplayAccountAction();
        return $action->execute();
    }
}