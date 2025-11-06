<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use iutnc\onlyfilms\dispatch\Dispatcher;
use iutnc\onlyfilms\repository\OnlyFilmsRepository;

session_start();

OnlyFilmsRepository::setConfig('onlyfilms.db.ini');


$dispatcher = new Dispatcher();


$dispatcher->run();