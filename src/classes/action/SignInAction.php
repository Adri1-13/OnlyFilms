<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\exception\AccountActivationException;
use iutnc\onlyfilms\exception\AuthnException;

class SignInAction extends Action {

    public function executeGet(): string
    {
        return <<<HTML
            <div class="row justify-content-center">
                <div class="my-4 col-md-6 col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="card-title text-center mb-4">Connexion</h2>
                            <form method="POST" action="?action=signin">
                                <div class="mb-3">
                                    <label for="mail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="mail" name="mail" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="passwd" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="passwd" name="passwd" required>
                                </div>
                                
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary">Se connecter</button>
                                </div>
                                <p class="mt-2"><a href="?action=forgot-password">Mot de passe oublié ?</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        HTML;

    }

    public function executePost(): string
    {
        if (!isset($_POST['mail']) || !isset($_POST['passwd'])) {
            return <<<HTML
                <div class="my-4">
                <div class="alert alert-warning">Tous les champs sont obligatoires dans la page de connexion</div>
                <a class="btn btn-primary" href="?action=signin">Réessayer</a>
                </div>
            HTML;
        }

        $mail = filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL);
        $mdp = $_POST['passwd'];

        if ($mail === false) {
            return <<<HTML
                <div class="my-4">
                <div class="alert alert-warning">Email invalide</div>
                <a class="btn btn-primary" href="?action=signin">Réessayer</a>
                </div>
            HTML;
        }

        try {
            AuthnProvider::signIn($mail, $mdp);
            header('Location: ?action=default');
            exit();

        } catch (AccountActivationException $ea) {
            $errorMessage = htmlspecialchars($ea->getMessage(), ENT_QUOTES, 'UTF-8');
            return <<<HTML
                <div class="my-4">
                <div class="alert alert-warning">{$errorMessage}</div>
                <a class="btn btn-primary" href="?action=send-again-activation">Renvoyer le lien d'activation</a>
                </div>
            HTML;
        } catch (AuthnException $e) {
            $errorMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            return <<<HTML
                <div class="my-4">
                <div class="alert alert-warning">{$errorMessage}</div>
                <a class="btn btn-primary" href="?action=signin">Réessayer</a>
                </div>
            HTML;

        }
    }

}