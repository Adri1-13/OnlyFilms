<?php

declare(strict_types=1);

namespace iutnc\netvod\action;


use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class AddUserAction extends Action {

    public function executeGet() : string {

        // verif qu on est pas deja connecté
        if (AuthnProvider::isSignedIn()) {
            return '<div class="alert alert-warning">Une session est déjà active. Veuillez-vous déconnecter</div>';
        }

        return <<<HTML
        <h1 class="display-6 mb-4">S'inscrire</h1>
        <form method="post" action="?action=add-user" class="container mt-4" style="max-width: 500px;">
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="passwd" class="form-label">Mot de passe :</label>
                <input type="password" class="form-control" id="passwd" name="passwd" required>
                <div class="form-text">Le mot de passe doit contenir au moins 10 caractères.</div>
            </div>
        
            <div class="mb-3">
                <label for="passwdconfirm" class="form-label">Confirmer le mot de passe :</label>
                <input type="password" class="form-control" id="passwdconfirm" name="passwdconfirm" required>
            </div>
        
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
        HTML;

    }

    public function executePost() : string {

        if (empty($_POST['email']) || empty($_POST['passwd']) || empty($_POST['passwdconfirm'])) {
            return <<<HTML
                     <div class="alert alert-warning" role="alert">
                        Les champs ne peuvent pas être vides.
                    </div>
                    <a href="?action=add-user" class="btn btn-secondary">Retour à l'inscription</a>
                    HTML;
        }

        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

        if ($email === false) {
            return <<<HTML
                     <div class="alert alert-warning" role="alert">
                        Email incorrect
                    </div>
                    <a href="?action=add-user" class="btn btn-secondary">Retour à l'inscription</a>
            HTML;
        }

        $mdp = $_POST['passwd'];
        $mdpconfirm = $_POST['passwdconfirm'];


        if ($mdp !== $mdpconfirm) {
            return <<<HTML
                    <div class="alert alert-warning" role="alert">
                        Les deux mots de passe ne correspondent pas.
                    </div>
                    <a href="?action=add-user" class="btn btn-secondary">Retour à l'inscription</a>
                    HTML;
        }

        try {
            $user = AuthnProvider::register($email, $mdp);
            $_SESSION['user'] = $user;
            return <<<HTML
                <div class="alert alert-success" role="alert">
                Inscription réussie ! Bienvenue sur Deefy {$email}.
                </div>
                <a href="?action=Default" class="btn btn-primary">Retour à l'accueil</a>
            HTML;

        } catch (AuthnException $e) {
            return <<<HTML
                     <div class="alert alert-warning" role="alert">
                        Erreur lors de l'inscription, {$e->getMessage()}
                    </div>
                    <a href="?action=add-user" class="btn btn-secondary">Retour à l'inscription</a>
                    HTML;
        }




    }

}