<?php

declare (strict_types = 1);

namespace iutnc\deefy\exception;


// ne pas oublier le "\" devant Exception sinon plus rien ne marche à cause des namespaces !!!!!
class InvalidPropertyNameException extends \Exception {
    public function __construct(string $propertyName) {
        parent::__construct("Erreur dans le nom de l'attribut demandé -> nom de la propriété invalide : $propertyName");
    }
}