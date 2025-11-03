<?php

declare(strict_types = 1);

namespace iutnc\netvod\action;

class DefaultAction extends Action {


    // normalement comme cette action doit juste afficher (GET) une page de défaut, elle ne doit rien modifier donc ça doit toujours appeler executeGet()
    public function executeGet(): string {
        return '<h1 class="display-6 mb-4">Bienvenue sur Deefy</h1>';
    }

    public function executePost() : string {
        return $this->executeGet();
    }


}