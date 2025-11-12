<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\action\Action;
use iutnc\onlyfilms\auth\AuthnProvider;

class SignOutAction extends Action {

    public function executeGet(): string
    {

        if (!AuthnProvider::isSignedIn()) {
            header('Location: ?action=default');
            exit();
        }
        unset($_SESSION['user']);

        session_destroy();

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        return <<<HTML
            <p>Vous êtes déconnecté</p>
            <p><a href="?action=default">Aller à l'accueil</a></p>
            <br><p>ou</p><br>
            <a href="?action=signin">Se connecter</a>
        HTML;

    }

    public function executePost(): string
    {
        return $this->executeGet();
    }
}