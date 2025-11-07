<?php

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\action\Action;
use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\exception\OnlyFilmsRepositoryException;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class DisplayAccountAction extends Action
{

    public function executeGet(): string
    {
        $user = AuthnProvider::getSignedInUser();

        $firstName = htmlspecialchars($user->getFirstname());
        $email     = htmlspecialchars($user->getMail());
        $role      = htmlspecialchars($user->getRole());

        return <<<HTML
            <div class="container mt-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">Mon compte</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Prénom :</strong> {$firstName}</p>
                        <p><strong>Email :</strong> {$email}</p>
                        <p><strong>Rôle :</strong> {$role}</p>
                        <a href="?action=catalog" class="btn btn-primary mt-3">Retour au catalogue</a>
                    </div>
                </div>
            </div>
        HTML;
    }

    public function executePost(): string
    {
        return $this->executeGet();
    }
}