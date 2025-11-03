<?php

declare(strict_types = 1);

namespace iutnc\netvod\action;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\auth\Authz;
use iutnc\netvod\exception\AuthnException;
use iutnc\netvod\render\AudioListRenderer;
use iutnc\netvod\Repository\NetVODRepository;

class DisplayPlaylistAction extends Action {

    public function executeGet() : string {

        // verif qu on est connecté
        if (!AuthnProvider::isSignedIn()) {
            return '<div class="alert alert-warning">Vous devez être connecté pour effectuer cette action.</div>';
        }

        $repo = NetVODRepository::getInstance();

        if (!isset($_GET['id'])) {
            return '<div class="alert alert-danger" role="alert"><h4 class="alert-heading">Erreur</h4><p>Veuillez spécifier une id de playlist dans la query string</p></div>';
        }

        $IDplaylist = (int)filter_var((int)$_GET['id'], FILTER_SANITIZE_NUMBER_INT);

        if (empty($IDplaylist) || $IDplaylist <= 0) { // TODO : vérifier si empty fonctionne ici --> normalement oui
            return <<<HTML
                return <div class="alert alert-danger" role="alert"><h4 class="alert-heading">Erreur</h4><p>Veuillez spécifier une ID de playlist valide dans la query string</p></div>;
            HTML;
        }


//        if ($repo->rechercheIDMaximumPlaylist() < $IDplaylist) {
//            return <<<HTML
//                <h2>Cette playlist n'existe pas</h2>
//            HTML;
//        }

        $user = $repo->trouverUser($_SESSION['user']->email);


//        if ($user->role !== $_SESSION['user']->role) { // TODO : pas sur de l'utilité de ce if mais ça serait pour tester si que le user qui est en session est bien le même que celui enregistré dans la bdd
//            return <<<HTML
//                <h2>Erreur dans le role</h2>
//            HTML;
//        }

        try {
            Authz::checkPlaylistOwner($IDplaylist);

            $playlist = $repo->trouverPlaylist_et_musiquesDedans($IDplaylist);

            if ($playlist === null) {
                return '<div class="alert alert-warning" role="alert"><h4 class="alert-heading">Aucune playlist</h4><p>Veuillez spécifier une ID de playlist valide</p></div>';
            }

            $_SESSION['playlist'] = $playlist;

            $renderer = new AudioListRenderer($playlist);
            $resHtml = $renderer->render(2);

            return <<<HTML
                    <div class="container mt-4">
                        <h1 class="display-6 mb-4">Playlist : <b>{$playlist->nom}</b></h1>
                        <div class="list-group mb-3">
                            {$resHtml}
                        </div>
                        <a href="?action=add-track" class="btn btn-primary me-2">Ajouter un podcast à votre playlist</a>
                        <a href="?action=add-album-track" class="btn btn-secondary">Ajouter une musique à votre playlist</a>
                    </div>
                    HTML;

        } catch (AuthnException $e) {
            return <<<HTML
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Accès refusé</h4>
                        <p>Vous n'avez pas la permission d'accéder à cette playlist.</p>
                        <hr>
                        <a href="?action=signin" class="btn btn-danger">Se connecter</a>
                     </div>
            HTML;
        }

    }

    public function executePost() : string {
        return "Cette page doit juste afficher une page et ne rien modifier donc jamais en POST";
    }

}