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
  `genre_id` INT(11) DEFAULT NULL,
  PRIMARY KEY (`series_id`),
  FOREIGN KEY (`genre_id`) REFERENCES `genre` (`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table `episode`
-- (Note: L'association Episode2Series dans le schéma UML suggère une relation N-N entre Episode et Series,
-- mais l'entité Episode a généralement une FK vers Series. J'ai respecté l'entité `episode` du schéma,
-- mais en pratique `Episode2Series` est redondante si `episode` a déjà `series_id`. Je crée `episode` sans FK vers `series` pour l'instant et j'utilise `Episode2Series` pour la liaison N-N stricte.)
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
-- Table d'association `watch_episode` (Suivi de visionnage utilisateur/épisode)
-- --------------------------------------------------------
CREATE TABLE `watch_episode` (
  `user_id` INT(11) NOT NULL,
  `episode_id` INT(11) NOT NULL,
  `viewing_date` DATETIME NOT NULL,
  PRIMARY KEY (`user_id`, `episode_id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  FOREIGN KEY (`episode_id`) REFERENCES `episode` (`episode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;