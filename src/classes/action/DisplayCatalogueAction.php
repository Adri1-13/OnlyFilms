<?php

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\render\Renderer;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class DisplayCatalogueAction extends Action
{

    public function executeGet(): string
    {
        try {
            $repo = OnlyFilmsRepository::getInstance();

            $recherche = isset($_GET['query']) ?? $_GET['query'];

            if (!empty($recherche)) {
                $seriesList = $repo->searchSeries($recherche);
            } else {
                $seriesList = $repo->findAllSeriesSortedByRating();
            }

            $html = '<h1>Catalogue des Séries</h1>';

            $html .= <<<HTML
            <div class="row mb-4">
                <div class="col-md-8 offset-md-2">
                    <form action="?action=catalog" method="GET" class="d-flex">
                        <input type="text" name="query" class="form-control me-2" 
                               placeholder="Rechercher par titre ou description..." 
                               value="$recherche">
                        <button type="submit" class="btn btn-primary">Rechercher</button>
                    </form>
                </div>
            </div>
            HTML;

            if (empty($seriesList)) {
                if (!empty($recherche)) {
                    $html .= '<p>Aucune série ne correspond à votre recherche pour "'. htmlspecialchars($recherche) .'".</p>';
                } else {
                    $html .= '<p>Aucune série disponible dans le catalogue pour le moment.</p>';
                }
            } else {
                $html .= '<div class="catalogue-list">';
                foreach ($seriesList as $serie) {
                    $html .= $serie->render(Renderer::COMPACT);
                }
                $html .= '</div>';
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