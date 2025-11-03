<?php

declare (strict_types = 1);

namespace iutnc\netvod\render;

use iutnc\netvod\audio\tracks\AudioTrack;

//require_once "Renderer.php";

abstract class AudioTrackRenderer implements Renderer {
    
    private AudioTrack $audioTrack;

    public function __construct(AudioTrack $audioTrack) {
        $this->audioTrack = $audioTrack;
    }


    public function render(int $selector) : string {
        switch ($selector) {
            case Renderer::COMPACT:
                return $this->renderCompact();
            case Renderer::LONG:
                return $this->renderLong();
            default:
                return '<div class="alert alert-warning" role="alert">
                        <strong>Erreur :</strong> Problème dans le type d\'affichage.
                        </div>';
        }
    }

    abstract public function renderCompact() : string;

    abstract public function renderLong() : string;

    public function getUrlAudio() : string {
        return $this->audioTrack->nomFichierAudio;
    }

    public function __get(string $name) : AudioTrack {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new Exception("$name . : invalid property");
    }

}