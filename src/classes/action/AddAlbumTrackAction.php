<?php

declare(strict_types=1);

namespace iutnc\netvod\action;

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\AudioTrack;


class AddAlbumTrackAction extends AddAudioTrackAction {

    protected function creerTrack(array $postInfos, string $cheminFichierAudio) : AudioTrack {

        $titre = filter_var($postInfos["titreAAjouter"], FILTER_SANITIZE_SPECIAL_CHARS);
        $artiste = filter_var($postInfos["artiste"], FILTER_SANITIZE_SPECIAL_CHARS);
        $nomAlbum = filter_var($postInfos["nomAlbum"], FILTER_SANITIZE_SPECIAL_CHARS);
        $duree = (int)filter_var($postInfos["duree"], FILTER_SANITIZE_NUMBER_INT);
        $numPiste = (int)filter_var($postInfos["numPiste"], FILTER_SANITIZE_NUMBER_INT);
        $annee = (int)filter_var($postInfos["annee"], FILTER_SANITIZE_NUMBER_INT);
        $genre = filter_var($postInfos["genre"], FILTER_SANITIZE_SPECIAL_CHARS);


        $nouvellePiste = new AlbumTrack($titre, $cheminFichierAudio, $nomAlbum, $numPiste); // bizarre de choisir le numéro de piste sachant qu'on est dans une playlist
        $nouvellePiste->setDuree($duree);
        $nouvellePiste->setArtiste($artiste);
        $nouvellePiste->setAnnee($annee);
        $nouvellePiste->setGenre($genre);


        return $nouvellePiste;
    }

    protected function formulaireHtml() : string
{
        return <<<HTML
            <h1 class="display-6 mb-4 text-center">Ajout d'une piste à la playlist <strong>{$_SESSION['playlist']->nom}</strong></h1>
                <form method="POST" action="?action=add-album-track" enctype="multipart/form-data" class="w-50 mx-auto p-4">
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre de la piste</label>
                        <input type="text" class="form-control" id="titre" name="titreAAjouter" placeholder="Ex : Master of Puppets" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="artiste" class="form-label">Artiste</label>
                        <input type="text" class="form-control" id="artiste" name="artiste" placeholder="Ex : Metallica" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="duree" class="form-label">Durée (en secondes)</label>
                        <input type="number" class="form-control" id="duree" name="duree" min="0" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="nomalbum" class="form-label">Album</label>
                        <input type="text" class="form-control" id="nomalbum" name="nomAlbum" placeholder="Ex : Master of Puppets" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="numPiste" class="form-label">Numéro de piste</label>
                        <input type="number" class="form-control" id="numPiste" name="numPiste" min="0" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="annee" class="form-label">Année</label>
                        <input type="number" class="form-control" id="annee" name="annee" placeholder="Ex : 1986" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="genre" class="form-label">Genre</label>
                        <input type="text" class="form-control" id="genre" name="genre" placeholder="Rock">
                    </div>
                
                    <div class="mb-3">
                        <label for="fichier" class="form-label">Fichier audio</label>
                        <input type="file" class="form-control" id="fichier" name="inputfile" accept="audio/*" required>
                    </div>
                
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Ajouter à la playlist</button>
                    </div>
                </form>

            HTML;
    }

    protected function nomAction() : string {
        return "add-album-track";
    }
}