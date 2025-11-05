<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\dispatch;

use iutnc\onlyfilms\action\AddFavAction;
use iutnc\onlyfilms\action\AddUserAction;
use iutnc\onlyfilms\action\DisplayEpisodeAction;
use iutnc\onlyfilms\action\SignOutAction;
use iutnc\onlyfilms\action\DefaultAction;
use iutnc\onlyfilms\action\SignInAction;
use iutnc\onlyfilms\action\DisplayCatalogueAction;

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
            case 'add-user':
                $action = new AddUserAction();
                break;
            case 'signout':
                $action = new SignOutAction();
                break;
            case 'signin':
                $action = new SignInAction();
                break;
            case 'add-fav':
                $action = new AddFavAction();
                break;
            case 'catalog':
                $action = new DisplayCatalogueAction();
                break;
            case 'display-episode':
                $action = new DisplayEpisodeAction();
                break;
            case 'default':
            default:
                $action = new DefaultAction();
                break;
        }

        $htmlres = $action->execute();

        $this->renderPage($htmlres);

    }

    private function renderPage(string $html) : void {
        $res = <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
                <title>OnlyFilms</title>
            </head>
            <body>
            {$html}
                        
            </body>
            </html>
        HTML;

        echo $res;
    }
}