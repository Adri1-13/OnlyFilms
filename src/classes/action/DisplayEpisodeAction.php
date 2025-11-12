<?php

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\action\Action;
use iutnc\onlyfilms\exception\OnlyFilmsRepositoryException;
use iutnc\onlyfilms\render\Renderer;
use iutnc\onlyfilms\repository\OnlyFilmsRepository;

class DisplayEpisodeAction extends Action
{
    // DANS l'url : action=display-episode&episode-id=2

    public function executeGet(): string
    {
        // verifs
        if (empty($_GET['episode-id'])) {
            return '<div class="alert alert-warning my-3">Aucun épisode sélectionné.</div>';
        }
        $id = filter_var($_GET['episode-id'],FILTER_VALIDATE_INT);
        if ($id === false) {
            return '<div class="alert alert-warning my-3">ID d\'épisode incorrect.</div>';
        }
        $repo = OnlyFilmsRepository::getInstance();

        try {
            $episode = $repo->findEpisodeById($id);

            $html = $episode->render(Renderer::LONG);

            $repo->addWatchedEpisode($_SESSION['user']->getId(),$episode->getId());
            $repo->cleanupSeriesIfCompleted((int)$_SESSION['user']->getId(), (int)$episode->getSeriesId());

            return $html;

        } catch (OnlyFilmsRepositoryException $e) {
            return '<div class="alert alert-danger my-3">ID d\'épisode incorrect ou introuvable.</div>';
        }
    }

    public function executePost(): string
    {
        return $this->executeGet(); // POST non supporté
    }
}