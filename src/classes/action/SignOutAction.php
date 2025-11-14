<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\action\Action;
use iutnc\onlyfilms\auth\AuthnProvider;

class SignOutAction extends Action {

    public function executeGet(): string
    {
        unset($_SESSION['user']);

        session_destroy();

        $action = new DefaultAction();
        return $action->execute();

    }

    public function executePost(): string
    {
        return $this->executeGet();
    }
}