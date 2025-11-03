<?php

declare(strict_types=1);

namespace iutnc\netvod\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\Repository\DeefyRepository;

class DisplayAllPlaylistUserAction extends Action {

    public function executeGet(): string {

        // verif qu on est connecté
        if (!AuthnProvider::isSignedIn()) {
            return '<div class="alert alert-warning">Vous devez être connecté pour effectuer cette action.</div>';
        }

        try {
            $user = AuthnProvider::getSignedInUser();

            $repo = DeefyRepository::getInstance();
            $playlists = $repo->trouverToutesLesPlaylistsD_unUser($user->id);

            $html = '<h1 class="display-6 mb-4">Mes playlists</h1>';

            if (empty($playlists)) {
                $html .= '<p>Vous n\'avez pas encore de playlist.</p>';
                $html .= '<a class="btn btn-primary" href="?action=add-playlist">Créer une playlist</a>';
                return $html;
            }

            $html .= '<ul class="list-group">';
            foreach ($playlists as $playlistId) {
                $playlist = $repo->trouverPlaylist_et_musiquesDedans($playlistId);
                if ($playlist !== null) {
                    $html .= "<li class='list-group-item'><a href='?action=display-playlist&id={$playlistId}'>{$playlist->nom}</a></li>";
                }
            }
            $html .= '</ul>';

            return $html;

        } catch (AuthnException $e) {
            return <<<HTML
            <div class="alert alert-danger" role="alert">
                {$e->getMessage()} <br>
                <a href="?action=signin" class="btn btn-warning mt-2">Se connecter</a>
            </div>
        HTML;
        }
    }

    public function executePost(): string {
        return "Post non supporté";
    }
}