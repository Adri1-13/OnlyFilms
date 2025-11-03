<?php

declare(strict_types = 1);

namespace iutnc\deefy\exception;

// ne pas oublier le "\" devant Exception sinon plus rien ne marche à cause des namespaces !!!!!
class InvalidPropertyValueException extends \Exception {
    public function __construct(string $message) {
        parent::__construct($message);
    }
}