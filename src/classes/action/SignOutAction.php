<?php

declare(strict_types=1);

namespace iutnc\netvod\action;

use iutnc\netvod\action\Action;
use iutnc\netvod\auth\AuthnProvider;

class SignOutAction extends Action {

    public function executeGet(): string {

        // verif qu on est pas deja déconnecté
        if (!AuthnProvider::isSignedIn()) {
            return '<div class="alert alert-success">Vous êtes déjà déconnecté</div>';
        }

        session_unset();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        return <<<HTML
            <div class="alert alert-success">Vous êtes déconnecté</div>
        HTML;
        // TODO laisse un cookie fantôme dans le navigateur
    }

    public function executePost(): string {
        return $this->executeGet();
    }
}