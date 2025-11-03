<?php

declare (strict_types = 1);

namespace iutnc\netvod\audio\lists;

class Album extends AudioList {

    private string $albumArtiste;
    private string $dateDeSortieAlbum;

    public function __construct(string $nom, array $pistes) {
        // faut-il vérifier que la liste de pistes fournit n'est pas vide comme l'énoncé dit
        // que la constructeur impose la présence du tableau contenant la liste des pistes de l'album ???

        if (count($pistes) === 0) {
            throw new InvalidPropertyValueException("Un album doit contenir au moins une piste");
        }

        parent::__construct($nom, $pistes);
    }

    public function setArtiste(string $artiste) : void {
        $this->albumArtiste = $artiste;
    }

    public function setDateSortie(string $date) : void {
        $this->dateDeSortieAlbum = $date;
    }
}