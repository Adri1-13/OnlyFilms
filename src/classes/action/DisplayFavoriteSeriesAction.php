<?php

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\action\Action;
use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\exception\AuthnException;
use iutnc\onlyfilms\render\Renderer;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class DisplayFavoriteSeriesAction extends Action
{

    public function executeGet(): string
    {
        if (!AuthnProvider::isSignedIn()) {
            return <<<HTML
                <h2>Vous devez être connecté pour voir vos séries favorites</h2>
                <a href="?action=signin">Se connecter</a>
            HTML;
        }

        try {
            $user = AuthnProvider::getSignedInUser();
            $repo = OnlyFilmsRepository::getInstance();

            $favouritesSeries = $repo->findFavoriteSeriesByUserID($user->getId());

            $htmlres = "<h1>Vos séries favorites</h1>";

            if (empty($favouritesSeries)) {
                $htmlres .= <<<HTML
                    <p>Vous n'avez pas encore ajouté de séries à vos favoris</p>
                    <p><a href="?action=catalog">Découvrir le catalogue</a></p>
                HTML;

            } else {
                // TODO : voir si mettre une div ici pour que l'affichage soit mieux
                foreach ($favouritesSeries as $serie) {
                    $htmlres .= <<<HTML
                        <p>{$serie->render(Renderer::COMPACT)}</p>
                    HTML;
                }
            }

            $htmlres .= <<<HTML
                <br><a href="?action=default">Retour à l'accueil</a>
            HTML;

            return $htmlres;
        } catch (AuthnException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function executePost(): string
    {
        return $this->executeGet();
    }
}