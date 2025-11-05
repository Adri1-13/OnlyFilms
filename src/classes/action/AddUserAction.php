<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;


use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\exception\AuthnException;

class AddUserAction extends Action {

    public function executeGet(): string
    {
        return <<<HTML
            <h1>Créer un compte</h1>
            <form method="POST" action="?action=add-user">
            
                    <label for="email">Email</label>
                    <input type="email" name="email" required>
                    
                    <label for="passwd">Mot de passe (minimum 10 caractères)</label>
                    <input type="password" name="passwd">
                    
                    <label for="passwd_confirm">Confirmer le mot de passe</label>
                    <input type="password" name="passwd_confirm">
                    
                <button type="submit">S'inscrire</button>
            </form>
        HTML;
    }

    public function executePost(): string
    {
        if (!isset($_POST['email']) || !isset($_POST['passwd']) || !isset($_POST['passwd_confirm'])) {
            return <<<HTML
                    <p>Erreur : Les champs ne peuvent pas être vides</p>
                    <a href="?action=add-user">Retour à l'inscription</a>
                    HTML;
        }

        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

        if ($email === false) {
            return <<<HTML
                    <p>Email incorrect</p>
                    <a href="?action=add-user">Retour à l'inscription</a>
            HTML;
        }


        $passwd = $_POST['passwd'];
        $passwd_confirm = $_POST['passwd_confirm'];

        if ($passwd !== $passwd_confirm) {
            return <<<HTML
                    <p>Les deux mots de passe ne correspondent pas.</p>
                    <a href="?action=add-user">Retour à l'inscription</a>
                    HTML;
        }

        if (count($passwd) <= 10) {
            return <<<HTML
                    <p>Les mots de passe doivent faire au moins 10 caractères</p>
                    <a href="?action=add-user">Retour à l'inscription</a>
            HTML;
        }

        try {
            $user = AuthnProvider::register($email, $passwd);

            return <<<HTML
                <p>Connexion réussie</p>
                <p>Bienvenue {$user->getMail()}</p>
            HTML;


            $_SESSION['user'] = $user;
        } catch (AuthnException $e) {
            return <<<HTML
                     <p>Erreur lors de l'inscription, {$e->getMessage()}</p>
                    <a href="?action=add-user">Retour à l'inscription</a>
            HTML;
        }
    }

}