-- SQL schema voor actors database
CREATE DATABASE IF NOT EXISTS film_actors CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE film_actors;

CREATE TABLE IF NOT EXISTS actors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  naam VARCHAR(255) NOT NULL,
  geboortedatum DATE DEFAULT NULL,
  bio TEXT DEFAULT NULL
);

-- voorbeelddata
INSERT INTO actors (naam, geboortedatum, bio) VALUES
("Leonardo DiCaprio", '1974-11-11', 'Bekende Amerikaanse acteur.'),
("Meryl Streep", '1949-06-22', 'Veelgeprezen actrice.');
