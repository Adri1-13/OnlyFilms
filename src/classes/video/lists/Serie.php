<?php
declare(strict_types=1);
namespace iutnc\onlyfilms\video\lists;
use iutnc\onlyfilms\render\Renderer;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;
use iutnc\onlyfilms\video\tracks\Episode;

class Serie implements Renderer {

    private int $id;
    private string $title;
    private string $description;
    private string $image;
    private int $year;
    private string $dateAdded; // format 'YYYY-MM-DD'
    private array $episodes;

    public function __construct (
        int $id,
        string $title,
        string $description,
        string $image,
        int $year,
        string $dateAdded,
        ?array $episodes
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->year = $year;
        $this->dateAdded = $dateAdded;
        $this->episodes = $episodes ?? [];
    }

    public function addEpisode(Episode $episode) : void {
        $this->episodes[] = $episode;
    }

    public function addEpisodes(array $episodes): void {
        foreach ($episodes as $episode) {
            $this->episodes[] = $episode;
        }
    }

    public function render(int $selector): string
    {
        $serieId = $this->getId(); // Récupérer l'ID pour les liens
        $repo = OnlyFilmsRepository::getInstance();
        $fav = (!$repo->isInFavList($_SESSION['user']->getId(), $this->id)) ? "<p><a href='?action=add-fav&id={$serieId}'>Ajouter aux favoris</a></p>" : "<p><a href='?action=del-fav&id={$serieId}'>Supprimer des favoris</a></p>";

        switch ($selector) {
            case self::COMPACT:
                // titre + photo + Année
                $html = <<<HTML
                <div class="serie compact">
                    <a href='?action=display-serie&serie-id={$serieId}'><img src="images/{$this->getImage()}" alt="Miniature de {$this->getTitle()}"></a>
                    <h3>{$this->getTitle()}</h3>
                    <p>Année : {$this->getYear()}</p>
                    $fav
                    <p><a href="?action=add-comment&serie_id={$serieId}">Noter/Commenter</a></p>
                </div>
                HTML;
                break;
            case self::LONG:
                $html = <<<HTML
                            <div class="serie long">
                                <h2>{$this->getTitle()} - {$this->getYear()}</h2>
                                <p>{$this->getDescription()}<p/>
                                $fav
                                <p><a href="?action=add-comment&serie_id={$serieId}">Noter/Commenter cette série</a></p>
                                <hr>
                            HTML;
                $avgRating = $repo->getAverageRating($serieId);
                $comments = $repo->getComments($serieId);

                foreach ($this->episodes as $episode) {
                    $html .= $episode->render(self::COMPACT);
                }

                if ($avgRating !== null) {
                    $formattedRating = number_format($avgRating, 1);
                    $html .= "<h3>Note moyenne: {$formattedRating} / 5</h3>";
                } else {
                    $html .= "<h3>Note moyenne: (Pas encore de note)</h3>";
                }

                $html .= "<h4>Commentaires</h4>";
                if (empty($comments)) {
                    $html .= "<p>Cette série n'a pas encore de commentaires.</p>";
                } else {
                    $html .= '<ul>';
                    foreach ($comments as $comment) {
                        $userName = $comment['firstname'];
                        $userNote = (int)$comment['note'];
                        $userText = $comment['text']; // Pas de htmlspecialchars
                        $date = date('d/m/Y', strtotime($comment['date_added']));

                        $html .= <<<COMMENT
                        <li>
                            <p><strong>{$userName}</strong> (Note: {$userNote}/5) - <em>{$date}</em></p>
                            <p>{$userText}</p>
                        </li>   
                        COMMENT;
                    }
                    $html .= '</ul>';
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