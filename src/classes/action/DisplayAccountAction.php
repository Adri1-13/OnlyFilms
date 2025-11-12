<?php

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;

class DisplayAccountAction extends Action {

    public function executeGet(): string {
        $user = AuthnProvider::getSignedInUser();

        $firstName = htmlspecialchars($user->getFirstname());
        $email = htmlspecialchars($user->getMail());
        $role = htmlspecialchars($user->getRole());

        return <<<HTML
            <div class="container mt-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Mon compte</h4>
                        <a href="?action=edit-account" class="btn btn-outline-light btn-sm">Modifier</a>
                    </div>
                    <div class="card-body">
                        <p><strong>Prénom :</strong> {$firstName}</p>
                        <p><strong>Nom :</strong> {$user->getName()}</p> <p><strong>Email :</strong> {$email}</p>
                        <p><strong>Rôle :</strong> {$role}</p>
                        <a href="?action=catalog" class="btn btn-primary mt-3">Retour au catalogue</a>
                    </div>
                </div>
            </div>
        HTML;
    }

    public function executePost(): string {
        return $this->executeGet();
    }
}