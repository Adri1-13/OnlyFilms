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
        $serieId = $this->getId();
        $repo = OnlyFilmsRepository::getInstance();

        // status favoris
        $isFav = $repo->isInFavList($_SESSION['user']->getId(), $this->id);

        $favLong = $isFav
            ? "<p><a href='?action=del-fav&id={$serieId}' class='btn btn-danger'>♥ Retirer des favoris</a></p>"
            : "<p><a href='?action=add-fav&id={$serieId}' class='btn btn-outline-danger'>♡ Ajouter aux favoris</a></p>";

        switch ($selector) {
            case self::COMPACT:

                $imgSrc = $this->getImage();
                $title = htmlspecialchars($this->getTitle(), ENT_QUOTES, 'UTF-8');
                $imgTag = '';

                if ($imgSrc !== '') {
                    $imgTag = '<img src="images/' . htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') . '" alt="Affiche ' . $title . '" class="card-img-top" style="aspect-ratio:2/3; object-fit:cover;">';
                } else {
                    $imgTag = '<div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted" style="aspect-ratio:2/3;">Aucune image</div>';
                }


                $favLink = $isFav
                    ? "<a href='?action=del-fav&id={$serieId}' class='btn btn-danger btn-sm' title='Retirer des favoris'>♥</a>"
                    : "<a href='?action=add-fav&id={$serieId}' class='btn btn-outline-danger btn-sm' title='Ajouter aux favoris'>♡</a>";


                $commentLink = "<a href='?action=add-comment&serie_id={$serieId}' class='btn btn-outline-secondary btn-sm'>Noter</a>";

                $html = <<<HTML
            <article class="card h-100 shadow-sm">
                $imgTag
                <div class="card-body d-flex flex-column p-2">
                    <h3 class="h6 card-title mb-1">
                        <a href='?action=display-serie&serie-id={$serieId}' class="text-decoration-none text-dark stretched-link">$title</a>
                    </h3>
                    <small class="card-text text-body-secondary mb-2">{$this->getYear()}</small>
                    
                    <div class="mt-auto pt-2 d-flex justify-content-between gap-2" style="position: relative; z-index: 2;">
                       $favLink
                       $commentLink
                    </div>
                </div>
            </article>
            HTML;
                break;

            case self::LONG:

                $imgLong = htmlspecialchars($this->getImage(), ENT_QUOTES, 'UTF-8');
                $titleLong = htmlspecialchars($this->getTitle(), ENT_QUOTES, 'UTF-8');
                $descLong = htmlspecialchars($this->getDescription(), ENT_QUOTES, 'UTF-8');

                $html = <<<HTML
            <div class="row my-4">
                <div class="col-md-3">
                    <img src="images/{$imgLong}" alt="Affiche {$titleLong}" class="img-fluid rounded shadow-sm" style="aspect-ratio:2/3; object-fit:cover; width:100%;">
                </div>
                <div class="col-md-9">
                    <h2 class="display-5">{$titleLong} <span class="text-muted fs-3">({$this->getYear()})</span></h2>
                    <p class="lead">{$descLong}</p>
                    {$favLong}
                    <p><a href="?action=add-comment&serie_id={$serieId}" class="btn btn-outline-primary">Noter ou commenter cette série</a></p>
                </div>
            </div>
            <hr>
            HTML;

                $avgRating = $repo->getAverageRating($serieId);
                $comments = $repo->getComments($serieId);

                $html .= '<h3>Épisodes</h3>';
                // episodes dans un list-group bootstrap
                $html .= '<div class="list-group mb-4">';
                if (empty($this->episodes)) {
                    $html .= '<div class="list-group-item">Aucun épisode disponible pour cette série.</div>';
                }
                foreach ($this->episodes as $episode) {
                    $html .= $episode->render(Renderer::COMPACT); // appel renderer compact episode
                }
                $html .= '</div>'; // Fin list-group

                // notes et commentaires
                $html .= '<div class="row">';
                $html .= '<div class="col-md-8">';
                $html .= "<h4>Commentaires</h4>";
                if (empty($comments)) {
                    $html .= "<p>Cette série n'a pas encore de commentaires.</p>";
                } else {
                    $html .= '<ul class="list-unstyled">';
                    foreach ($comments as $comment) {
                        $userName = htmlspecialchars($comment['firstname']);
                        $userNote = (int)$comment['note'];
                        $userText = nl2br(htmlspecialchars($comment['text'])); // nl2br pour les sauts de ligne
                        $date = date('d/m/Y', strtotime($comment['date_added']));

                        $html .= <<<COMMENT
                    <li class="mb-3 border-bottom pb-2">
                        <p class="mb-0"><strong>{$userName}</strong> - <em class="text-muted">{$date}</em></p>
                        <p class="mb-1">Note: <span class="text-primary fw-bold">{$userNote}/5</span></p>
                        <p class="mb-0">{$userText}</p>
                    </li>   
                    COMMENT;
                    }
                    $html .= '</ul>';
                }
                $html .= '</div>';

                $html .= '<div class="col-md-4">';
                if ($avgRating !== null) {
                    $formattedRating = number_format($avgRating, 1);
                    $html .= "<div class='card bg-light'><div class='card-body text-center'>
                            <h5 class='card-title'>Note moyenne</h5>
                            <p class='display-4 fw-bold'>{$formattedRating} <span class='fs-4 text-muted'>/ 5</span></p>
                          </div></div>";
                } else {
                    $html .= "<div class='card bg-light'><div class='card-body text-center'>
                            <h5 class='card-title'>Note moyenne</h5>
                            <p class='fs-4 text-muted'>(Pas encore de note)</p>
                          </div></div>";
                }
                $html .= '</div>';
                $html .= '</div>';

                break;
            default:
                throw new \Exception("Paramètre renderer incorrect");
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