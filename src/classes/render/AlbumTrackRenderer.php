<?php

declare(strict_types = 1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;

//require_once "AudioTrackRenderer.php";

class AlbumTrackRenderer extends AudioTrackRenderer {

    /**
     * Classe renderer pour les albums de pistes de musique, hérite de :
     * - $audioTrack représentant une piste audio aussi bien pour les musiques que les podcasts
     */


    public function __construct(AlbumTrack $track) {
        parent::__construct($track);
    }

    public function renderCompact() : string {
        return "<p>" . $this->audioTrack->titre . " - " . $this->audioTrack->artiste . "</p>";
    }

    public function renderLong(): string {
        $html = '<div class="mb-3 p-3 border rounded">';
        $html .= '<h5 class="fw-bold">' . $this->audioTrack->titre . '</h5>';
        $html .= '<p class="mb-1"><strong>Artiste:</strong> ' . $this->audioTrack->artiste . '</p>';
        $html .= '<p class="mb-2"><strong>Album:</strong> ' . $this->audioTrack->album . '</p>';
        $html .= '<audio class="w-100" controls src="' . $this->getUrlAudio() . '"></audio>';
        $html .= '</div>';

        return $html;
    }

}