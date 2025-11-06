<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\action\Action;
use iutnc\onlyfilms\auth\AuthnProvider;

class SignOutAction extends Action {

    public function executeGet(): string
    {

        if (!AuthnProvider::isSignedIn()) {
            return <<<HTML
                <div>Vous êtes déjà déconnecté</div>
            HTML;
        }
        unset($_SESSION['user']);

//        session_destroy();

        return <<<HTML
            <p>Vous êtes déconnecté</p>
            <a href="?action=default">Aller à l'accueil</a>
            <a href="?action=signin">Se connecter</a>
        HTML;

    }

    public function executePost(): string
    {
        return $this->executeGet();
    }
}