<?php

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\render\Renderer;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class DisplayCatalogueAction extends Action
{

    public function executeGet(): string
    {
        if (!AuthnProvider::isSignedIn()) {
            $defaultAction = new DefaultAction();
            return $defaultAction->executeGet();
        }

        try {
            $repo = OnlyFilmsRepository::getInstance();
            $seriesList = $repo->findAllSeries();

            $html = '<h1>Catalogue des Séries</h1>';

            if (empty($seriesList)) {
                $html .= '<p>Aucune série disponible dans le catalogue pour le moment.</p>';
            } else {
                foreach ($seriesList as $serie) {
                    $html .= $serie->render(Renderer::COMPACT); //
                }
            }

            $html .= '<br><a href="?action=default">Retour à l\'accueil</a>';

            return $html;

        } catch (\Exception $e) {
            return "Une erreur est survenue lors de l'affichage du catalogue : " . $e->getMessage();
        }
    }

    public function executePost(): string
    {
        return $this->executeGet();
    }
}