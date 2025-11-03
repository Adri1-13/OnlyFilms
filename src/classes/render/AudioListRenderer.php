<?php

declare (strict_types = 1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

class AudioListRenderer implements Renderer {

    private AudioList $audioList;

    public function __construct(AudioList $audioList) {
        $this->audioList = $audioList;
    }

    public function render(int $selector = 1): string
    {
//        $res = "Nom de la liste de pistes : " . "<b>" . $this->audioList->nom . "</b>"; modif TD15 ----> plus besoin de cette ligne
        // parcourt de chaque piste dans le tableau contenant les pistes d'AudioList
        $res = '<div class="d-flex flex-column gap-3 mb-3">'; // conteneur vertical avec espacement

        foreach ($this->audioList->pistes as $piste) {
            if ($piste instanceof AlbumTrack) {
                $renderer = new AlbumTrackRenderer($piste);
            } elseif ($piste instanceof PodcastTrack) {
                $renderer = new PodcastRenderer($piste);
            } else {
                continue;
            }

            $res .= $renderer->render($selector);
        }

        $res .= '</div>'; // fin conteneur vertical

        // résumé
        $res .= '<div class="alert alert-info" role="alert">';
        $res .= "<strong>Nombre de pistes :</strong> " . $this->audioList->nbrPistes . "<br>";
        $res .= "<strong>Durée totale :</strong> " . $this->audioList->dureeTotale . " secondes";
        $res .= '</div>';

        return $res;
    }
}