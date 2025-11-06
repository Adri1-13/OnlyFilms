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
                <div>
                    <label for="firstname">Prénom</label>
                    <input type="text" name="firstname" required>
                </div>
                
                <div>
                    <label for="name">Nom</label>
                    <input type="text" name="name" required>
                </div>
                
                <div>
                    <label for="mail">Email</label>
                    <input type="email" name="mail" required>
                </div>
                
                <div>
                    <label for="passwd">Mot de passe (minimum 10 caractères)</label>
                    <input type="password" name="passwd" required minlength="10">
                </div>
                
                <div>
                    <label for="passwd_confirm">Confirmer le mot de passe</label>
                    <input type="password" name="passwd_confirm" required minlength="10">
                </div>
                
                <button type="submit">S'inscrire</button>
            </form>
        HTML;
    }

    public function executePost(): string
    {
        if (!isset($_POST['mail']) || !isset($_POST['passwd']) || !isset($_POST['passwd_confirm']) || !isset($_POST['firstname']) || !isset($_POST['name'])) {
            return <<<HTML
                    <p>Erreur : Tous les champs sont obligatoires</p>
                    <a href="?action=add-user">Retour à l'inscription</a>
                    HTML;
        }

        $mail = filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL);
        $firstname = htmlspecialchars(trim($_POST['firstname']));
        $name = htmlspecialchars(trim($_POST['name']));


        if ($mail === false) {
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

        if (strlen($passwd) < 10) {
            return <<<HTML
                    <p>Le mot de passe doit faire au moins 10 caractères</p>
                    <a href="?action=add-user">Retour à l'inscription</a>
            HTML;
        }

        try {
            $user = AuthnProvider::register($mail, $passwd, $name, $firstname);
            $_SESSION['user'] = $user;

            header('Location: ?action=default');
            exit();

        } catch (AuthnException $e) {
            return <<<HTML
                     <p>Erreur lors de l'inscription, {$e->getMessage()}</p>
                    <a href="?action=add-user">Retour à l'inscription</a>
            HTML;
        }
    }

}