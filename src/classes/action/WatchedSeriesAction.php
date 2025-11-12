<?php
namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class WatchedSeriesAction extends Action
{
    public function executeGet(): string
    {
        if (!AuthnProvider::isSignedIn()) {
            return '<div class="alert alert-warning my-3">Utilisateur non authentifié.</div>';
        }

        $userId = (int) AuthnProvider::getSignedInUser()->getId();
        $rows = OnlyFilmsRepository::getInstance()->getWatchedSeries($userId);

        $html = '<section class="my-4">';
        $html .= '<h2 class="h4 mb-3">Séries terminées</h2>';

        if (empty($rows)) {
            $html .= '<div class="alert alert-info">Aucune série terminée.</div>';
            $html .= '</section>';
            return $html;
        }

        // même grille que pour épisodes en coours
        $html .= '<div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3">';

        foreach ($rows as $r){
            $sid   = (int)$r['series_id'];
            $title = htmlspecialchars($r['title'] ?? '', ENT_QUOTES, 'UTF-8');
            $img   = (string)($r['img'] ?? '');
            $date  = htmlspecialchars($r['viewing_date'] ?? '', ENT_QUOTES, 'UTF-8');

            $html .= '<div class="col">';
            $html .= '  <article class="card h-100 shadow-sm">';

            // placeholder si pas image
            if ($img) {
                $html .= '    <img src="images/' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8') . '" alt="Affiche ' . $title . '" class="card-img-top" style="aspect-ratio:2/3; object-fit:cover;">';
            } else {
                $html .= '    <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted" style="aspect-ratio:2/3;">Aucune image</div>';
            }

            // carte
            $html .= '    <div class="card-body d-flex flex-column p-2">';
            $html .= '      <h3 class="h6 card-title mb-1">' . $title . '</h3>';

            if ($date) {
                // formatage date
                $formattedDate = (new \DateTime($date))->format('d/m/Y');
                $html .= '      <small class="card-text text-body-secondary mb-2">Terminée le : ' . $formattedDate . '</small>';
            }

            // bouton détails
            $html .= '      <div class="mt-auto pt-2">';
            $html .= '        <a class="btn btn-primary btn-sm w-100" href="?action=display-serie&serie-id=' . $sid . '">Détails</a>';
            $html .= '      </div>';

            $html .= '    </div>';
            $html .= '  </article>';
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</section>';

        return $html;
    }

    public function executePost(): string { return $this->executeGet(); }
}