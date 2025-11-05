<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\action\Action;
use iutnc\onlyfilms\auth\AuthnProvider;

class SignOutAction extends Action {

    public function executeGet(): string
    {

        if (!AuthnProvider::isSignedIn()) {
            return '<div>Vous êtes déjà déconnecté</div>';
        }
        unset($_SESSION['user']);

//        session_destroy();

        return <<<HTML
            <p>Vous êtes déconnecté</p>
        HTML;
    }

    public function executePost(): string
    {
        return $this->executeGet();
    }
}