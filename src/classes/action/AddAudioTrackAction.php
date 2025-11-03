<?php

declare(strict_types=1);

namespace iutnc\netvod\action;

use iutnc\netvod\audio\lists\AudioList;
use iutnc\netvod\audio\tracks\AudioTrack;
use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\auth\Authz;
use iutnc\netvod\exception\AuthnException;
use iutnc\netvod\render\AudioListRenderer;
use finfo;
use iutnc\netvod\Repository\NetVODRepository;

abstract class AddAudioTrackAction extends Action {

    public function executeGet(): string {

        // verif qu on est connecté
        if (!AuthnProvider::isSignedIn()) {
            return '<div class="alert alert-warning">Vous devez être connecté pour effectuer cette action.</div>';
        }

        if (!isset($_SESSION["playlist"])) {
            $action = new DisplayAllPlaylistUserAction();
            $html = $action->execute();

            return <<<HTML
                <div class="alert alert-warning">Vous devez d'abord sélectionner une playlist.</div>
                $html
            HTML;
        }

        try {
            Authz::checkPlaylistOwner($_SESSION['playlist']->id);
        } catch (AuthnException $e) {
            return <<<HTML
                <h2>{$e->getMessage()}</h2>
            HTML;
        }

        return $this->formulaireHtml();

    }


    public function executePost(): string {

        // verif qu on est connecté
        if (!AuthnProvider::isSignedIn()) {
            return '<div class="alert alert-warning">Vous devez être connecté pour effectuer cette action.</div>';
        }

        if (!isset($_SESSION["playlist"])) {
            return <<<HTML
                <h1>Pas de playlist créée</h1>
                <p>Vous pouvez en créer une ici : </p>
                <a href="?action=add-playlist">Créer une playlist</a>
            HTML;
        }

        try {
            Authz::checkPlaylistOwner($_SESSION['playlist']->id);
        } catch (AuthnException $e) {
            return <<<HTML
                <h2>{$e->getMessage()}</h2>
            HTML;
        }

        if (!isset($_FILES['inputfile']) || $_FILES['inputfile']['error'] !== UPLOAD_ERR_OK) {
            return '<div class="alert alert-danger" role="alert">Erreur durant le transfert</div>';
        }

        $tmp = $_FILES['inputfile']['tmp_name']; // chemin temporaire sur le serveur (dans le répertoire D:\XAMPP\tmp)



        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $vraiTypeMimeFichierTemp = $finfo->file($tmp);

        if ($vraiTypeMimeFichierTemp !== "audio/mpeg") {
            return '<div class="alert alert-danger" role="alert">'
                . htmlspecialchars("Type de fichier interdit : {$vraiTypeMimeFichierTemp}")
                . '</div>';
        }

        $cheminStockageFinal = __DIR__ . "/../../../audio/"; // chemin sur ma machine

        $nomFichierFinal = uniqid() . ".mp3";

        /*
         * La structure des fichiers sur machine != structure fichiers serv web
         */
        $cheminPourLeServeurWeb = "audio/" . $nomFichierFinal; // chemin sur le serveur web


        $dest = $cheminStockageFinal . $nomFichierFinal;


        if (!move_uploaded_file($tmp, $dest)) {
            return "<h2>Erreur pendant le déplacement du fichier</h2>";
        }


        $nouvellePiste = $this->creerTrack($_POST, $cheminPourLeServeurWeb);

        $repo = NetVODRepository::getInstance();

        $nouvellePiste = $repo->sauvegarderPiste($nouvellePiste);


        $playlist = $_SESSION["playlist"];
        $playlist->addPiste($nouvellePiste);

        $repo->addTrackToPlaylist($playlist->id, $nouvellePiste->id);

        $_SESSION['playlist'] = $playlist;

        $renderer = new AudioListRenderer($_SESSION["playlist"]);
        $resultHtml = $renderer->render(2);

        return <<<HTML
            <h1>{$nouvellePiste->titre} ajoutée à la playlist {$_SESSION["playlist"]->nom}</h1>
            {$resultHtml}
            <br>
            <a href="?action={$this->nomAction()}">Ajouter une nouvelle piste</a>
        HTML;

    }


    abstract protected function creerTrack(array $postInfos, string $cheminFichierAudio) : AudioTrack;

    protected abstract function formulaireHtml() : string;

    protected abstract function nomAction() : string;
}
