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

            $recherche = $_GET['query'] ?? '';

            if (!empty($recherche)) {
                $seriesList = $repo->searchSeries($recherche);
            } else {
                $seriesList = $repo->findAllSeriesSortedByRating();
            }

            $rechercheHtml = htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8');
            $html = <<<HTML
            <div class="row my-4">
                <div class="col-md-8 offset-md-2">
                    <form action="index.php" method="GET" class="d-flex">
                        <input type="hidden" name="action" value="catalog">
                        <input type="text" name="query" class="form-control me-2" 
                               placeholder="Rechercher par titre ou description" 
                               value="{$rechercheHtml}">
                        <button type="submit" class="btn btn-primary">Rechercher</button>
                    </form>
                </div>
            </div>
            HTML;

            if (empty($seriesList)) {
                if (!empty($recherche)) {
                    $html .= '<div class="alert alert-info">Aucune série ne correspond à votre recherche pour "'. htmlspecialchars($recherche) .'".</div>';
                } else {
                    $html .= '<div class="alert alert-info">Aucune série disponible dans le catalogue pour le moment.</div>';
                }
            } else {

                $html .= '<div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3">';
                foreach ($seriesList as $serie) {
                    $html .= '<div class="col">';
                    $html .= $serie->render(Renderer::COMPACT);
                    $html .= '</div>';
                }
                $html .= '</div>';
            }

            $html .= '<br><a href="?action=default" class="btn btn-outline-secondary mt-4">Retour à l\'accueil</a>';

            return $html;

        } catch (\Exception $e) {
            return "<div class='alert alert-danger'>Une erreur est survenue : " . $e->getMessage() . "</div>";
        }
    }

    public function executePost(): string
    {
        return $this->executeGet();
    }
}