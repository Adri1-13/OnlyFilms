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
                <label for="email">Email</label>
                <input type="email" name="email" required>
                
                <label for="passwd">Mot de passe</label>
                <input type="password" name="passwd" required>
                
                <button type="submit">Se connecter</button>
            </form>
        HTML;

    }

    public function executePost(): string
    {
        if (!isset($_POST['email']) || !isset($_POST['passwd'])) {
            return <<<HTML
                <h2>Tous les champs sont obligatoires dans la page de connexion</h2>
                <a href="?action=signin"><button>Réesayer</button></a>
            HTML;
        }

        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $mdp = $_POST['passwd'];

        if ($email === false) {
            return <<<HTML
                <h2>Email invalide</h2>
                <a href="?action=signin"><button>Réesayer</button></a>
            HTML;
        }

        try {
            $user = AuthnProvider::signIn($email, $mdp);

            $_SESSION['user'] = $user;

            return <<<HTML
                <p>Connexion réussie</p>
                <p>Bienvenue {$user->getFirstname()}</p>
            HTML;

        } catch (AuthnException $e) {
            return <<<HTML
                <p>{$e->getMessage()}</p>
                <a href="?action=signin">Réessayer</a>
            HTML;

        }
    }

}