-- Initialisation
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table `user`
-- --------------------------------------------------------
CREATE TABLE `user` (
    `user_id` INT(11) NOT NULL AUTO_INCREMENT,
    `mail` VARCHAR(100) NOT NULL,
    `password` VARCHAR(100) NOT NULL,
    `name` VARCHAR(100) DEFAULT NULL,
    `firstname` VARCHAR(100) DEFAULT NULL,
    `role` INT(11) NOT NULL DEFAULT 1,
    `activated` BOOLEAN NOT NULL DEFAULT FALSE,
    `activation_token` VARCHAR(64) DEFAULT NULL,
    `token_generation_date` DATETIME DEFAULT NULL,
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table `genre`
-- --------------------------------------------------------
CREATE TABLE `genre` (
    `genre_id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table `series`
-- --------------------------------------------------------
CREATE TABLE `series` (
    `series_id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `img` VARCHAR(100) DEFAULT NULL,
    `year` INT(4) DEFAULT NULL,
    `date_added` DATE NOT NULL,
    PRIMARY KEY (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table d'association `genre_series`
-- --------------------------------------------------------
CREATE TABLE `genre_series` (
    `genre_id` INT(11) NOT NULL,
    `series_id` INT(11) NOT NULL,
    PRIMARY KEY (`genre_id`, `series_id`),
    FOREIGN KEY (`genre_id`) REFERENCES `genre` (`genre_id`),
    FOREIGN KEY (`series_id`) REFERENCES `series` (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table `episode`
-- --------------------------------------------------------
CREATE TABLE `episode` (
    `episode_id` INT(11) NOT NULL AUTO_INCREMENT,
    `num` INT(11) NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `summary` TEXT,
    `duration` INT(11) DEFAULT 0,
    `file` VARCHAR(100) DEFAULT NULL,
    `img` VARCHAR(100) DEFAULT NULL,
    `series_id` INT (11) NOT NULL,
    PRIMARY KEY (`episode_id`),
    FOREIGN KEY (`series_id`) REFERENCES `series` (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table d'association `commentary` (Commentaires utilisateur/série)
-- --------------------------------------------------------
CREATE TABLE `commentary` (
    `user_id` INT(11) NOT NULL,
    `series_id` INT(11) NOT NULL,
    `text` TEXT,
    `date_added` DATETIME NOT NULL,
    PRIMARY KEY (`user_id`, `series_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
    FOREIGN KEY (`series_id`) REFERENCES `series` (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table d'association `notation` (Notes utilisateur/série)
-- --------------------------------------------------------
CREATE TABLE `notation` (
    `user_id` INT(11) NOT NULL,
    `series_id` INT(11) NOT NULL,
    `note` INT(1) NOT NULL, -- Utilisation de DECIMAL pour une meilleure précision des notes
    `date_added` DATETIME NOT NULL,
    PRIMARY KEY (`user_id`, `series_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
    FOREIGN KEY (`series_id`) REFERENCES `series` (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table d'association `like_list` (Liste de favoris utilisateur/série)
-- --------------------------------------------------------
CREATE TABLE `like_list` (
    `user_id` INT(11) NOT NULL,
    `series_id` INT(11) NOT NULL,
    PRIMARY KEY (`user_id`, `series_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
    FOREIGN KEY (`series_id`) REFERENCES `series` (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table d'association `watch_episode`
-- --------------------------------------------------------
CREATE TABLE `watch_episode` (
    `user_id` INT(11) NOT NULL,
    `episode_id` INT(11) NOT NULL,
    `viewing_date` DATETIME NOT NULL,
    PRIMARY KEY (`user_id`, `episode_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
    FOREIGN KEY (`episode_id`) REFERENCES `episode` (`episode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table d'association `watched_serie`
-- --------------------------------------------------------
CREATE TABLE `watched_series` (
    `user_id` INT(11) NOT NULL,
    `series_id` INT(11) NOT NULL,
    `viewing_date` DATETIME NOT NULL,
    PRIMARY KEY (`user_id`, `series_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
    FOREIGN KEY (`series_id`) REFERENCES `series` (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Insertions dans la table `user`
-- --------------------------------------------------------
INSERT INTO `user` (`user_id`, `mail`, `password`, `name`, `firstname`, `role`) VALUES
(1, 'alice.dupont@example.com', 'hashed_password_1', 'Dupont', 'Alice', 1),
(2, 'bob.martin@example.com', 'hashed_password_2', 'Martin', 'Bob', 1),
(3, 'admin@platform.com', 'hashed_admin_password', 'Super', 'Admin', 99);

-- --------------------------------------------------------
-- Insertions dans la table `genre`
-- --------------------------------------------------------
INSERT INTO `genre` (`genre_id`, `name`) VALUES
(1, 'Science-Fiction'),
(2, 'Drame'),
(3, 'Comédie'),
(4, 'Thriller');

-- --------------------------------------------------------
-- Insertions dans la table `series`
-- --------------------------------------------------------
INSERT INTO `series` (`series_id`, `title`, `description`, `img`, `year`, `date_added`) VALUES
(1, 'Au-delà du Ciel', 'Un groupe d\'explorateurs spatiaux découvre une anomalie qui remet en question toute leur existence.', 'sky_img.jpg', 2023, '2023-09-01'),
(2, 'Le Secret de la Ville', 'Une intrigue politique sombre se déroule dans une capitale moderne.', 'city_secret.jpg', 2022, '2022-11-15'),
(3, 'Les Aventures de Max', 'Les mésaventures hilarantes d\'un jeune homme essayant de réussir à Hollywood.', 'max_adventures.jpg', 2024, '2024-01-20');

-- --------------------------------------------------------
-- Insertions dans la table `genre_series`
-- (Association entre genres et séries)
-- --------------------------------------------------------
INSERT INTO `genre_series` (`genre_id`, `series_id`) VALUES
(1, 1), -- Science-Fiction -> Au-delà du Ciel
(2, 1), -- Drame -> Au-delà du Ciel
(2, 2), -- Drame -> Le Secret de la Ville
(4, 2), -- Thriller -> Le Secret de la Ville
(3, 3); -- Comédie -> Les Aventures de Max

-- --------------------------------------------------------
-- Insertions dans la table `episode`
-- --------------------------------------------------------
INSERT INTO `episode` (`episode_id`, `num`, `title`, `summary`, `duration`, `file`, `img`, `series_id`) VALUES
-- Série 1 : Au-delà du Ciel
(1, 1, 'Le Premier Saut', 'L\'équipage quitte la Terre et rencontre des difficultés inattendues.', 3000, 'ep1_s1.mp4', 'ep1_s1_img.jpg', 1),
(2, 2, 'Écho Lointain', 'Un signal étrange mène l\'équipage vers un système inconnu.', 3200, 'ep2_s1.mp4', 'ep2_s1_img.jpg', 1),
(3, 3, 'La Découverte', 'L\'anomalie est localisée, mais son secret pourrait être mortel.', 3400, 'ep3_s1.mp4', 'ep3_s1_img.jpg', 1),
-- Série 2 : Le Secret de la Ville
(4, 1, 'L\'Ombre du Maire', 'Une journaliste enquête sur des transactions suspectes.', 2800, 'ep1_s2.mp4', 'ep1_s2_img.jpg', 2),
(5, 2, 'Filatures', 'La journaliste se retrouve en danger après avoir déterré une information cruciale.', 2900, 'ep2_s2.mp4', 'ep2_s2_img.jpg', 2),
(6, 3, 'Révélations', 'Le maire est acculé, mais il a une dernière carte à jouer.', 3100, 'ep3_s2.mp4', 'ep3_s2_img.jpg', 2),
-- Série 3 : Les Aventures de Max
(7, 1, 'Le Casting Catastrophe', 'Max rate lamentablement une audition pour un petit rôle.', 1500, 'ep1_s3.mp4', 'ep1_s3_img.jpg', 3),
(8, 2, 'Livreur de Rêves', 'Pour joindre les deux bouts, Max devient livreur de repas.', 1450, 'ep2_s3.mp4', 'ep2_s3_img.jpg', 3),
(9, 3, 'Rencontre inattendue', 'Max croise une star de cinéma qui lui donne un conseil précieux.', 1600, 'ep3_s3.mp4', 'ep3_s3_img.jpg', 3);

-- --------------------------------------------------------
-- Insertions dans la table `commentary`
-- (Commentaires utilisateur/série)
-- --------------------------------------------------------
INSERT INTO `commentary` (`user_id`, `series_id`, `text`, `date_added`) VALUES
(1, 1, 'Une série de Science-Fiction très prenante, jattends la suite avec impatience!', '2023-09-05 14:30:00'),
(2, 2, 'Un thriller bien ficelé avec des acteurs incroyables.', '2023-10-01 20:15:00'),
(1, 3, 'Très drôle, parfait pour se détendre!', '2024-02-10 18:00:00');

-- --------------------------------------------------------
-- Insertions dans la table `notation`
-- (Notes utilisateur/série)
-- NOTE: La colonne `note` est de type INT(1) dans le schéma original, supposons qu'elle soit une note sur 5 ou 10. J'utilise une note sur 5 ici (1 à 5).
-- --------------------------------------------------------
INSERT INTO `notation` (`user_id`, `series_id`, `note`, `date_added`) VALUES
(1, 1, 5, '2023-09-05 14:31:00'),
(2, 1, 4, '2023-10-10 09:00:00'),
(2, 2, 5, '2023-10-01 20:16:00'),
(1, 3, 4, '2024-02-10 18:01:00');

-- --------------------------------------------------------
-- Insertions dans la table `like_list`
-- (Liste de favoris utilisateur/série)
-- --------------------------------------------------------
INSERT INTO `like_list` (`user_id`, `series_id`) VALUES
(1, 1), -- Alice aime Au-delà du Ciel
(1, 3), -- Alice aime Les Aventures de Max
(2, 2); -- Bob aime Le Secret de la Ville

-- --------------------------------------------------------
-- Insertions dans la table `watch_episode`
-- (Suivi de visionnage utilisateur/épisode - Épisodes TERMINÉS)
-- --------------------------------------------------------
INSERT INTO `watch_episode` (`user_id`, `episode_id`, `viewing_date`) VALUES
(1, 1, '2023-09-02 21:00:00'), -- Alice a vu S1E1
(1, 2, '2023-09-03 21:30:00'), -- Alice a vu S1E2
(2, 4, '2023-10-01 19:30:00'), -- Bob a vu S2E1
(2, 5, '2023-10-02 19:45:00'), -- Bob a vu S2E2
(1, 7, '2024-02-10 17:30:00'); -- Alice a vu S3E1


DELIMITER $$
-- Insere une série dans watched_series si toutes les épisodes de cette meme séries sont regardées
CREATE TRIGGER trg_watch_episode_after_insert
AFTER INSERT ON watch_episode
FOR EACH ROW
BEGIN
    DECLARE v_series_id INT;
    DECLARE v_total INT;
    DECLARE v_watched INT;

      -- Trouver la série de l'épisode inséré
      SELECT e.series_id INTO v_series_id
      FROM episode e
      WHERE e.episode_id = NEW.episode_id;

      -- Nb total d'épisodes de la série
      SELECT COUNT(*) INTO v_total
      FROM episode
      WHERE series_id = v_series_id;

      -- Nb d'épisodes vus par cet utilisateur dans cette série
      SELECT COUNT(*) INTO v_watched
      FROM watch_episode we
      INNER JOIN episode e2 ON e2.episode_id = we.episode_id
      WHERE we.user_id = NEW.user_id AND e2.series_id = v_series_id;

      -- Si tout est vu, placer dans watched_series
      IF v_total > 0 AND v_watched = v_total THEN
            INSERT INTO watched_series(user_id, series_id, viewing_date)
            VALUES (NEW.user_id, v_series_id, NOW())
            ON DUPLICATE KEY UPDATE viewing_date = NOW();
      END IF;
END$$

DELIMITER ;

CREATE TABLE IF NOT EXISTS password_reset_token (
  token CHAR(64) NOT NULL,            -- bin2hex(random_bytes(32))
  user_id INT(11) NOT NULL,
  issued_at DATETIME NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (token),
  CONSTRAINT fk_prt_user FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS password_reset_token (
  token CHAR(64) NOT NULL,            -- bin2hex(random_bytes(32))
  user_id INT(11) NOT NULL,
  issued_at DATETIME NOT NULL,
  expires_at DATETIME NOT NULL,
  used INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (token),
  CONSTRAINT fk_prt_user FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


UPDATE user SET activated = 1 WHERE activated = 0;