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

            $rechercheHtml = htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8');
            $triHtml = htmlspecialchars($tri, ENT_QUOTES, 'UTF-8');
            $genreHtml = htmlspecialchars($genre, ENT_QUOTES, 'UTF-8');


            if (!empty($recherche)) {
                $seriesList = $repo->searchSeries($recherche, $tri, $genre);
            } else {
                $seriesList = $repo->findAllSeries($tri, $genre);
            }

            $allGenres = $repo->getAllGenres();

            $html = '<section class="my-4">';
            $html .= '<div class="row mb-3 gy-2">';
            $html .= '<div class="col-md-7 offset-md-2">';
            // les balises input avec un type 'hidden' permettent de préserver les queryStrings
            $html .= <<<HTML
                <form action="index.php" method="GET" class="d-flex">
                    <input type="hidden" name="action" value="catalog">
                    <input type="hidden" name="tri" value="{$triHtml}">
                    <input type="hidden" name="genre" value="{$genreHtml}">
                    <input type="text" name="recherche" class="form-control me-2" placeholder="Rechercher par titre ou description" value="{$rechercheHtml}">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>
            HTML;
            $html .= '</div>';

            // Formulaire pour les genres
            $html .= '<div class="col-md-3">';
            $html .= <<<HTML
                <form action="index.php" method="GET" id="genreForm" class="d-flex align-items-center">
                    <input type="hidden" name="action" value="catalog">
                    <input type="hidden" name="tri" value="{$triHtml}">
                    <input type="hidden" name="recherche" value="{$rechercheHtml}">
                    <label for="genre" class="form-label me-2 mb-0 flex-shrink-0">Genre:</label>
                    <select name="genre" class="form-select form-select-sm" onchange="document.getElementById('genreForm').submit();">
                        <option value="">Aucun</option>
            HTML;
            //document.getElementById('genreForm').submit(), commande js pour submit le formulaire quant un élément est selectionné.

            foreach ($allGenres as $g) {
                $genreItemHtml = htmlspecialchars($g, ENT_QUOTES, 'UTF-8');
                $selected = ($g === $genre) ? 'selected' : ''; //Permet de garder le genre choisi dans la barre de navigation
                $html .= "<option value='{$genreItemHtml}' {$selected}>{$genreItemHtml}</option>";
            }
            $html .= '</select></form></div></div>';

            // bouton filtrer les séries
            $html .= <<<HTML
            <div class="row mb-4">
                <div class="col-md-8 offset-md-2 d-flex justify-content-start align-items-center gap-2 flex-wrap">
                    <span class="fw-bold me-2">Trier par :</span>
                    <a href="?action=catalog&recherche={$rechercheHtml}&tri=date_desc&genre={$genreHtml}" class="btn btn-outline-secondary btn-sm">Date d'ajout</a>
                    <a href="?action=catalog&recherche={$rechercheHtml}&tri=title_asc&genre={$genreHtml}" class="btn btn-outline-secondary btn-sm">Titre (A-Z)</a>
                    <a href="?action=catalog&recherche={$rechercheHtml}&tri=rating_desc&genre={$genreHtml}" class="btn btn-outline-secondary btn-sm">Notation</a>
                </div>
            </div>
            HTML;

            if (empty($seriesList)) {
                // Utilisation des alertes Bootstrap
                if (!empty($recherche) || !empty($genre)) {
                    $html .= '<div class="alert alert-info">Aucune série ne correspond à vos filtres.</div>';
                } else {
                    $html .= '<div class="alert alert-info">Aucune série disponible dans le catalogue pour le moment.</div>';
                }
            } else {
                // Utilisation de la grille Bootstrap pour les cartes
                $html .= '<div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3">';
                foreach ($seriesList as $serie) {
                    $html .= '<div class="col">';
                    $html .= $serie->render(Renderer::COMPACT);
                    $html .= '</div>';
                }
                $html .= '</div>';
            }

            $html .= '<div class="mt-4">';
            $html .= '<a href="?action=default" class="btn btn-outline-secondary">Retour à l\'accueil</a>';
            $html .= '</div>';

            $html.= '</section>';
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