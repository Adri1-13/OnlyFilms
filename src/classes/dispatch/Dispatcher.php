<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\dispatch;

use iutnc\onlyfilms\action\ForgotPasswordAction;
use iutnc\onlyfilms\action\ResetPasswordAction;
use iutnc\onlyfilms\action\AddFavAction;
use iutnc\onlyfilms\action\DelFavAction;
use iutnc\onlyfilms\action\AddUserAction;
use iutnc\onlyfilms\action\DisplayAccountAction;
use iutnc\onlyfilms\action\DisplayEpisodeAction;
use iutnc\onlyfilms\action\DisplaySerieAction;
use iutnc\onlyfilms\action\SignOutAction;
use iutnc\onlyfilms\action\DefaultAction;
use iutnc\onlyfilms\action\SignInAction;
use iutnc\onlyfilms\action\DisplayCatalogueAction;
use iutnc\onlyfilms\action\InProgressSeriesAction;
use iutnc\onlyfilms\action\WatchedSeriesAction;
use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\action\AddCommentAction;

class Dispatcher
{

    private string $actionQuery;

    public function __construct()
    {
        if (!isset($_GET["action"])) {
            $this->actionQuery = "";
        } else {
            $this->actionQuery = $_GET["action"];
        }
    }


    public function run(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!AuthnProvider::isSignedIn()) {
            switch ($this->actionQuery) {
                case 'signin':
                    $action = new SignInAction();
                    break;
                case 'add-user':
                    $action = new AddUserAction();
                    break;
                case 'forgot-password':
                    $action = new ForgotPasswordAction();
                    break;
                case 'reset-password':
                    $action = new ResetPasswordAction();
                    break;
                case 'default':
                default:
                    $action = new DefaultAction();
                    break;
            }
        } else {
            switch ($this->actionQuery) {
                case 'signout':
                    $action = new SignOutAction();
                    break;
                case 'catalog':
                    $action = new DisplayCatalogueAction();
                    break;
                case 'in-progress':
                    $action = new InProgressSeriesAction();
                    break;
                case 'add-fav':
                    $action = new AddFavAction();
                    break;
                case 'del-fav':
                    $action = new DelFavAction();
                    break;
                case 'display-episode':
                    $action = new DisplayEpisodeAction();
                    break;
                case 'display-serie':
                    $action = new DisplaySerieAction();
                    break;
                case 'add-comment':
                    $action = new AddCommentAction();
                    break;
                case 'display-account':
                    $action = new DisplayAccountAction();
                    break;
                case 'watched-series';
                    $action = new WatchedSeriesAction();
                    break;
                case 'default':
                default:
                    $action = new DefaultAction();
                    break;

            }
        }

        $htmlres = $action->execute();

        $this->renderPage($htmlres);

    }

    private function renderPage(string $html): void
    {

        $navbar = <<<NAV
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
              <div class="container-fluid">
                <a class="navbar-brand" href="?action=default">
                    <img src="images/logo.png" width="120" height="30" alt="OnlyFilms">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                  <ul class="navbar-nav ms-auto">
        NAV;
        // si connecté
        if (AuthnProvider::isSignedIn()) {
            $user = $_SESSION['user'];
            $firstName = htmlspecialchars($user->getFirstname());
            $navbar .= <<<NAV
                    <li class="nav-item">
                      <a class="nav-link" href="?action=catalog">Catalogue</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="?action=in-progress">Séries en cours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=watched-series">Séries terminées</a>
                    </li>
                    <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Bonjour, {$firstName}
                      </a>
                      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                        <li><a class="dropdown-item" href="?action=display-account">Mon compte</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="?action=signout">Déconnexion</a></li>
                      </ul>
                    </li>
            NAV;
        } else {
            $navbar .= <<<NAV
                    <li class="nav-item">
                      <a class="nav-link" href="?action=signin">Connexion</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="?action=add-user">Inscription</a>
                    </li>
            NAV;
        }

        $navbar .= <<<NAV
                  </ul>
                </div>
              </div>
            </nav>
        NAV;

        $res = <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
                <title>OnlyFilms</title>
            </head>
            <body>
            {$navbar}
            <main class="container">
            {$html}
            </main>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>      
            </body>
            </html>
        HTML;

        echo $res;
    }
}