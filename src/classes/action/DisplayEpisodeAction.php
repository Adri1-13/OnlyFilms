<?php

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\action\Action;
use iutnc\onlyfilms\exception\OnlyFilmsRepositoryException;
use iutnc\onlyfilms\render\Renderer;
use iutnc\onlyfilms\repository\OnlyFilmsRepository;

class DisplayEpisodeAction extends Action
{
    // DANS l'url : action=display-episode&episode-id2

    public function executeGet(): string
    {
        // verifs
        if (empty($_GET['episode-id'])) {
            return '<p>Aucun épisode sélectionné</p>';
        }
        $id = filter_var($_GET['episode-id'],FILTER_VALIDATE_INT);
        if ($id === false) {
            return '<p>ID épisode incorrect</p>';
        }
        $repo = OnlyFilmsRepository::getInstance();

        try {
            $episode = $repo->findEpisodeById($_GET['episode-id']);
            return $episode->render(Renderer::LONG);

        } catch (OnlyFilmsRepositoryException $e) {
            return '<p>ID épisode incorrect</p>';
        }
    }

    public function executePost(): string
    {
        return $this->executeGet(); // POST non supporté
    }
}