<?php
declare(strict_types=1);
namespace iutnc\onlyfilms\video\tracks;

use iutnc\onlyfilms\render\Renderer;

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
    }

    /**
     * @throws \Exception
     */
    public function render(int $selector): string
    {
        switch ($selector) {
            // COMPACT = Nom + Descrption + Durée
            case self::COMPACT :
                $html = <<<HTML
                <div class="episode compact">
                    <h3>Épisode {$this->getNumber()} : {$this->getTitle()}</h3>
                    <p>{$this->getSummary()}</p>
                    <p>Durée : {$this->getDuration()} min</p>
                </div>
                HTML;
                break;
            // LONG = Lecteur vidéo
            case self::LONG :
                $file = $this->getFile();
                $video = $file ? "<video controls src='video/{$file}'></video>" : "<p>Aucun fichier vidéo disponible.</p>";
                $html = <<<HTML
                <div class="episode long">
                    <h2>{$this->getTitle()}</h2>
                    {$video}
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