<?php

declare(strict_types=1);

namespace iutnc\netvod\dispatch;

use iutnc\netvod\action\AddAlbumTrackAction;
use iutnc\netvod\action\AddPlaylistAction;
use iutnc\netvod\action\AddPodcastTrackAction;
use iutnc\netvod\action\AddUserAction;
use iutnc\netvod\action\DisplayAllPlaylistUserAction;
use iutnc\netvod\action\SignOutAction;
use iutnc\netvod\action\DefaultAction;
use iutnc\netvod\action\DisplayPlaylistAction;
use iutnc\netvod\action\SignInAction;

class Dispatcher {

    private string $actionQuery;

    public function __construct() {
        if (!isset($_GET["action"])) {
            $this->actionQuery = "";
        } else {
            $this->actionQuery = $_GET["action"];
        }
    }


    public function run() : void {

        switch ($this->actionQuery) {

            case 'default':
            default:
                $action = new DefaultAction();
                break;
        }

        $htmlres = $action->execute();

        $this->renderPage($htmlres);

    }

    private function renderPage(string $html) : void {


    }
}