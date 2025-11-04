<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\repository;
 
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
     * ça ne dépend pas de l'instance de OnlyFilmsRepository ?
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


    function trouverUser(string $email) : User | null {
        $requete = "SELECT id, email, passwd, role FROM user WHERE email = ?";

        $stmt = $this->pdo->prepare($requete);

        $stmt->execute([$email]);

        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC); // normalement on a une unique ligne par user

        if ($ligne === false) {
            return null;
        }

        return new User($ligne['id'], $ligne['email'], $ligne['passwd'], $ligne['role']);

    }

    function addUser(string $email, string $passwd, int $role) : User {
        $requete = "INSERT INTO user(email, passwd, role) VALUES (?,?,?)";

        $stmt = $this->pdo->prepare($requete);
        $stmt->execute([$email, $passwd, $role]);

        $nouvID = $this->pdo->lastInsertId();

        return new User((int)$nouvID, $email, $passwd, $role);
    }
    
    /* =================== SERIES =================== */
     public function findAllSeries(): array {
        $stmt = $this->pdo->query("SELECT * FROM series ORDER BY date_added DESC");
        return $stmt->fetchAll();
    }

    public function findSeriesById(int $id): array  {
        $stmt = $this->pdo->prepare("SELECT * FROM series WHERE series_id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }




}