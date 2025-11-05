<?php
declare(strict_types=1);
namespace iutnc\onlyfilms\video\lists;
use iutnc\onlyfilms\render\Renderer;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class Serie implements Renderer {

    private int $id;
    private string $title;
    private string $description;
    private string $image;
    private int $year;
    private string $dateAdded; // format 'YYYY-MM-DD'

    public function __construct (
        int $id,
        string $title,
        string $description,
        string $image,
        int $year,
        string $dateAdded
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->year = $year;
        $this->dateAdded = $dateAdded;
    }

    public function render(int $selector): string
    {
        switch ($selector) {
            case self::COMPACT:
                // titre + photo + Année
                $html = <<<HTML
                <div class="serie compact">
                    <img src="images/{$this->getImage()}" alt="Affiche de {$this->getTitle()}">
                    <h3>{$this->getTitle()}</h3>
                    <p>Année : {$this->getYear()}</p>
                </div>
                HTML;
                break;

            case self::LONG:
                //on récupere tab des épisodes
                $repo = OnlyFilmsRepository::getInstance();
                $episodes = $repo->findEpisodesBySeriesId($this->getId());
                $html = <<<HTML
                            <div class="serie long">
                                <h2>{$this->getTitle()}</h2>
                                <p>{$this->getDescription()}<p/>
                            HTML;
                foreach ($episodes as $episode) {
                    $HTML .= $episode->render(self::COMPACT);
                }
                $html .= '</div>';
                break;
            default:
                throw new \Exception("Paramètre renderer incorrect");
        }

        // On retourne le HTML généré
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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return string
     */
    public function getDateAdded(): string
    {
        return $this->dateAdded;
    }

    /**
     * @param string $dateAdded
     */
    public function setDateAdded(string $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }
}