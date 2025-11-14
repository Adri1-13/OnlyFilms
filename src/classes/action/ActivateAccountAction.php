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
                <div class="container mt-5">
                    <div class="alert alert-danger">
                        <h2>Lien d'activation invalide</h2>
                        <p>Si vous avez déjà un compte mais que celui-ci n'est pas activé, vous pouvez demander un nouveau lien d'activation</p>
                        <a href="?action=send-again-activation" class="btn btn-primary">Renvoyer le lien d'activation</a>
                    </div>
                </div>
            HTML;
        }

        $token = $_GET['token'];

        try {
            // Activer le compte
            AuthnProvider::activateAccount($token);

            return <<<HTML
                <div class="container mt-5">
                    <div class="card shadow-sm">
                        <div class="card-body p-5 text-center">
                            <h2 class="text-success mb-4">Votre compte a été activé</h2>
                            <p class="lead">Vous pouvez maintenant vous connecter.</p>
                            <a href="?action=signin" class="btn btn-primary btn-lg mt-3">Se connecter</a>
                        </div>
                    </div>
                </div>
            HTML;
        } catch (AuthnException $e) {
            return "<h2>" . $e->getMessage() . "</h2>";
        }
    }

    public function executePost(): string {
        return $this->executeGet();
    }
}