<?php
namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\exception\OnlyFilmsRepositoryException;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class ForgotPasswordAction extends Action
{
    public function executeGet(): string {
        return <<<HTML
            <div class="row justify-content-center my-4">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <h2 class="card-title text-center mb-4">Mot de passe oublié</h2>
                            <p class="text-center text-muted mb-4">Entrez votre email pour recevoir un lien de réinitialisation.</p>
                            
                            <form method="POST" action="?action=forgot-password">
                                <div class="mb-3">
                                    <label for="mail" class="form-label">Adresse email</label>
                                    <input type="email" class="form-control" id="mail" name="mail" required>
                                </div>
                                <div class="d-grid mt-4">
                                    <button class="btn btn-primary" type="submit">Envoyer le lien</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        HTML;
    }

    public function executePost(): string {
        $email = trim($_POST['mail'] ?? '');
        if ($email === '') return '<div class="container mt-4"><p>Email invalide.</p></div>' . $this->executeGet();

        $repo = OnlyFilmsRepository::getInstance();
        try {
            $user = $repo->findUser($email);
            // si le user n'existe déjà pas
        } catch (OnlyFilmsRepositoryException $e) {
            return <<<HTML
                <div class="my-4 alert alert-warning">Si le compte existe un mail a été envoyé</div>
            HTML;
        }

        // crée le token et REDIRIGE direct vers la page de reset
        $token = $repo->createPasswordResetToken((int)$user->getId());


        return <<<HTML
        <div class="container mt-5">
            <div class="alert alert-info">
                <h4>Email envoyé</h4>
                <p>Si un compte existe avec cette adresse mail, vous recevrez un lien de réinitialisation.</p>
                <p>Pour la sae, le lien a été généré ci-dessous (en réalité, il serait envoyé par email).</p>
                <div class="alert alert-warning mt-3">
                    <p><strong>Lien de réinitialisation (valable 30 minutes) :</strong></p>
                    <a href="?action=reset-password&token={$token}" class="btn btn-primary">Réinitialiser mon mot de passe</a>
                </div>
            </div>
        </div>
    HTML;
    }
}
