<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class AddFavAction extends Action {

    public function executeGet(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!AuthnProvider::isSignedIn()) {
            $action = new DefaultAction();
            return $action->execute();
        }

        if (isset($_GET["id"])) {
            $repo = OnlyFilmsRepository::getInstance();
            if (!$repo->isInFavList($_SESSION["user"]->getId(), $_GET["id"])){
                $repo->addFav($_SESSION["user"]->getId(), $_GET["id"]);
            }
        }

        $action = new DisplayCatalogueAction();
        return $action->execute();
    }

    public function executePost(): string {
        return $this->executeGet();
    }
}