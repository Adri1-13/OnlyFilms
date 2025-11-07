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
                <h1>Bienvenue sur OnlyFilms, {$firstName} !</h1>
                <p>
                    <a href="?action=catalog">Catalogue des séries</a>
                    <br>
                    <a href="?action=signout">Déconnexion</a>
                </p>

                <h2>Vos séries préférées :</h2>
             HTML;

            if (empty($favouriteSeries)) {
                $htmlres .= <<<HTML
                    <p>Vous n'avez pas encore ajouté de séries à vos favoris</p>
                    <p><a href="?action=catalog">Découvrir le catalogue</a></p>
                HTML;

            } else {
                // TODO : voir si mettre une div ici pour que l'affichage soit mieux
                foreach ($favouriteSeries as $serie) {
                    $htmlres .= <<<HTML
                        <p>{$serie->render(Renderer::COMPACT)}</p>
                    HTML;
                }
            }

            if (empty($seriesInProgress)) {
                $htmlres .= <<<HTML
                    <p>Vous n'avez pas de série en cours de visionnage</p>
                HTML;

            } else {
                foreach ($seriesInProgress as $seriesInProgress) {
                    $serie = $seriesInProgress['series'];
                    $episode = $seriesInProgress['last_episode'];
                    $progress = $seriesInProgress['progress_pct'];

                    $serieTitle = htmlspecialchars($serie->getTitle());
                    $serieImg = htmlspecialchars($serie->getImage());
                    $episodeTitle = htmlspecialchars($episode->getTitle());
                    $episodeNum = $episode->getNumber();
                    $episodeId = $episode->getId();

                    $htmlres .= <<<HTML
                        <article class="serie-progress">
                            <img src="images/{$serieImg}" alt="{$serieTitle}" width="150">
                            <div class="info">
                                <h3>{$serieTitle}</h3>
                                <p>Dernier épisode : Épisode {$episodeNum} - {$episodeTitle}</p>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {$progress}%"></div>
                                </div>
                                <p>Progression : {$progress}%</p>
                                <a href="?action=display-episode&episode-id={$episodeId}" class="btn btn-primary">Reprendre</a>
                            </div>
                        </article>
                    HTML;

                }

            }

            return $htmlres;
        }

        // Sinon, affichage pour visiteur
        return <<<HTML
            <h1>Bienvenue sur NetVOD </h1>
            <p>Explorez notre catalogue de séries en vous connectant <a href="?action=signin">ici</a></p>
            <p>ou en créant un compte <a href="?action=add-user">ici</a></p>
        HTML;
    }


    public function executePost(): string
    {
        return $this->executeGet();
    }
}
