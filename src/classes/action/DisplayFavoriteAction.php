<?php
declare(strict_types=1);

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\auth\User;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class DisplayFavoriteAction extends Action
{
    public function execute(): string
    {
        return $this->http_method === 'POST' ? $this->executePost() : $this->executeGet();
    }

    public function executeGet(): string
    {
        AuthnProvider::getSignedInUser();
        $user = $_SESSION['user'] ?? null;
        if (!$user instanceof User) {
            return '<div class="alert">Utilisateur non authentifié.</div>';
        }
        $userId = (int) $user->getId();

        $repo   = OnlyFilmsRepository::getInstance();
        $series = $repo->getUserFavouriteSeries($userId); // tableau d'objets Serie

        if (empty($series)) {
            return '<h2>Mes favoris</h2><p>Aucune série dans vos favoris.</p>';
        }

        $html = '<h2>Mes favoris</h2><div class="grid">';
        foreach ($series as $s) {
            $sid   = (int) $s->getId();
            $title = htmlspecialchars($s->getTitle());
            $img   = (string) $s->getImg();

            $html .= '<article class="card">';
            if ($img !== '') {
                $html .= '<img src="'.htmlspecialchars($img).'" alt="Affiche '.$title.'" class="cover">';
            }
            $html .= '<h3>'.$title.'</h3>';
            $html .= '<a class="btn" href="?action=display-serie&id='.$sid.'">Voir la série</a>'; // TODO ( pas sur sa dépend comment on va faire)
            $html .= '</article>';
        }
        $html .= '</div>';

        return $html;
    }

    public function executePost(): string
    {
        return $this->executeGet();
    }
}
