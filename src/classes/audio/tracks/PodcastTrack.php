<?php

declare(strict_types = 1);

namespace iutnc\deefy\audio\tracks;


class PodcastTrack extends AudioTrack {
    private string $auteur;
    private string $date;

    public function __construct(string $titre, string $cheminFichierAudio) {
        parent::__construct($titre, $cheminFichierAudio);
        $this->auteur = "";
        $this->date = "";
    }

    public function __get(string $name) : string|int {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return parent::__get($name);
    }

    public function setAuteur(string $auteur) : void {
        $this->auteur = $auteur;
    }

    public function setDate(string $date) : void {
        $this->date = $date;
    }
}