<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\Repository;

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


    public function findUser(string $email) : User | null {
        $requete = "SELECT id, email, passwd, role FROM user WHERE email = ?";

        $stmt = $this->pdo->prepare($requete);

        $stmt->execute([$email]);

        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC); // normalement on a une unique ligne par user

        if ($ligne === false) {
            return null;
        }

        return new User($ligne['id'], $ligne['email'], $ligne['passwd'], $ligne['role']);

    }

    public function addUser(string $email, string $passwd, int $role) : User {
        $requete = "INSERT INTO user(email, passwd, role) VALUES (?,?,?)";

        $stmt = $this->pdo->prepare($requete);
        $stmt->execute([$email, $passwd, $role]);

        $nouvID = $this->pdo->lastInsertId();

        return new User((int)$nouvID, $email, $passwd, $role);
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



    public function findSeriesBySerieId(int $id): ?Serie {
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

    public function findSeriesByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT s.* FROM series s
            JOIN Like_list l ON s.series_id = l.series_id
            WHERE l.user_id = ? ORDER BY s.date_added DESC");
        $stmt->execute([$userId]);
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


        /* =================== EPISODES =================== */

    /**
     * @throws \Exception
     */
    public function findEpisodesBySeriesId(int $seriesId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM episode WHERE series_id = ? ORDER BY num ASC");
        $stmt->execute([$seriesId]);
        $rows = $stmt->fetchAll();

        $episodes = [];
        foreach ($rows as $r) {
            $episodes[] = new Episode(
                (int)$r['episode_id'],
                (int)$r['num'],
                $r['title'],
                $r['summary'] ?? '',
                (int)$r['duration'],
                $r['file'] ?? '',
                $r['img'],
                (int)$r['series_id']
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

}