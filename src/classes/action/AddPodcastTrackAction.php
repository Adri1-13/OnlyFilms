<?php

declare(strict_types = 1);

namespace iutnc\netvod\action;

use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use finfo;

class AddPodcastTrackAction extends AddAudioTrackAction {

    protected function creerTrack(array $postInfos, string $cheminFichierAudio) : AudioTrack {

        $titre = filter_var($postInfos["titreAAjouter"], FILTER_SANITIZE_SPECIAL_CHARS);
        $auteur = filter_var($postInfos["auteur"], FILTER_SANITIZE_SPECIAL_CHARS);
        $duree = (int)filter_var($postInfos["duree"], FILTER_SANITIZE_NUMBER_INT);
        $date = filter_var($postInfos["date"], FILTER_SANITIZE_SPECIAL_CHARS);
        $genre = filter_var($postInfos["genre"], FILTER_SANITIZE_SPECIAL_CHARS);

        $nouvellePiste = new PodcastTrack($titre, $cheminFichierAudio);
        $nouvellePiste->setAuteur($auteur);
        $nouvellePiste->setDuree($duree);
        $nouvellePiste->setDate($date);
        $nouvellePiste->setGenre($genre);


        return $nouvellePiste;
    }

    protected function formulaireHtml() : string {
        return <<<HTML
            <h1 class="display-6 mb-4 text-center">Ajout d'un podcast à la playlist <strong>{$_SESSION['playlist']->nom}</strong></h1>
            <form method="POST" action="?action=add-track" enctype="multipart/form-data" class="w-50 mx-auto p-4">
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre de la piste</label>
                    <input type="text" class="form-control" id="titre" name="titreAAjouter" placeholder="Titre du podcast" required>
                </div>
            
                <div class="mb-3">
                    <label for="auteur" class="form-label">Auteur</label>
                    <input type="text" class="form-control" id="auteur" name="auteur" placeholder="Nom de l’auteur" required>
                </div>
                
                <div class="mb-3">
                    <label for="date" class="form-label">Date de publication</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
            
                <div class="mb-3">
                    <label for="duree" class="form-label">Durée (en secondes)</label>
                    <input type="number" class="form-control" id="duree" name="duree" min="0" required>
                </div>
                
                <div class="mb-3">
                        <label for="genre" class="form-label">Genre</label>
                        <input type="text" class="form-control" id="genre" name="genre" placeholder="Ex: Documentaire">
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

    protected function nomAction(): string
    {
        return "add-track";
    }
}