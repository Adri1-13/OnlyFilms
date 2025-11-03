<?php

declare(strict_types = 1);

namespace iutnc\deefy\audio\tracks;

class AlbumTrack extends AudioTrack {
    
    private string $artiste;
    private string $album;
    private int $annee;
    private int $numPiste;

    public function __construct(string $titrePiste, string $cheminFichierAudio, string $nomAlbum, int $numPiste) {
        parent::__construct($titrePiste, $cheminFichierAudio);

        $this->album = $nomAlbum;
        $this->numPiste = $numPiste;
    }

    public function __toString(): string {
        return json_encode(get_object_vars($this));
    }

    public function __get(string $name) : string|int {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return parent::__get($name);
    }

    public function setArtiste(string $artiste) : void {
        $this->artiste = $artiste;
    }

    public function setAnnee(int $annee) : void {
        $this->annee = $annee;
    }

}