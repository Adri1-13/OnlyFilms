<?php

declare(strict_types=1);
namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\action\Action;
use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\exception\AuthnException;

class SendAgainActivationTokenAction extends Action
{

    public function executeGet(): string {

        return <<<HTML
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <h2 class="card-title text-center mb-4">Renvoyer le lien d'activation</h2>
                                <p class="text-muted text-center mb-4">Entrez votre email pour recevoir un nouveau lien d'activation.</p>
                                
                                <form method="POST" action="?action=send-again-activation">
                                    <div class="mb-3">
                                        <label for="mail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="mail" name="mail" required>
                                    </div>
                                    
                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-primary">Envoyer</button>
                                    </div>
                                </form>
                                
                                <div class="text-center mt-3">
                                    <a href="?action=default">Retour à l'accueil</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        HTML;
    }

    public function executePost(): string {
        if (!isset($_POST['mail'])) {
            return <<<HTML
                <div class="alert alert-danger">Email requis</div>
                <a href="?action=resend-activation">Retour</a>
            HTML;
        }

        $mail = filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL);

        if ($mail === false) {
            return <<<HTML
                <div class="alert alert-danger">Email invalide</div>
                <a href="?action=resend-activation">Retour</a>
            HTML;
        }

        try {

            $newToken = AuthnProvider::sendAgainActivationToken($mail);

            // une fois qu'on a regénéré un nouveau token on revalide le user
            header('Location: ?action=activate-account&token=' . $newToken);
            exit();

        } catch (AuthnException $e) {
            return <<<HTML
                <div class="container mt-5">
                    <div class="alert alert-danger">
                        <p>{$e->getMessage()}</p>
                        <a href="?action=send-again-activation">Réessayer</a>
                    </div>
                </div>
            HTML;
        }
    }
}