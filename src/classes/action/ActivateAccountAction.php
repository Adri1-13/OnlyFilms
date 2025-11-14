<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\action\Action;
use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\exception\AuthnException;

class ActivateAccountAction extends Action {

    public function executeGet(): string {


        if (!isset($_GET['token']) || empty($_GET['token'])) {
            return <<<HTML
                <div class="row justify-content-center my-5">
                    <div class="col-md-8">
                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-heading">Lien d'activation invalide</h4>
                            <p>Le lien que vous avez utilisé est manquant ou incorrect.</p>
                            <p class="mb-0">Si vous avez déjà un compte mais que celui-ci n'est pas activé, vous pouvez demander un nouveau lien.</p>
                            <a href="?action=send-again-activation" class="btn btn-danger mt-3">Renvoyer un lien d'activation</a>
                        </div>
                    </div>
                </div>
            HTML;
        }

        $token = $_GET['token'];

        try {
            // Activer le compte
            AuthnProvider::activateAccount($token);

            return <<<HTML
                <div class="row justify-content-center my-5">
                    <div class="col-md-6">
                        <div class="card shadow-sm text-center">
                            <div class="card-body p-4 p-md-5">
                                <h2 class="h3 mb-3 text-success">Votre compte a été activé !</h2>
                                <p class="lead mb-4">Vous pouvez maintenant vous connecter.</p>
                                <a href="?action=signin" class="btn btn-primary btn-lg">Se connecter</a>
                            </div>
                        </div>
                    </div>
                </div>
            HTML;

        } catch (AuthnException $e) {
            return <<<HTML
                <div class="row justify-content-center my-5">
                    <div class="col-md-8">
                        <div class="alert alert-warning" role="alert">
                            <h4 class="alert-heading">Erreur d'activation</h4>
                            <p class="mb-0">Veuillez réessayer ou <a href="?action=send-again-activation" class="alert-link">demander un nouveau lien</a>.</p>
                        </div>
                    </div>
                </div>
            HTML;
        }
    }

    public function executePost(): string {
        return $this->executeGet();
    }
}