<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use iutnc\onlyfilms\dispatch\Dispatcher;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

session_start();

//OnlyFilmsRepository::setConfig('netvod.db.ini');


$dispatcher = new Dispatcher();


$dispatcher->run();