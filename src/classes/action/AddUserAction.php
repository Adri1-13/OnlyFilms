<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;


use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\exception\AuthnException;

class AddUserAction extends Action {

    public function executeGet(): string
    {
        return <<<HTML
            <div class="row justify-content-center">
                <div class="my-4 col-md-6 col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="card-title text-center mb-4">Créer un compte</h2>
                            <form method="POST" action="?action=add-user">
                                
                                <div class="mb-3">
                                    <label for="firstname" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="mail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="mail" name="mail" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="passwd" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="passwd" name="passwd" required minlength="10" aria-describedby="passwordHelpBlock">
                                    <div id="passwordHelpBlock" class="form-text">
                                      Minimum 10 caractères.
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="passwd_confirm" class="form-label">Confirmer le mot de passe</label>
                                    <input type="password" class="form-control" id="passwd_confirm" name="passwd_confirm" required minlength="10">
                                </div>
                                
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary">S'inscrire</button>
                                </div>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
                <div class="container mt-5">
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Email invalide</h4>
                        <p>L'adresse email saisie n'est pas valide.</p>
                        <hr>
                        <a href="?action=add-user" class="btn btn-outline-danger">Retour à l'inscription</a>
                    </div>
                </div>
            HTML;
        }


        $passwd = $_POST['passwd'];
        $passwd_confirm = $_POST['passwd_confirm'];

        if ($passwd !== $passwd_confirm) {
            return <<<HTML
                <div class="container mt-5">
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Erreur</h4>
                        <p>Tous les champs sont obligatoires.</p>
                        <hr>
                        <a href="?action=add-user" class="btn btn-outline-danger">Retour à l'inscription</a>
                    </div>
                </div>
            HTML;
        }

        if (strlen($passwd) < 10) {
            return <<<HTML
                <div class="container mt-5">
                    <div class="alert alert-warning" role="alert">
                        <h4 class="alert-heading">⚠️ Mot de passe trop court</h4>
                        <p>Le mot de passe doit faire au moins 10 caractères.</p>
                        <hr>
                        <a href="?action=add-user" class="btn btn-outline-warning">Retour à l'inscription</a>
                    </div>
                </div>
            HTML;
        }

        try {
            $activationToken = AuthnProvider::register($mail, $passwd, $name, $firstname);

            return <<<HTML
                <div class="container mt-5">
                    <div class="card shadow-sm">
                        <div class="card-body p-5 text-center">
                            <h2 class="text-success mb-4">Inscription réussie !</h2>
                            <p class="text-muted mb-4">Pour finaliser votre inscription, veuillez activer votre compte :</p>
                            
                            <div class="alert alert-info">
                                <p class="mb-2"><strong>Lien d'activation :</strong></p>
                                <a href="?action=activate-account&token={$activationToken}" class="btn btn-primary btn-lg">
                                    Activer mon compte
                                </a>
                            </div>
                            
                            <small class="text-muted">Attention, ce lien est valide pendant 15 minutes.</small>
                        </div>
                    </div>
                </div>
            HTML;

        } catch (AuthnException $e) {
            return <<<HTML
                <div class="container mt-5">
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Erreur lors de l'inscription</h4>
                        <hr>
                        <a href="?action=add-user" class="btn btn-outline-danger">Retour à l'inscription</a>
                    </div>
                </div>
            HTML;
        }

    }

}