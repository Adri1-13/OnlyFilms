<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\exception\AuthnException;

class SignInAction extends Action {

    public function executeGet(): string
    {
        return <<<HTML
            <form method="POST" action="?action=signin">
                <label for="mail">Email</label>
                <input type="email" name="mail" required>
                
                <label for="passwd">Mot de passe</label>
                <input type="password" name="passwd" required>
                
                <button type="submit">Se connecter</button>
            </form>
        HTML;

    }

    public function executePost(): string
    {
        if (!isset($_POST['mail']) || !isset($_POST['passwd'])) {
            return <<<HTML
                <h2>Tous les champs sont obligatoires dans la page de connexion</h2>
                <a href="?action=signin"><button>Réesayer</button></a>
            HTML;
        }

        $mail = filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL);
        $mdp = $_POST['passwd'];

        if ($mail === false) {
            return <<<HTML
                <h2>Email invalide</h2>
                <a href="?action=signin">Réesayer</a>
            HTML;
        }

        try {
            $user = AuthnProvider::signIn($mail, $mdp);

            $_SESSION['user'] = $user;

            return <<<HTML
                <p>Connexion réussie</p>
                <p>Bienvenue {$user->getMail()}</p>
                <a href="?action=default">Aller à l'accueil</a>
            HTML;

        } catch (AuthnException $e) {
            return <<<HTML
                <p>{$e->getMessage()}</p>
                <a href="?action=signin">Réessayer</a>
            HTML;

        }
    }

}