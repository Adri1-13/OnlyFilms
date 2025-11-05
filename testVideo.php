<?php

declare(strict_types=1);
require_once 'vendor/autoload.php';
use iutnc\onlyfilms\video\tracks\Episode;
use iutnc\onlyfilms\render\EpisodeRenderer;

$episode = new Episode(1,1,'Adrien <3','Adrien se faut sauter la boite Ã  caca',510,'beach.mp4',2);

echo EpisodeRenderer::render(1,$episode);
//echo EpisodeRenderer::render(2,$episode);