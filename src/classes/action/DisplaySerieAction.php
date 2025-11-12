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
            return '<div class="alert alert-warning my-3">Aucune série sélectionnée.</div>';
        }
        $id = filter_var($_GET['serie-id'],FILTER_VALIDATE_INT);
        if ($id === null || $id === false) {
            return '<div class="alert alert-warning my-3">ID de série incorrect.</div>';
        }
        $repo = OnlyFilmsRepository::getInstance();
        try {
            $serie = $repo->findSerieBySerieId($id);
            return <<<HTML
                    {$serie->render(Renderer::LONG)}
                    <div class="mt-4">
                        <a href="?action=catalog" class="btn btn-outline-secondary">Retour au catalogue</a>
                    </div>
                    HTML;

        } catch (OnlyFilmsRepositoryException $e) {
            //echo $e->getMessage();
            return '<div class="alert alert-danger my-3">ID de série incorrect ou introuvable.</div>';
        }
    }

    public function executePost(): string
    {
        return $this->executeGet(); // POST non supporté
    }
}