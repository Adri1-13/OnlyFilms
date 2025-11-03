<?php

declare(strict_types=1);

namespace iutnc\netvod\dispatch;

use iutnc\netvod\action\AddAlbumTrackAction;
use iutnc\netvod\action\AddPlaylistAction;
use iutnc\netvod\action\AddPodcastTrackAction;
use iutnc\netvod\action\AddUserAction;
use iutnc\netvod\action\DisplayAllPlaylistUserAction;
use iutnc\netvod\action\SignOutAction;
use iutnc\netvod\action\DefaultAction;
use iutnc\netvod\action\DisplayPlaylistAction;
use iutnc\netvod\action\SignInAction;

class Dispatcher {

    private string $actionQuery;

    public function __construct() {
        if (!isset($_GET["action"])) {
            $this->actionQuery = "";
        } else {
            $this->actionQuery = $_GET["action"];
        }
    }


    public function run() : void {

        switch ($this->actionQuery) {
            case 'display-playlist':
                $action = new DisplayPlaylistAction();
                break;
            case 'add-playlist':
                $action = new AddPlaylistAction();
                break;
            case 'add-track':
                $action = new AddPodcastTrackAction();
                break;
            case 'add-album-track':
                $action = new AddAlbumTrackAction();
                break;
            case 'add-user':
                $action = new AddUserAction();
                break;
            case 'signout':
                $action = new SignOutAction();
                break;
            case 'signin':
                $action = new SignInAction();
                break;
            case 'display-all-playlists':
                $action = new DisplayAllPlaylistUserAction();
                break;
            case 'default':
            default:
                $action = new DefaultAction();
                break;
        }

        $htmlres = $action->execute();

        $this->renderPage($htmlres);

    }

    private function renderPage(string $html) : void {

        if (isset($_SESSION['user'])) {
            $temoinConnexion = "Vous êtes connecté en tant que {$_SESSION['user']->email}";
        } else {
            $temoinConnexion = "Vous n'êtes pas connecté";
        }

        $res = <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <!-- Required meta tags -->
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
            <title>Deefy</title>
        </head>
        <body>
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
              <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Deefy</a>
                  <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="?action=default">accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=add-playlist">créer une playlist</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=add-track">ajouter un podcast</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=add-album-track">ajouter une musique</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=display-all-playlists">afficher les playlists</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=add-user">s'inscrire</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=signin">se connecter</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=signout">se déconnecter</a></li>
                  </ul>
                  <span class="navbar-text">
                    {$temoinConnexion}
                  </span>
              </div>
            </nav>
            <main class="container mt-4">
                {$html}
            </main>
        </body>
        </html>
        HTML;
        echo $res;
    }
}