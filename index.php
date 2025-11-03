<?php

declare(strict_types=1);

// On inclut l'autoloader de Composer pour que les classes soient trouvées automatiquement
require_once 'vendor/autoload.php';

// On importe la classe Dispatcher pour pouvoir l'utiliser
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\Repository\DeefyRepository;

session_start();

DeefyRepository::setConfig('deefy.db.ini');

// 1. On crée une nouvelle instance du dispatcher.
//    Son constructeur va automatiquement lire l'URL.
$dispatcher = new Dispatcher();

// 2. On lance l'exécution de l'application.
//    La méthode run() va choisir la bonne action et afficher la page.
$dispatcher->run();