<?php

declare(strict_types = 1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\PodcastTrack;

//require_once "AudioTrackRenderer.php";

class PodcastRenderer extends AudioTrackRenderer {

    /**
     * Classe renderer pour les podcasts, hérite de :
     * - $audioTrack représentant une piste audio aussi bien pour les musiques que les podcasts
     */


    public function __construct(PodcastTrack $podcast) {
        parent::__construct($podcast);
    }


    public function renderCompact() : string {
        return <<<HTML
                <div class="list-group-item">
                    <strong>{$this->audioTrack->titre}</strong> - {$this->audioTrack->auteur}
                </div>
            HTML;
    }

    public function renderLong(): string {
        $url = $this->getUrlAudio();
        return <<<HTML
        <div class="mb-3 p-3 border rounded">
            <h5 class="fw-bold">{$this->audioTrack->titre}</h5>
            <p class="mb-1"><strong>Auteur:</strong> {$this->audioTrack->auteur}</p>
            <p class="mb-1"><strong>Genre:</strong> {$this->audioTrack->genre}</p>
            <p class="mb-1"><strong>Durée:</strong> {$this->audioTrack->duree} secondes</p>
            <p class="mb-2"><strong>Date:</strong> {$this->audioTrack->date}</p>
            <audio class="w-100" controls src="{$url}"></audio>
        </div>
    HTML;
    }
}