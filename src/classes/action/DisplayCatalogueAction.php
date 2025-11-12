<?php

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\render\Renderer;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class DisplayCatalogueAction extends Action {

    public function executeGet(): string {
        try {
            $repo = OnlyFilmsRepository::getInstance();

            $recherche = $_GET['recherche'] ?? '';
            $tri = $_GET['tri'] ?? 'date_desc';
            $genre = $_GET['genre'] ?? '';

            if (!empty($recherche)) {
                $seriesList = $repo->searchSeries($recherche, $tri, $genre);
            } else {
                $seriesList = $repo->findAllSeries($tri, $genre);
            }

            $allGenres = $repo->getAllGenres();

            $html = '<h1>Catalogue des Séries</h1>';
            $html .= '<div class="row mb-3 gy-2">';
            $html .= '<div class="col-md-7 offset-md-2">';
            //Les balise input avec un type 'hidden' permettent de préserver les queryStrings, je les répète donc à chaque fois
            //Formulaire pour la barre de recherche
            $html .= <<<HTML
                <form action="?action=catalog" method="GET" class="d-flex">
                    <input type="hidden" name="action" value="catalog">
                    <input type="hidden" name="tri" value="{$tri}">
                    <input type="hidden" name="genre" value="{$genre}">
                    <input type="text" name="recherche" class="form-control me-2" placeholder="Rechercher par titre ou description..." value="{$recherche}">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>
            HTML;
            $html .= '</div>';

            //Formulaire pour les genres
            $html .= '<div class="col-md-3">';
            $html .= <<<HTML
                <form action="?action=catalog" method="GET" id="genreForm" class="d-flex align-items-center">
                    <input type="hidden" name="action" value="catalog">
                    <input type="hidden" name="tri" value="{$tri}">
                    <input type="hidden" name="recherche" value="{$recherche}">
                    <label for="genre" class="form-label me-2 mb-0 flex-shrink-0">Genre:</label>
                    <select name="genre" class="form-select form-select-sm" onchange="document.getElementById('genreForm').submit();">
                        <option value="">Aucun</option>
            HTML;
            //document.getElementById('genreForm').submit(), commande js pour submit le formulaire quant un élément est selectionné.

            foreach ($allGenres as $g) {
                $selected = ($g === $genre) ? 'selected' : ''; //Permet de garder le genre choisi dans la barre de navigation
                $html .= "<option value='{$g}' {$selected}>{$g}</option>";
            }
            $html .= '</select></form></div></div>';

            //Boutons pour filtrer les séries
            $html .= <<<HTML
            <div class="row mb-4">
                <div class="col-md-8 offset-md-2 d-flex justify-content-start align-items-center gap-2">
                    <p class="fw-bold">Trier par :</p>
                    <a href="?action=catalog&recherche={$recherche}&tri=date_desc&genre={$genre}" class="btn btn-outline-secondary btn-sm">Date d'ajout</a>
                    <a href="?action=catalog&recherche={$recherche}&tri=title_asc&genre={$genre}" class="btn btn-outline-secondary btn-sm">Titre (A-Z)</a>
                    <a href="?action=catalog&recherche={$recherche}&tri=rating_desc&genre={$genre}" class="btn btn-outline-secondary btn-sm">Notation</a>
                </div>
            </div>
            HTML;

            if (empty($seriesList)) {
                if (!empty($recherche) || !empty($genre)) {
                    $html .= '<p>Aucune série ne correspond à vos filtres.</p>';
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
            return "Une erreur est survenue lors de l'affichage du catalogue : " . $e->getMessage() . $e->getTraceAsString();
        }
    }

    public function executePost(): string
    {
        return $this->executeGet();
    }
}