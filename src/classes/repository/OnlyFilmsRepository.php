<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\Repository;

use iutnc\onlyfilms\exception\OnlyFilmsRepositoryException;
use iutnc\onlyfilms\auth\User;
use iutnc\onlyfilms\video\lists\Serie;
use iutnc\onlyfilms\video\tracks\Episode;



use PDO;

class OnlyFilmsRepository
{
    private \PDO $pdo;
    private static ?OnlyFilmsRepository $instance = null;
    private static array $config = [];


    private function __construct(array $conf)
    {
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

    public static function getInstance(): OnlyFilmsRepository
    {
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
    public static function setConfig($file): void
    {
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


    /**
     * Trouve un utilisateur en fonction de son mail
     * @throws OnlyFilmsRepositoryException
     */
    function findUser(string $mail): User|false
    {
        $requete = "SELECT * FROM user WHERE mail = ?";

        $stmt = $this->pdo->prepare($requete);

        $stmt->execute([$mail]);

        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC); // normalement on a une unique ligne par user

        if ($ligne === false) {
            throw new OnlyFilmsRepositoryException("Aucun utilisateur trouvé");
        }

        return new User($ligne['user_id'], $ligne['firstname'], $ligne['name'], $ligne['mail'], $ligne['password'], $ligne['role']);

    }

    /**
     * Vérifie si un utilisateur existe
     * @param string $mail
     * @return bool
     */
    function userExists(string $mail): bool
    {
        $requete = "SELECT 1 FROM user WHERE mail = ?";

        $stmt = $this->pdo->prepare($requete);

        $stmt->execute([$mail]);

        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC); // normalement on a une unique ligne par user

        return ($ligne === false) ? false : true;

    }

    /**
     * Ajoute un utilisateur
     * @param string $mail
     * @param string $passwd
     * @param string $name
     * @param string $firstname
     * @param int $role
     * @return User
     */
    function addUser(string $mail, string $passwd, string $name, string $firstname, int $role): User
    {
        $requete = "INSERT INTO user(mail, password, name, firstname, role) VALUES (?,?,?,?,?)";

        $stmt = $this->pdo->prepare($requete);
        $stmt->execute([$mail, $passwd, $name, $firstname, $role]);

        $nouvID = $this->pdo->lastInsertId();

        return new User((int) $nouvID, $firstname, $name, $mail, $passwd, $role);
    }

    /* =================== SERIES =================== */
    /**
     * Récupère toutes les séries
     * @return array
     */
    public function findAllSeries(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM series ORDER BY date_added DESC");
        $rows = $stmt->fetchAll();

        $series = [];
        foreach ($rows as $r) {
            $series[] = new Serie(
                (int) $r['series_id'],
                $r['title'],
                $r['description'] ?? '',
                $r['img'] ?? '',
                (int) $r['year'],
                $r['date_added'],
                null
            );
        }
        return $series;
    }


    /**
     * Récupère une série par son ID avec tous ses épisodes
     * @throws OnlyFilmsRepositoryException
     */
    public function findSerieBySerieId(int $id): Serie
    {

        if ($id < 0) {
            throw new OnlyFilmsRepositoryException('Id doit être positif');
        }
        $stmt = $this->pdo->prepare("SELECT * FROM series WHERE series_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (empty($row)) {
            throw new OnlyFilmsRepositoryException('Serie introuvable');
        }

        try {
            $episodes = $this->findEpisodesBySeriesId($id);
        } catch (\Exception $e) {
            throw new OnlyFilmsRepositoryException('Echec récupération liste épisodes');
        }

        return new Serie(
            (int) $row['series_id'],
            $row['title'],
            $row['description'] ?? '',
            $row['img'] ?? '',
            (int) $row['year'],
            $row['date_added'],
            $episodes,
        );
    }


    /* =================== EPISODES =================== */

    public function getTotalEpisodesInSerie(int $seriesId): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM episode WHERE series_id = ?");
        $stmt->execute([$seriesId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Récupère tous les épisodes d'une série
     * @throws \Exception
     */
    public function findEpisodesBySeriesId(int $seriesId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM episode WHERE series_id = ? ORDER BY num ASC");
        $stmt->execute([$seriesId]);
        $rows = $stmt->fetchAll();

        $episodes = [];
        foreach ($rows as $r) {
            $episodes[] = new Episode(
                (int) $r['episode_id'],
                (int) $r['num'],
                $r['title'],
                $r['summary'] ?? '',
                (int) $r['duration'],
                $r['file'] ?? '',
                $r['img'],
                (int) $r['series_id']
            );
        }
        if (empty($episodes)) {
            throw new \Exception("Aucun épisode associé a cette série");
        }
        return $episodes;
    }

    public function findEpisodeIdByNumAndSeriesId(int $episodeNum, int $seriesId): int
    {
        $stmt = $this->pdo->prepare("SELECT episode_id FROM episode WHERE num = ? AND series_id = ?");
        $stmt->execute([$episodeNum, $seriesId]);

        $episodeId = $stmt->fetchColumn();

        return (int) $episodeId;
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
     * Récupère un épisode par son ID
     * @throws OnlyFilmsRepositoryException
     */
    public function findEpisodeById(int $id): Episode
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

    public function delFav(int $userId, int $serieId): void {
        $stmt = $this->pdo->prepare("DELETE FROM like_list WHERE user_id = ? and series_id = ?");
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

    /**
     * récupère les séries en cours de l'utilisateur
     * @param int $userId
     * @return array
     * @throws OnlyFilmsRepositoryException
     */
    public function getUserInSerieProgress(int $userId): array
    {
        // 1) Par série : combien vus + dernière date de visionnage
        // Recupere l'ID de la serie , le nombre d'ep qu'il a vu & la dernier date
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

            // 2) Total d'épisodes de la série que l'user a vu
            $stTot = $this->pdo->prepare("SELECT COUNT(*) FROM episode WHERE series_id = ?");
            $stTot->execute([$seriesId]);
            $total = (int) $stTot->fetchColumn();

            // 3) Dernier épisode vu (id) pour cette série
            $stLastEp = $this->pdo->prepare("
            SELECT we.episode_id
            FROM watch_episode we
            JOIN episode e ON e.episode_id = we.episode_id
            WHERE we.user_id = ? AND e.series_id = ?
            ORDER BY we.viewing_date ASC
            LIMIT 1
            ");
            $stLastEp->execute([$userId, $seriesId]);
            $lastEpisodeId = (int) $stLastEp->fetchColumn();

            // 4) Objets avec méthodes existantes
            $serieObj = $this->findSerieBySerieId($seriesId);
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

    /**
     * Récupère les séries favorites de l'utilisateur
     * @param int $userId
     * @return array
     */
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
                (string) $r['date_added'],
                null
            );
        }
        return $series;
    }

    /**
     * Marque un épisode comme visionné
     * @param int $userId
     * @param int $episodeId
     * @return void
     */
    public function addWatchedEpisode(int $userId, int $episodeId): void
    {
        //tester l'existence de la ligne
        $sqlCheck = "
        SELECT 1 
        FROM watch_episode 
        WHERE user_id = ? AND episode_id = ?
        ";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([$userId, $episodeId]);

        $exists = $stmtCheck->fetchColumn();

        //Exécuter l'action appropriée
        if ($exists) {
            //La ligne existe : update la date de visionnage
            $sqlAction = "
            UPDATE watch_episode 
            SET viewing_date = NOW() 
            WHERE user_id = ? AND episode_id = ?
            ";
        } else {
            //La ligne n'existe pas : insert la nouvelle ligne
            $sqlAction = "
            INSERT INTO watch_episode (user_id, episode_id, viewing_date)
            VALUES (?, ?, NOW())
            ";
        }

        //exécuter la requête d'action
        $stmtAction = $this->pdo->prepare($sqlAction);
        $stmtAction->execute([$userId, $episodeId]);
    }
    public function cleanupSeriesIfCompleted(int $userId, int $seriesId): void
    {
        // total d'épisodes de la série
        $st = $this->pdo->prepare("SELECT COUNT(*) FROM episode WHERE series_id = ?");
        $st->execute([$seriesId]);
        $total = (int) $st->fetchColumn();

        if ($total <= 0)
            return;

        // combien vus par cet utilisateur
        $st = $this->pdo->prepare("
        SELECT COUNT(*) 
        FROM watch_episode we
        JOIN episode e ON e.episode_id = we.episode_id
        WHERE we.user_id = ? AND e.series_id = ?
    ");
        $st->execute([$userId, $seriesId]);
        $watched = (int) $st->fetchColumn();

        // si tout vu : purge les lignes de cette série pour cet utilisateur
        if ($watched >= $total) {
            $del = $this->pdo->prepare("
            DELETE we FROM watch_episode we
            JOIN episode e ON e.episode_id = we.episode_id
            WHERE we.user_id = ? AND e.series_id = ?
        ");
            $del->execute([$userId, $seriesId]);
        }
    }




}