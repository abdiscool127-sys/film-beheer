-- SQL schema voor directors database
CREATE DATABASE IF NOT EXISTS film_directors CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE film_directors;

CREATE TABLE IF NOT EXISTS directors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  naam VARCHAR(255) NOT NULL,
  geboortedatum DATE DEFAULT NULL,
  bio TEXT DEFAULT NULL
);

-- voorbeelddata
INSERT INTO directors (naam, geboortedatum, bio) VALUES
("Christopher Nolan", '1970-07-30', 'Regisseur van veel sciencefiction en drama.'),
("Greta Gerwig", '1983-08-04', 'Regisseert en schrijft hedendaagse films.');
