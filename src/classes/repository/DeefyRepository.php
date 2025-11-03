<?php

declare(strict_types=1);

namespace iutnc\deefy\Repository;

/*
 * Classe permettant l'unique connexion à la base de données,
 * dès qu'une classe veut accéder ou modifier la bdd elle passe par cette classe de connexion
 */

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\auth\User;

class DeefyRepository {
    private \PDO $pdo;
    private static ?DeefyRepository $instance = null;
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

    public static function getInstance() : DeefyRepository {
        if (self::$instance === null) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }
    /*
     * pourquoi cette fonction et l'attribut qu'elle modifie doit être en static ?
     * ça ne dépend pas de l'instance de DeefyRepository ?
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

    public function findAllPlaylists() : array {
        $requete = "SELECT id, nom FROM playlist";

        $stmt = $this->pdo->prepare($requete);

        $stmt->execute();

        $playlists = [];

        while ($ligne = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $playlist = new Playlist($ligne["nom"]);
            $playlist->setID((int)$ligne['id']);
            $playlists[] = $playlist;
        }

        return $playlists;

    }

    public function saveEmptyPlaylist(Playlist $playlist) : Playlist {
        $requete = "INSERT INTO playlist(nom) VALUES (?)";

        $stmt = $this->pdo->prepare($requete);
        $nomPlaylist = $playlist->nom;
        $stmt->bindParam(1, $nomPlaylist);

        $stmt->execute();

        $nouveauId = (int)$this->pdo->lastInsertId();

        $playlist->setID($nouveauId);

        return $playlist; // on return la playlist une fois que son id est actualisé
    }

    function sauvegarderPiste(AudioTrack $piste) : AudioTrack {
        if ($piste instanceof AlbumTrack) {
            $type = "A";
            $artiste_album = $piste->artiste;
            $titre_album = $piste->album;
            $annee_album = $piste->annee;
            $numero_album = $piste->numPiste; // numeroAlbum c'est le numéro de la piste ou encore autre chose ?
            $auteur_podcast = null;
            $date_podcast = null;
        } elseif ($piste instanceof PodcastTrack) {
            $type = "P";
            $artiste_album = null;
            $titre_album = null;
            $annee_album = null;
            $numero_album = null;
            $auteur_podcast = $piste->auteur;
            $date_podcast = $piste->date;
        } else {
            throw new \Exception("Erreur dans le type de la piste");
        }

        $requete = "INSERT INTO track(titre, genre, duree, filename, type, artiste_album, titre_album, annee_album, numero_album, auteur_podcast, date_posdcast)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($requete);

        $titre = $piste->titre;
        $genre = $piste->genre;
        $duree = $piste->duree;
        $nomFichierAudio = $piste->nomFichierAudio;

        $stmt->bindParam(1,$titre);
        $stmt->bindParam(2,$genre);
        $stmt->bindParam(3,$duree);
        $stmt->bindParam(4,$nomFichierAudio);
        $stmt->bindParam(5, $type);
        $stmt->bindParam(6, $artiste_album);
        $stmt->bindParam(7, $titre_album);
        $stmt->bindParam(8, $annee_album);
        $stmt->bindParam(9, $numero_album);
        $stmt->bindParam(10, $auteur_podcast);
        $stmt->bindParam(11, $date_podcast);

        $stmt->execute();

        $nouvelID = (int)$this->pdo->lastInsertId();
        $piste->setID($nouvelID);

        return $piste;
    }

    function addTrackToPlaylist(int $playlistID, int $pisteID) : void {
        // trouver le numéro de la nouvelle piste
        $requeteTrouverNumeroPiste = "SELECT COUNT(*) as plusGrandNumPiste FROM playlist2track WHERE id_pl = ?";

        $stmtCount = $this->pdo->prepare($requeteTrouverNumeroPiste);
        $stmtCount->bindParam(1, $playlistID);
        $stmtCount->execute();
        // fetchColumn() récupère la première colonne du résultat (le COUNT)
        $ligne = $stmtCount->fetch(\PDO::FETCH_ASSOC);
        $nouveauNumero = $ligne['plusGrandNumPiste'] + 1;

        $requeteInsert = "INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (?,?,?)";

        $stmtInsert = $this->pdo->prepare($requeteInsert);
        $stmtInsert->bindParam(1, $playlistID);
        $stmtInsert->bindParam(2, $pisteID);
        $stmtInsert->bindParam(3, $nouveauNumero);

        $stmtInsert->execute();


    }

    function trouverUser(string $email) : User | null {
        $requete = "SELECT id, email, passwd, role FROM user WHERE email = ?";

        $stmt = $this->pdo->prepare($requete);
        $stmt->bindParam(1, $email);

        $stmt->execute();

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

    /**
     * Renvoie une playlist (grâce à son id) avec toutes les musiques qui sont dedans
     */
    function trouverPlaylist_et_musiquesDedans(int $id) : Playlist | null {
        $requetePlaylist = "SELECT id, nom FROM playlist WHERE id = ?";

        $stmt = $this->pdo->prepare($requetePlaylist);
        $stmt->execute([$id]);

        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($ligne === false) {
            return null;
        }

        $playlist = new Playlist($ligne['nom']);

        $playlist->setID($ligne['id']);


        $requeteMusiques = "SELECT T.*
                            FROM track T
                            INNER JOIN playlist2track p2t ON T.id = p2t.id_track
                            WHERE p2t.id_pl = ?";

        $stmtMusiques = $this->pdo->prepare($requeteMusiques);
        $stmtMusiques->execute([$id]);

        while ($ligne = $stmtMusiques->fetch(\PDO::FETCH_ASSOC)) {
            if ($ligne['type'] === 'A') {
                $pisteAudio = new AlbumTrack($ligne['titre'], $ligne['filename'],$ligne['titre_album'], (int)$ligne['numero_album']);
                $pisteAudio->setAnnee((int)$ligne['annee_album']);
                $pisteAudio->setArtiste($ligne['artiste_album']);
            } else {
                $pisteAudio = new PodcastTrack($ligne['titre'], $ligne['filename']);
                $pisteAudio->setAuteur($ligne['auteur_podcast']);
                $pisteAudio->setDate($ligne['date_posdcast']); // peut-etre probleme comme dans la connexion on conserve le type des colonnes, php ne va pas accepter le type date au lieu du type string

            }
            $pisteAudio->setDuree($ligne['duree']);
            $pisteAudio->setID($ligne['id']);
            $pisteAudio->setGenre($ligne['genre']);

            $playlist->addPiste($pisteAudio);
        }


        return $playlist;
    }

    /**
    * Fonction qui retourner toutes les playlists possédées par un user
    */
    public function trouverToutesLesPlaylistsD_unUser(int $id) : array {
        $requete = "SELECT id_pl FROM user2playlist WHERE id_user = ?";

        $stmt = $this->pdo->prepare($requete);

        $stmt->execute([$id]);

        $tabRes = [];

        while ($ligne = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tabRes[] = $ligne['id_pl'];
        }

        return $tabRes;
    }

//    public function rechercheIDMaximumPlaylist() : int {
//        $requete = "SELECT MAX(id_pl) as MAX FROM user2playlist";
//
//        $stmt = $this->pdo->prepare($requete);
//
//        $stmt->execute();
//
//        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC);
//
//        return $ligne['MAX'];
//
//    }

    public function associerPlayList_A_UnUser(int $idPlaylist, int $idUser) : void {
        $requete = "INSERT INTO user2playlist (id_user, id_pl) VALUES (?,?)";
        $stmt = $this->pdo->prepare($requete);

        $stmt->execute([$idUser,$idPlaylist]);
    }




}