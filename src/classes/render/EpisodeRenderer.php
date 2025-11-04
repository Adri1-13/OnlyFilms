<?php

namespace iutnc\onlyfilms\render;

use iutnc\onlyfilms\render\Renderer;
use iutnc\onlyfilms\video\tracks\Episode;

class EpisodeRenderer implements Renderer
{
    /**
     * @throws \Exception
     */
    public static function render(int $selector,Episode $episode): string
    {
        switch ($selector) {
            // COMPACT = Nom + Descrption + Durée
            case self::COMPACT :
                $html = <<<HTML
                <div class="episode compact">
                    <h3>Épisode {$episode->getNumber()} : {$episode->getTitle()}</h3>
                    <p>{$episode->getSummary()}</p>
                    <p>Durée : {$episode->getDuration()} min</p>
                </div>
                HTML;
                break;
            // LONG = Lecteur vidéo
            case self::LONG :
                $file = $episode->getFile();
                $video = $file ? "<video controls src='video/{$file}'></video>" : "<p>Aucun fichier vidéo disponible.</p>";
                $html = <<<HTML
                <div class="episode long">
                    <h2>{$episode->getTitle()}</h2>
                    {$video}
                </div>
                HTML;
                break;
            default:
                throw new \Exception("Parametre renderer incorrect");
                break;
        }
        return $html;
    }
}