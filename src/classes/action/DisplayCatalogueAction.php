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
            $sort = $_GET['sort'] ?? 'date_desc';

            if (!empty($recherche)) {
                $seriesList = $repo->searchSeries($recherche, $sort);
            } else {
                $seriesList = $repo->findAllSeries($sort);
            }

            $html = '<h1>Catalogue des Séries</h1>';

            $html .= <<<HTML
            <div class="row mb-3">
                <div class="col-md-8 offset-md-2 d-flex justify-content-start gap-2">
                    <p>Trier par :</p>
                    <a href="?action=catalog&query={$recherche}&sort=date_desc" 
                       class="btn btn-outline-secondary btn-sm">Date d'ajout (défaut)</a>
                    <a href="?action=catalog&query={$recherche}&sort=title_asc" 
                       class="btn btn-outline-secondary btn-sm">Titre (A-Z)</a>
                </div>
            </div>
            HTML;

            $html .= <<<HTML
            <div class="row mb-4">
                <div class="col-md-8 offset-md-2">
                    <form action="?action=catalog" method="GET" class="d-flex">
                        <input type="hidden" name="action" value="catalog">
                        <input type="hidden" name="sort" value="{$sort}">
                        
                        <input type="text" name="query" class="form-control me-2" 
                               placeholder="Rechercher par titre ou description..." 
                               value="{$recherche}">
                        <button type="submit" class="btn btn-primary">Rechercher</button>
                    </form>
                </div>
            </div>
            HTML;

            if (empty($seriesList)) {
                if (!empty($recherche)) {
                    $html .= '<p>Aucune série ne correspond à votre recherche pour "'. $recherche .'".</p>';
                } else {
                    $html .= '<p>Aucune série disponible dans le catalogue pour le moment.</p>';
                }
            } else {
                foreach ($seriesList as $serie) {
                    $html .= $serie->render(Renderer::COMPACT);
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