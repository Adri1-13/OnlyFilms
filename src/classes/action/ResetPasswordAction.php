<?php
namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class ResetPasswordAction extends Action
{
    private function form(string $token, string $err = ''): string {
        $errHtml = $err ? '<div class="alert alert-danger">'.$err.'</div>' : '';
        $t = htmlspecialchars($token);
        return <<<HTML
        <div class="container mt-4">
          <h2>Réinitialiser le mot de passe</h2>
          {$errHtml}
          <form method="post" class="mt-3">
            <input type="hidden" name="token" value="{$t}">
            <div class="mb-3">
              <label class="form-label">Nouveau mot de passe</label>
              <input type="password" class="form-control" name="p1" required minlength="10">
            </div>
            <div class="mb-3">
              <label class="form-label">Confirmer</label>
              <input type="password" class="form-control" name="p2" required minlength="10">
            </div>
            <button class="btn btn-primary" type="submit">Valider</button>
          </form>
        </div>
        HTML;
    }

    public function executeGet(): string {
        $token = $_GET['token'] ?? '';
        if ($token === '') return '<div class="container mt-4"><p>Lien invalide.</p></div>';

        $repo = OnlyFilmsRepository::getInstance();
        $row = $repo->getValidResetToken($token);
        if (!$row) return '<div class="container mt-4"><p>Lien invalide ou expiré.</p></div>';

        return $this->form($token);
    }

    public function executePost(): string {
        $token = $_POST['token'] ?? '';
        $p1 = $_POST['p1'] ?? '';
        $p2 = $_POST['p2'] ?? '';

        $repo = OnlyFilmsRepository::getInstance();
        $row = $repo->getValidResetToken($token);
        if (!$row) return '<div class="container mt-4"><p>Lien invalide ou expiré.</p></div>';

        if ($p1 !== $p2)  return $this->form($token, 'Les mots de passe ne correspondent pas.');
        if (strlen($p1) < 8) return $this->form($token, '8 caractères minimum.');

        $repo->updateUserPassword((int)$row['user_id'], $p1);
        $repo->consumeResetToken($token);

        // petit lien pour retourner se connecter
        return '<div class="container mt-4"><div class="alert alert-success">Mot de passe réinitialisé. <a href="?action=signin">Se connecter</a></div></div>';
    }
}
