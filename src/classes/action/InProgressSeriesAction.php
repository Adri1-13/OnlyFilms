<?php
namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\auth\User;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class InProgressSeriesAction extends Action
{
    public function executeGet(): string
    {
        if (!AuthnProvider::isSignedIn()) return '<div>Utilisateur non authentifié.</div>';
        $user = AuthnProvider::getSignedInUser();

        $userId = (int)$user->getId();

        $repo = OnlyFilmsRepository::getInstance();
        $rows = $repo->getUserInSerieProgress($userId); 

        if (empty($rows)) return '<h2>Poursuivre la lecture</h2><p>Aucune série en cours.</p>';

        $html = '<h2>Poursuivre la lecture</h2><div>';
        foreach ($rows as $r) {
            $serie   = $r['series'];
            $episode = $r['last_episode'];
            $pct     = (int)$r['progress_pct'];

            $sid      = (int)$serie->getId();
            $title    = $serie->getTitle();
            $img      = (string)$serie->getImage();
            $epId     = (int)$episode->getId();
            $epTitle  = $episode->getTitle();

            $html .= '<article>';
            if ($img !== '') {
                $html .= '<img src="'.$img.'" alt="Affiche '.$title.'" class="cover">';
            }
            $html .= '<h3>'.$title.'</h3>';
            $html .= '<div class="progress"><div class="bar" style="width: '.$pct.'%"></div></div>';
            $html .= '<p>Dernier épisode : '.$epTitle.' — '.$pct.'%</p>';
            $html .= '<a class="btn" href="?action=display-episode&episode-id='.$epId.'">Reprendre</a>';
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
