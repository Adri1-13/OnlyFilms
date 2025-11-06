<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\Repository;

use iutnc\onlyfilms\exception\OnlyFilmsRepositoryException;
use iutnc\onlyfilms\auth\User;
use iutnc\onlyfilms\video\lists\Serie;
use iutnc\onlyfilms\video\tracks\Episode;



use PDO;

class OnlyFilmsRepository {
    private \PDO $pdo;
    private static ?OnlyFilmsRepository $instance = null;
    private static array $config = [];


    private function __construct(array $conf) {
        $this->pdo = new \PDO(
            $conf['dsn'],
            $conf['user'],
            $conf['password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // exceptions sur erreurs
                \PDO::ATTR_EMULATE_PREPARES => false,           // utilise les vrais types du serveur
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );

        $this->pdo->prepare('SET NAMES \'UTF8\'')->execute(); // permet de gérer les accents dans les données
    }

    public static function getInstance() : OnlyFilmsRepository {
        if (self::$instance === null) {
            self::$instance = new OnlyFilmsRepository(self::$config);
        }
        return self::$instance;
    }
    /*
     * pourquoi cette fonction et l'attribut qu'elle modifie doit être en static ?
     * ça ne dépend pas de l'instance de NetVODRepository ?
     * ---> obligé parce que pour faire une instance on a besoin de la config, et si setConfig n'est pas static, alors il faut obligatoirement une instance
     * pour appeler la fonction, donc on doit mettre setConfig et config en static pour qu'ils ne dépendent pas d'une instance et ensuite pouvoir créer une instance de repository
     */
    public static function setConfig($file) : void {
        $fichierConfig = parse_ini_file($file);
        if ($fichierConfig === false) {
            throw new \Exception("Erreur dans le fichier de config"); // pourquoi c'est \Exception ? parce que c'est dans un namespace
        }
        $driver = $fichierConfig['driver'];
        $host = $fichierConfig['host'];
        $dbname = $fichierConfig['dbname'];
        $dsn = "$driver:host=$host;dbname=$dbname";
        self::$config = ['dsn' => $dsn, 'user' => $fichierConfig['user'], 'password' => $fichierConfig['password']];
    }


        /* =================== USER =================== */


    function findUser(string $mail) : User | null {
        $requete = "SELECT * FROM user WHERE mail = ?";

        $stmt = $this->pdo->prepare($requete);

        $stmt->execute([$mail]);

        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC); // normalement on a une unique ligne par user

        if ($ligne === false) {
            return null;
        }

        return new User($ligne['user_id'], $ligne['firstname'], $ligne['name'], $ligne['mail'], $ligne['password'], $ligne['role']);

    }

    function addUser(string $mail, string $passwd, string $name, string $firstname, int $role) : User {
        $requete = "INSERT INTO user(mail, password, name, firstname, role) VALUES (?,?,?,?,?)";

        $stmt = $this->pdo->prepare($requete);
        $stmt->execute([$mail, $passwd, $name, $firstname, $role]);

        $nouvID = $this->pdo->lastInsertId();

        return new User((int)$nouvID, $firstname, $name, $mail, $passwd, $role);
    }
    
    /* =================== SERIES =================== */
    public function findAllSeries(): array {
        $stmt = $this->pdo->query("SELECT * FROM series ORDER BY date_added DESC");
        $rows = $stmt->fetchAll();

        $series = [];
        foreach ($rows as $r) {
            $series[] = new Serie(
                (int)$r['series_id'],
                $r['title'],
                $r['description'] ?? '',
                $r['img'] ?? '',
                (int)$r['year'],
                $r['date_added']
            );
        }
        return $series;
    }



    public function findSeriesBySerieId(int $id): ?Serie { // TODO : peut etre renomme meme si c faux en anglais (series -> serie) pour que le nom soit plus juste car on retourne qu'une serie
        $stmt = $this->pdo->prepare("SELECT * FROM series WHERE series_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row === false) return null;

        return new Serie(
            (int)$row['series_id'],
            $row['title'],
            $row['description'] ?? '',
            $row['img'] ?? '',
            (int)$row['year'],
            $row['date_added']
        );
    }

    public function findFavoriteSeriesByUserID(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT s.* FROM series s
            JOIN Like_list l ON s.series_id = l.series_id
            WHERE l.user_id = ? ORDER BY s.date_added DESC");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();

        $series = [];
        foreach ($rows as $r) {
            $series[] = new Serie(
                (int)$ligne['series_id'],
                $ligne['title'],
                $ligne['description'],
                $ligne['img'],
                (int)$ligne['year'],
                $ligne['date_added']
            );
        }
        return $series;
    }


        /* =================== EPISODES =================== */

    /**
     * @throws \Exception
     */
    public function findEpisodesBySeriesId(int $seriesId): array {
    $stmt = $this->pdo->prepare("SELECT * FROM episode WHERE series_id = ? ORDER BY num ASC");
    $stmt->execute([$seriesId]);
    $rows = $stmt->fetchAll();

        $episodes = [];
        foreach ($lignes as $ligne) {
            $episodes[] = new Episode(
                (int)$ligne['episode_id'],
                (int)$ligne['num'],
                $ligne['title'],
                $ligne['summary'] ?? '',
                (int)$ligne['duration'],
                $ligne['file'] ?? '',
                $ligne['img'],
                (int)$ligne['series_id']
            );
        }
        if (!empty($episodes)) {
            throw new \Exception("Aucun épisode associé a cette série");
        }
        return $episodes;
    }

    public function addComment(int $userId, int $serieId, string $comment, int $note): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO commentary (user_id, series_id, text, date_added)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $serieId, $comment]);

        $stmt = $this->pdo->prepare("
            INSERT INTO notation (user_id, series_id, note, date_added)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $serieId, $note]);
    }

    /**
     * @throws OnlyFilmsRepositoryException
     */
    public function findEpisodeById(int $id) : Episode
    {

        if ($id < 0) {
            throw new OnlyFilmsRepositoryException("Id doit etre positif");
        }
        $stmt = $this->pdo->prepare("SELECT * FROM episode WHERE episode_id = ?");
        $stmt->execute([$id]);
        $rows = $stmt->fetchAll();

        if (empty($rows)) {
            throw new OnlyFilmsRepositoryException("Aucun épisode trouvé");
        }

        $row = $rows[0];
        return new Episode(
            $row['episode_id'], // $id
            $row['num'],        // $number
            $row['title'],      // $title
            $row['summary'],    // $summary
            $row['duration'],   // $duration
            $row['file'],       // $file
            $row['img'],        // $img
            $row['series_id']   // $seriesId
        );
    }

    public function addFav(int $userId, int $serieId): void {
        $stmt = $this->pdo->prepare("INSERT INTO like_list (user_id, series_id) VALUES (?, ?)");
        $stmt->execute([$userId, $serieId]);
    }

    public function isInFavList(int $userId, int $serieId): bool {
        $stmt = $this->pdo->prepare("SELECT * FROM like_list WHERE user_id = ? AND series_id = ?");
        $stmt->execute([$userId, $serieId]);
        $rows = $stmt->fetchAll();

        if (!empty($rows)) {
            return true;
        }
        return false;
    }
    public function getUserInSerieProgress(int $userId): array
    {
        // 1) Par série : combien vus + dernière date de visionnage
        $sql = "
        SELECT e.series_id,
               COUNT(*) AS watched_count,
               MAX(we.viewing_date) AS last_viewing
        FROM watch_episode we
        JOIN episode e ON e.episode_id = we.episode_id
        WHERE we.user_id = ?
        GROUP BY e.series_id
        ORDER BY last_viewing DESC
    ";
        $st = $this->pdo->prepare($sql);
        $st->execute([$userId]);
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];

        foreach ($rows as $r) {
            $seriesId = (int) $r['series_id'];
            $watchedCount = (int) $r['watched_count'];

            // 2) Total d'épisodes de la série
            $stTot = $this->pdo->prepare("SELECT COUNT(*) FROM episode WHERE series_id = ?");
            $stTot->execute([$seriesId]);
            $total = (int) $stTot->fetchColumn();

            // Si tout est vu, ce n'est plus "en cours"
            if ($total <= 0 || $watchedCount >= $total) {
                continue;
            }

            // 3) Dernier épisode vu (id) pour cette série
            $stLastEp = $this->pdo->prepare("
            SELECT we.episode_id
            FROM watch_episode we
            JOIN episode e ON e.episode_id = we.episode_id
            WHERE we.user_id = ? AND e.series_id = ?
            ORDER BY we.viewing_date DESC
            LIMIT 1
        ");
            $stLastEp->execute([$userId, $seriesId]);
            $lastEpisodeId = (int) $stLastEp->fetchColumn();

            // 4) Objets avec méthodes existantes
            $serieObj = $this->findSeriesBySerieId($seriesId);
            if ($serieObj === null)
                continue;

            $episodeObj = $this->findEpisodeById($lastEpisodeId);

            $pct = (int) round(($watchedCount / max(1, $total)) * 100);

            $result[] = [
                'series' => $serieObj,     // objet Serie
                'last_episode' => $episodeObj,   // objet Episode
                'progress_pct' => $pct,          // int
                'last_viewing' => $r['last_viewing'],
            ];
        }

        return $result;
    }
    public function getUserFavouriteSeries(int $userId): array
    {
        $sql = "
        SELECT s.*
        FROM like_list l
        INNER JOIN series s ON s.series_id = l.series_id
        WHERE l.user_id = ?
        ORDER BY s.date_added DESC
    ";
        $st = $this->pdo->prepare($sql);
        $st->execute([$userId]);
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);

        $series = [];
        foreach ($rows as $r) {
            $series[] = new Serie(
                (int) $r['series_id'],
                (string) $r['title'],
                (string) ($r['description'] ?? ''),
                (string) ($r['img'] ?? ''),
                (int) $r['year'],
                (string) $r['date_added']
            );
        }
        return $series;
    }


}