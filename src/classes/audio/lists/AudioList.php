<?php

declare(strict_types = 1);

namespace iutnc\deefy\audio\lists;


use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;


class AudioList {

    // On met les attributs en protected du coup car sinon erreurs d'accès à ces atrributs
    // dans les classes filles car on a juste le getter magique mais pas de setter
    protected ?int $id = null;
    protected string $nom;
    protected int $nbrPistes;

    // initialiser à 0 la durée totale
    protected int $dureeTotale = 0;

    // tableau qui va contenir des AudioTrack normalement
    protected array $pistes;

    public function __construct(string $nom, array $pistes = []) {
        $this->nom = $nom;
        $this->pistes = $pistes;
        $this->nbrPistes = count($pistes);

        foreach($pistes as $piste) {
            $this->dureeTotale += $piste->duree;
        }
    }

    public function __get(string $name) : string|int|array {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new InvalidPropertyNameException($name);
        }
    }

    public function setID(int $id) : void {
        $this->id = $id;
    }
}