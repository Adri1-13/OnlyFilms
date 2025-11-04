<?php

declare(strict_types=1);

namespace iutnc\netvod\action;

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
            $firstname = htmlspecialchars($user['firstname'] ?? '');

            return <<<HTML
                <h1>Bienvenue sur NetVOD, {$firstname} !</h1>
                <p>Accédez à votre <a href="?action=catalog">catalogue de séries</a> ou retrouvez vos 
                <a href="?action=view-favorites">séries préférées</a>.</p>
            HTML;
        }

        // Sinon, affichage pour visiteur
        return <<<HTML
            <h1>Bienvenue sur NetVOD </h1>
            <p>Explorez notre catalogue de séries en vous <a href="?action=login">connectant</a>
            ou en <a href="?action=register">créant un compte</a> gratuitement.</p>
        HTML;
    }

    /**
     * Méthode POST
     * -> Dans ce cas précis, rien à traiter en POST.
     */
    public function executePost(): string
    {
        return $this->executeGet();
    }
}
