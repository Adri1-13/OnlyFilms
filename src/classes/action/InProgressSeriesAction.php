<?php
namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\auth\User;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class InProgressSeriesAction extends Action
{
    public function executeGet(): string
    {
        if (!AuthnProvider::isSignedIn()) {
            return '<div class="alert alert-warning my-3">Utilisateur non authentifié.</div>';
        }

        $user = AuthnProvider::getSignedInUser();
        $userId = (int)$user->getId();

        $repo = OnlyFilmsRepository::getInstance();
        $rows = $repo->getUserInSerieProgress($userId);

        if (empty($rows)) {
            return '<section class="my-4"><h2 class="h4 mb-3">Poursuivre la lecture</h2><div class="alert alert-info">Aucune série en cours.</div></section>';
        }

        $html = '<section class="my-4">';
        $html .= '<h2 class="h4 mb-3">Poursuivre la lecture</h2>';

        $html .= '<div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3">';

        foreach ($rows as $r) {
            $serie   = $r['series'];
            $episode = $r['last_episode'];
            $pct     = (int)$r['progress_pct'];

            $sid     = (int)$serie->getId();
            $title   = (string)$serie->getTitle();
            $img     = (string)$serie->getImage();
            $epId    = (int)$episode->getId();
            $epTitle = (string)$episode->getTitle();

            $html .= '<div class="col">';
            $html .= '  <article class="card h-100 shadow-sm">';
            if ($img !== '') {
                $html .= '    <img src="images/' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8') . '" alt="Affiche ' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" class="card-img-top" style="aspect-ratio:2/3; object-fit:cover;">';
            } else {
                // placeholder si pas d'image
                $html .= '    <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted" style="aspect-ratio:2/3;">Aucune image</div>';
            }

            $html .= '    <div class="card-body d-flex flex-column p-2">';
            $html .= '      <h3 class="h6 card-title mb-1">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h3>';
            $html .= '      <div class="mb-2">';
            $html .= '        <div class="progress" role="progressbar" aria-label="Progression de la série" aria-valuenow="' . $pct . '" aria-valuemin="0" aria-valuemax="100">';
            $html .= '          <div class="progress-bar" style="width: ' . $pct . '%;"></div>';
            $html .= '        </div>';
            $html .= '      </div>';
            $html .= '      <p class="card-text small text-body-secondary mb-2">Dernier épisode : ' . htmlspecialchars($epTitle, ENT_QUOTES, 'UTF-8') . ' — ' . $pct . '%</p>';

            $html .= '      <div class="mt-auto pt-2">';
            $html .= '        <a class="btn btn-primary btn-sm w-100" href="?action=display-episode&episode-id=' . $epId . '">Reprendre</a>';
            $html .= '      </div>';
            $html .= '    </div>';
            $html .= '  </article>';
            $html .= '</div>';
        }

        $html .= '</div></section>';

        return $html;
    }

    public function executePost(): string
    {
        return $this->executeGet();
    }
}