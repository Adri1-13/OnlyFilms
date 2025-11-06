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
            $favouriteSeries = $repo->getUserFavouriteSeries($user->getId());

             $htmlres = <<<HTML
                <h1>Bienvenue sur OnlyFilms, {$firstName} !</h1>
                <p>
                    <a href="?action=catalog">Catalogue des séries</a>
                    <br>
                    <a href="?action=in-progress">Séries en cours</a>
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
