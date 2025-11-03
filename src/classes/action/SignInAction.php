<?php

declare(strict_types=1);

namespace iutnc\netvod\action;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\exception\AuthnException;

class SignInAction extends Action {

    public function executeGet(): string {

        // verif qu on est pas connecté
        if (AuthnProvider::isSignedIn()) {
            return '<div class="alert alert-warning">Une session est déjà active. Veuillez vous déconnecter</div>';
        }

        return <<<HTML
            <h1 class="display-6 mb-4">Connexion</h1>
                <form method="post" action="?action=signin" class="container mt-4" style="max-width: 400px;">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email :</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="passwd" class="form-label">Mot de passe :</label>
                        <input type="password" class="form-control" id="passwd" name="passwd" required>
                    </div>
                
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </form>
        HTML;

    }

    public function executePost(): string {


        // verif qu on est connecté
        if (AuthnProvider::isSignedIn()) {
            return '<div class="alert alert-warning">Une session est déjà active. Veuillez vous déconnecter</div>';
        }

        if (empty($_POST['email']) || empty($_POST['passwd'])) {
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
                <div class="container mt-4" style="max-width: 600px;">
                    <div class="alert alert-success text-center" role="alert">
                        <p>Connexion réussie</p>
                        <p>Bienvenue {$email}</p>
                    </div>
                
                    <div class="text-center mt-3">
                        <a href="?action=add-playlist" class="btn btn-primary me-2">Créer une playlist</a>
                        <a href="?action=add-track" class="btn btn-secondary">Ajouter une musique à votre playlist</a>
                    </div>
                </div>
                HTML;

        } catch (AuthnException $e) {
            sleep(3); // on attends 3s avant de répondre
            return <<<HTML
                <div class="alert alert-warning">{$e->getMessage()}</div>
                <a href="?action=signin" class="btn btn-secondary">Réessayer</a>
            HTML;

        }




    }

}