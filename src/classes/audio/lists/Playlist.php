<?php

declare (strict_types = 1);

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\InvalidPropertyValueException;

class Playlist extends AudioList {
    

    public function addPiste(AudioTrack $track) : void {
        $this->pistes[] = $track;
        $this->nbrPistes++;
        $this->dureeTotale += $track->duree;
    }

    public function removePiste(int $indice) : void {
        if (isset($this->pistes[$indice])) {
            $this->dureeTotale -= $this->pistes[$indice]->duree;
            unset($this->pistes[$indice]);
            $this->pistes = array_values($this->pistes);
            $this->nbrPistes = count($this->pistes);
        }
    }

    public function addListePistes(array $nouvellesPistes) : void {
        foreach ($nouvellesPistes as $nouvellePiste) { // parcourt de chaque nouvelle piste qu'on veut ajouter
            $doublon = false;
            foreach ($this->pistes as $piste) { // comparaison pour chaque nouvelle piste qu'on veut ajouter de toutes les pistes déjà présentent dans la playlist
                if ($nouvellePiste->titre === $piste->titre && $nouvellePiste->nomFichierAudio === $piste->nomFichierAudio) {
                    $doublon = true;
                    break; // pas besoin de continuer à chercher dans les pistes de la playlist puisqu'on a déjà trouvé un doublon
                }
            }
            if (!($doublon)) {
                $this->addPiste($nouvellePiste);
            }
        }
    }
}