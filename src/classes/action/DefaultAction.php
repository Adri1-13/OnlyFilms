<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;

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

            // TODO : modifis actions en fonction de leurs vrais noms
            return <<<HTML
                <h1>Bienvenue sur OnlyFilms, {$firstName} !</h1>
                <p>
                    <a href="?action=catalog">Catalogue des séries</a>
                    <br>
                    <a href="?action=in-progress">Séries en cours</a>
                    <br>
                    <a href="?action=view-favorites">Mes favoris</a>
                    <br>
                    <a href="?action=signout">Déconnexion</a>
                </p>
            HTML;
        }

        // Sinon, affichage pour visiteur
        return <<<HTML
            <h1>Bienvenue sur NetVOD </h1>
            <p>Explorez notre catalogue de séries en vous <a href="?action=signin">connectant</a>
            ou en <a href="?action=add-user">créant un compte</a></p>
        HTML;
    }


    public function executePost(): string
    {
        return $this->executeGet();
    }
}
