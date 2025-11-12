<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\render\Renderer;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class DefaultAction extends Action
{
    /**
     * Méthode GET
     * -> Affiche page d'accueil : différente si utilisateur connecté ou non.
     */
    public function executeGet(): string
    {
        // Si l’utilisateur est connecté
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            $firstName = htmlspecialchars($user->getFirstname());

            $repo = OnlyFilmsRepository::getInstance();
            // récupérer les séries favorites de l'utilisateur
            $favouriteSeries = $repo->getUserFavouriteSeries($user->getId());

            // récupérer les séries en cours
            $seriesInProgress = $repo->getUserInSerieProgress($user->getId());

            $htmlres = <<<HTML
                <section class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h1 class="h3">Bienvenue, {$firstName} !</h1>
                        <div>
                            <a href="?action=catalog" class="btn btn-primary me-2">Catalogue des séries</a>
                            
                        </div>
                    </div>
                </section>

                <section class="my-4">
                    <h2 class="h4 mb-3">Vos séries préférées</h2>
             HTML;

            if (empty($favouriteSeries)) {
                $htmlres .= <<<HTML
                    <div class="alert alert-info">
                        Vous n'avez pas encore ajouté de séries à vos favoris. 
                        <a href="?action=catalog" class="alert-link">Découvrir le catalogue</a>
                    </div>
                HTML;

            } else {
                // TODO : voir si mettre une div ici pour que l'affichage soit mieux

                // Utilisation de la même grille que le catalogue
                $htmlres .= '<div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3">';
                foreach ($favouriteSeries as $serie) {
                    // Chaque favori est une carte dans une colonne
                    $htmlres .= '<div class="col">';
                    // On appelle render(COMPACT) qui génère la carte
                    $htmlres .= $serie->render(Renderer::COMPACT);
                    $htmlres .= '</div>';
                }
                $htmlres .= '</div>';
            }
            $htmlres .= '</section>';


            $htmlres .= '<section class="my-4">';
            $htmlres .= '<h2 class="h4 mb-3">Poursuivre la lecture</h2>';

            if (empty($seriesInProgress)) {
                $htmlres .= <<<HTML
                    <div class="alert alert-info">Vous n'avez pas de série en cours de visionnage.</div>
                HTML;

            } else {
                // Utilisation de la même grille
                $htmlres .= '<div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3">';

                foreach ($seriesInProgress as $r) {
                    $serie = $r['series'];
                    $episode = $r['last_episode'];
                    $pct = (int)$r['progress_pct'];
                    $title   = htmlspecialchars((string)$serie->getTitle(), ENT_QUOTES, 'UTF-8');
                    $img     = htmlspecialchars((string)$serie->getImage(), ENT_QUOTES, 'UTF-8');
                    $epTitle = htmlspecialchars((string)$episode->getTitle(), ENT_QUOTES, 'UTF-8');
                    $epId    = (int)$episode->getId();

                    // Génération de la carte
                    $htmlres .= '<div class="col">';
                    $htmlres .= '  <article class="card h-100 shadow-sm">';
                    if ($img !== '') {
                        $htmlres .= '    <img src="images/' . $img . '" alt="Affiche ' . $title . '" class="card-img-top" style="aspect-ratio:2/3; object-fit:cover;">';
                    } else {
                        $htmlres .= '    <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted" style="aspect-ratio:2/3;">Aucune image</div>';
                    }
                    $htmlres .= '    <div class="card-body d-flex flex-column">';
                    $htmlres .= '      <h3 class="h6 card-title mb-2">' . $title . '</h3>';
                    $htmlres .= '      <div class="mb-2">';
                    $htmlres .= '        <div class="progress" role="progressbar" aria-label="Progression de la série" aria-valuenow="' . $pct . '" aria-valuemin="0" aria-valuemax="100">';
                    $htmlres .= '          <div class="progress-bar" style="width: ' . $pct . '%;"></div>';
                    $htmlres .= '        </div>';
                    $htmlres .= '      </div>';
                    $htmlres .= '      <p class="card-text small text-body-secondary mb-3">Dernier épisode : ' . $epTitle . ' — ' . $pct . '%</p>';
                    $htmlres .= '      <div class="mt-auto">';
                    $htmlres .= '        <a class="btn btn-primary w-100" href="?action=display-episode&episode-id=' . $epId . '">Reprendre</a>';
                    $htmlres .= '      </div>';
                    $htmlres .= '    </div>';
                    $htmlres .= '  </article>';
                    $htmlres .= '</div>';
                }

                $htmlres .= '</div>';
            }
            $htmlres .= '</section>';

            return $htmlres;
        }

        // affichage visiteur
        return <<<HTML
            <div class="container text-center" style="margin-top: 20vh;">
                <h1 class="display-4">Bienvenue sur OnlyFilms</h1>
                <p class="lead my-4">Explorez notre catalogue de séries en vous connectant ou en créant un compte.</p>
                <p>
                    <a href="?action=signin" class="btn btn-primary btn-lg me-2">Connexion</a>
                    <a href="?action=add-user" class="btn btn-outline-secondary btn-lg">Créer un compte</a>
                </p>
            </div>
        HTML;
    }


    public function executePost(): string
    {
        return $this->executeGet();
    }
}