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
            $firstname = htmlspecialchars($user->getFirstname());

            // TODO : modifis actions en fonction de leurs vrais noms
            return <<<HTML
                <h1>Bienvenue sur OnlyFilms, {$firstname} !</h1>
                <p>Accédez au catalogue de séries <a class="btn btn-primary" href="?action=catalog"></a>
                <br>
                Reprendre dans vos séries en cours <a class="btn btn-primary" href="?action=in-progress"></a>
                <br>
                Retrouver vos séries préférées <a href="?action=view-favorites"></a></p>
            HTML;
        }

        // Sinon, affichage pour visiteur
        return <<<HTML
            <h1>Bienvenue sur OnlyFilms</h1>
            <p>Explorez notre catalogue de séries en vous <a href="?action=signin">connectant</a>
            ou en <a href="?action=add-user">créant un compte</a></p>
        HTML;
    }


    public function executePost(): string
    {
        return $this->executeGet();
    }
}
