<?php
declare(strict_types=1);
namespace iutnc\onlyfilms\video\tracks;

use iutnc\onlyfilms\render\Renderer;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class Episode implements Renderer {

    private int $id;
    private int $number = 1;
    private string $title;
    private ?string $summary = null;
    private int $duration = 0;
    private ?string $file = null;
    private ?string $img = null;
    private ?int $seriesId = null;

    /**
     * @param int $id
     * @param int $number
     * @param string $title
     * @param string|null $summary
     * @param int $duration
     * @param string|null $file
     * @param int|null $seriesId
     */
    public function __construct(int $id, int $number, string $title, ?string $summary, int $duration, ?string $file, ?string $img,?int $seriesId)
    {
        $this->id = $id;
        $this->number = $number;
        $this->title = $title;
        $this->summary = $summary;
        $this->duration = $duration;
        $this->file = $file;
        $this->seriesId = $seriesId;
        $this->img = $img;
    }

    /**
     * @throws \Exception
     */
    public function render(int $selector): string
    {
        $repo = OnlyFilmsRepository::getInstance();
        $title = htmlspecialchars($this->getTitle(), ENT_QUOTES, 'UTF-8');
        $summary = htmlspecialchars($this->getSummary() ?? '', ENT_QUOTES, 'UTF-8');

        // preparation des id pour les boutons de nav
        $prevEpId = null;
        if ($this->getNumber() > 1){
            $prevEpId = $repo->findEpisodeIdByNumAndSeriesId($this->getNumber()-1, $this->getSeriesId());
        }

        $nextEpId = null;
        if ($this->getNumber() < $repo->getTotalEpisodesInSerie($this->getSeriesId())){
            $nextEpId = $repo->findEpisodeIdByNumAndSeriesId($this->getNumber()+1, $this->getSeriesId());
        }

        switch ($selector) {

            // COMPACT = Affichage dans la liste d'épisodes
            case self::COMPACT :
                $imgTag = '';
                $imgSrc = $this->img;

                if ($imgSrc) {
                    $imgTag = '<img src="images/' . htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') . '" alt="Miniature ' . $title . '" style="width: 120px; height: 68px; object-fit: cover; margin-right: 1rem; border-radius: 0.25rem;">';
                } else {
                    // Placeholder
                    $imgTag = '<div class="bg-light d-flex align-items-center justify-content-center text-muted" style="width: 120px; height: 68px; margin-right: 1rem; flex-shrink: 0; border-radius: 0.25rem;">16:9</div>';
                }

                $durationMin = floor($this->getDuration() / 60);

                $html = <<<HTML
                <a href='?action=display-episode&episode-id={$this->getId()}' class="list-group-item list-group-item-action d-flex align-items-start py-3">
                    $imgTag
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-1 h6">Épisode {$this->getNumber()} : {$title}</h5>
                            <small class="text-body-secondary">{$durationMin} min</small>
                        </div>
                        <p class="mb-1 small text-body-secondary">{$summary}</p>
                    </div>
                </a>
                HTML;
                break;

            // LONG = lecteur vidéo
            case self::LONG :
                $file = $this->getFile();

                $video = $file
                    ? <<<VIDEO
                      <div class="ratio ratio-16x9 mb-3 shadow-sm rounded">
                          <video controls src='video/{$file}' class="w-100 h-100"></video>
                      </div>
                      VIDEO
                    : '<div class="alert alert-warning">Aucun fichier vidéo disponible.</div>';

                // bouton suiv et prec
                $btnPrec = $prevEpId
                    ? "<a href='?action=display-episode&episode-id={$prevEpId}' class='btn btn-outline-secondary'>&laquo; Épisode Précédent</a>"
                    : "<span class='btn btn-outline-secondary disabled'>&laquo; Épisode Précédent</span>";

                $btnSuiv = $nextEpId
                    ? "<a href='?action=display-episode&episode-id={$nextEpId}' class='btn btn-primary'>Épisode Suivant &raquo;</a>"
                    : "<span class='btn btn-primary disabled'>Épisode Suivant &raquo;</span>";

                $html = <<<HTML
                <div class="episode-player my-4">
                    <h2 class="h3 mb-1">{$title}</h2>
                    <p class="lead mb-3 text-muted">{$summary}</p>
                    
                    {$video}
                    
                    <div class="d-flex justify-content-between mt-4">
                        {$btnPrec}
                        {$btnSuiv}
                    </div>
                    
                    <div class="mt-4">
                        <a href="?action=display-serie&serie-id={$this->getSeriesId()}" class="btn btn-sm btn-outline-secondary">Retour à la liste des épisodes</a>
                    </div>
                </div>
                HTML;
                break;
            default:
                throw new \Exception("Paramètre renderer incorrect");
                break;
        }
        return $html;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @param string|null $summary
     */
    public function setSummary(?string $summary): void
    {
        $this->summary = $summary;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return string|null
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * @param string|null $file
     */
    public function setFile(?string $file): void
    {
        $this->file = $file;
    }

    /**
     * @return int|null
     */
    public function getSeriesId(): ?int
    {
        return $this->seriesId;
    }

    /**
     * @param int|null $seriesId
     */
    public function setSeriesId(?int $seriesId): void
    {
        $this->seriesId = $seriesId;
    }



}