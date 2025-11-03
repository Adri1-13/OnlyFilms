<?php

declare(strict_types=1);

namespace iutnc\netvod\action;

use iutnc\netvod\audio\lists\Playlist;
use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\exception\AuthnException;
use iutnc\netvod\render\AudioListRenderer;
use iutnc\netvod\Repository\NetVODRepository;

class AddPlaylistAction extends Action {

    public function executeGet() : string {

        // verif qu on est connecté
        if (!AuthnProvider::isSignedIn()) {
            return '<div class="alert alert-warning">Vous devez être connecté pour effectuer cette action.</div>';
        }

        return <<<HTML
            <h1 class="display-6 mb-4">Création d'une playlist</h1>
            <form method="POST" action="?action=add-playlist" class="container mt-4" style="max-width: 500px;">
                <div class="mb-3">
                    <label for="nomPlaylist" class="form-label">Nom :</label>
                    <input type="text" class="form-control" id="nomPlaylist" name="nomPlaylist" placeholder="Playlist été" required>
                </div>
            
                <button type="submit" class="btn btn-primary">Créer</button>
            </form>
        HTML;

    }

    /**
     * @throws AuthnException
     */
    public function executePost(): string {

        // verif qu on est connecté
        if (!AuthnProvider::isSignedIn()) {
            return '<div class="alert alert-warning">Vous devez être connecté pour effectuer cette action.</div>';
        }

        if (empty($_POST["nomPlaylist"])) {
            return <<<HTML
                <div class="alert alert-warning">Un des champs du formulaire est manquant</div>
            HTML;

        }

        $user = AuthnProvider::getSignedInUser();

        $nomPlaylist = filter_var($_POST["nomPlaylist"], FILTER_SANITIZE_SPECIAL_CHARS);

        $playlist = new Playlist($nomPlaylist);

        $repo = NetVODRepository::getInstance();

        $playlist= $repo->saveEmptyPlaylist($playlist);

        $repo->associerPlayList_A_UnUser($playlist->id, $user->id);

        $_SESSION["playlist"] = $playlist;

        $renderer = new AudioListRenderer($playlist);
        $rendererHtmlPlaylist = $renderer->render(2);

        return <<<HTML
            <h1>Playlist {$nomPlaylist} créée.</h1>
            {$rendererHtmlPlaylist}
            <br>
            <a href="?action=add-track">Ajouter un podcast à votre playlist</a> <b>ou</b> <a href="?action=add-album-track">Ajouter une musique à votre playlist</a>
        HTML;

    }
}