<?php
namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\auth\User;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class InProgressSeriesAction extends Action
{
    public function execute(): string
    {
        return $this->http_method === 'POST' ? $this->executePost() : $this->executeGet();
    }

    public function executeGet(): string
    {
        AuthnProvider::getSignedInUser();

        $user = $_SESSION['user'] ?? null;
        if (!$user instanceof User) return '<div class="alert">Utilisateur non authentifié.</div>';
        $userId = (int)$user->getId();

        $repo = OnlyFilmsRepository::getInstance();
        $rows = $repo->getUserInSerieProgress($userId); 

        if (empty($rows)) return '<h2>Poursuivre la lecture</h2><p>Aucune série en cours.</p>';

        $html = '<h2>Poursuivre la lecture</h2><div class="grid">';
        foreach ($rows as $r) {
            $serie   = $r['series'];
            $episode = $r['last_episode'];
            $pct     = (int)$r['progress_pct'];

            $sid      = (int)$serie->getId();
            $title    = htmlspecialchars($serie->getTitle());
            $img      = (string)$serie->getImg();
            $epId     = (int)$episode->getId();
            $epTitle  = htmlspecialchars($episode->getTitle());

            $html .= '<article class="card">';
            if ($img !== '') {
                $html .= '<img src="'.htmlspecialchars($img).'" alt="Affiche '.$title.'" class="cover">';
            }
            $html .= '<h3>'.$title.'</h3>';
            $html .= '<div class="progress"><div class="bar" style="width: '.$pct.'%"></div></div>';
            $html .= '<p>Dernier épisode : '.$epTitle.' — '.$pct.'%</p>';
            $html .= '<a class="btn" href="?action=display-episode&id='.$epId.'">Reprendre</a>';
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
