<?php
namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class ForgotPasswordAction extends Action
{
    public function executeGet(): string {
        return <<<HTML
        <div class="container mt-4">
          <h2>Mot de passe oublié</h2>
          <form method="post" class="mt-3">
            <div class="mb-3">
              <label for="mail" class="form-label">Adresse email</label>
              <input type="email" class="form-control" id="mail" name="mail" required>
            </div>
            <button class="btn btn-primary" type="submit">Générer le lien</button>
          </form>
        </div>
        HTML;
    }

    public function executePost(): string {
        $email = trim($_POST['mail'] ?? '');
        if ($email === '') return '<div class="container mt-4"><p>Email invalide.</p></div>' . $this->executeGet();

        $repo = OnlyFilmsRepository::getInstance();
        $user = $repo->findUser($email);

        if (!$user) {
            // on dit juste non trouvé
            return '<div class="container mt-4"><p>Email inconnu.</p></div>' . $this->executeGet();
        }

        // crée le token et REDIRIGE direct vers la page de reset
        $token = $repo->createPasswordResetToken((int)$user->getId());

        // URL absolue propre
        $base = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $url  = $base . '?action=reset-password&token=' . urlencode($token);

        header('Location: ' . $url);
        return ''; // stop rendu après redirection
    }
}
