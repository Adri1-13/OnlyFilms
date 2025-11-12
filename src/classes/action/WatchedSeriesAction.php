<?php
namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class WatchedSeriesAction extends Action
{
    public function executeGet(): string
    {
        if (!AuthnProvider::isSignedIn()) return '<div>Utilisateur non authentifié.</div>';

        $userId = (int) AuthnProvider::getSignedInUser()->getId();
        $rows = OnlyFilmsRepository::getInstance()->getWatchedSeries($userId);

        $html = '<h2>Vos séries déjà regardées</h2>';

        if (empty($rows)) return $html . '<p>Aucune série terminée.</p>';

        $html .= '<div class="grid">';
        foreach ($rows as $r){
            $sid   = (int)$r['series_id'];
            $title = htmlspecialchars($r['title'] ?? '');
            $img   = (string)($r['img'] ?? '');
            $date  = htmlspecialchars($r['viewing_date'] ?? '');

            $html .= '<article class="card">';
            if ($img) $html .= '<img class="cover" src="images/'.$img.'" alt="Affiche '.$title.'">';
            $html .= '<h3>'.$title.'</h3>';
            if ($date) $html .= '<p>Terminée le : '.$date.'</p>';
            $html .= '<a class="btn" href="?action=display-series&series-id='.$sid.'">Détails</a>';
            $html .= '</article>';
        }
        $html .= '</div>';

        return $html;
    }

    public function executePost(): string { return $this->executeGet(); }
}
