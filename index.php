<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use iutnc\netvod\dispatch\Dispatcher;
use iutnc\netvod\Repository\NetVODRepository;

session_start();

//NetVODRepository::setConfig('netvod.db.ini');


$dispatcher = new Dispatcher();


$dispatcher->run();