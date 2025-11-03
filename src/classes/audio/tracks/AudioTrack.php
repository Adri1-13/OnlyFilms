<?php

declare (strict_types = 1);


namespace iutnc\netvod\audio\tracks;

use iutnc\netvod\exception\InvalidPropertyNameException;
use iutnc\netvod\exception\InvalidPropertyValueException;


abstract class AudioTrack {

    protected ?int $id = null;
    private string $titre;
    private string $nomFichierAudio;
    private int $duree;
    private string $genre;

    public function __construct(string $titre, string $cheminFichierAudio) {
        $this->titre = $titre;

        $partiesTab = explode("/", $cheminFichierAudio);
        $this->nomFichierAudio = $cheminFichierAudio; // modif ici

        $this->duree = 0;
        $this->genre = "";
    }

    /**
     * Getter magique
     */

    public function __get(string $name) : int|string {
        if (!(property_exists($this, $name))) {
            throw new InvalidPropertyNameException("Cet attribut n'existe pas : $name");
        }
        return $this->$name;

    }

    public function setDuree(int $duree) : void {
        if ($duree < 0) {
            throw new InvalidPropertyValueException("La durée ne peut pas être négative");
        }
        $this->duree = $duree;
    }

    public function setGenre(string $genre) : void {
        $this->genre = $genre;
    }

    public function setID(int $id) : void {
        $this->id = $id;
    }

}