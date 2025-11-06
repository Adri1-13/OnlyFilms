<?php

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\action\Action;
use iutnc\onlyfilms\exception\OnlyFilmsRepositoryException;
use iutnc\onlyfilms\render\Renderer;
use iutnc\onlyfilms\repository\OnlyFilmsRepository;

class DisplaySerieAction extends Action
{
    // DANS l'url : action=display-serie&serie-id=2

    public function executeGet(): string
    {
        // verifs
        if (empty($_GET['serie-id'])) {
            return '<p>Aucune série sélectionnée</p>';
        }
        $id = filter_var($_GET['serie-id'],FILTER_VALIDATE_INT);
        if ($id === null || $id === false) {
            return '<p>ID série incorrecte</p>';
        }
        $repo = OnlyFilmsRepository::getInstance();
        try {
            $serie = $repo->findSerieBySerieId($id);
            return $serie->render(Renderer::LONG);

        } catch (OnlyFilmsRepositoryException $e) {
            return '<p>ID série incorrecte</p>';
        }
    }

    public function executePost(): string
    {
        return $this->executeGet(); // POST non supporté
    }
}