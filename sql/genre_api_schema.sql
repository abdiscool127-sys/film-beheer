-- ================================================
-- Database voor Genre-API: film_app_genre
-- Doel: kleine aparte database met films + genre-naam, te gebruiken door een eenvoudige API-pagina
-- Opmerking: de INSERT ... SELECT werkt alleen als de MySQL-gebruiker toegang heeft tot beide databases.
-- ================================================

CREATE DATABASE IF NOT EXISTS film_app_genre CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE film_app_genre;

-- TABEL: films_by_genre (kopie van relevante velden uit hoofd-database)
CREATE TABLE IF NOT EXISTS films_by_genre (
  id INT PRIMARY KEY,
  titel VARCHAR(255) NOT NULL,
  jaar VARCHAR(10) DEFAULT NULL,
  genre_naam VARCHAR(100) DEFAULT NULL,
  beschrijving TEXT,
  poster VARCHAR(512) DEFAULT NULL,
  rating VARCHAR(10) DEFAULT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- OPTIONEEL: vul tabel met data uit de bestaande `film_app` database (als beschikbaar)
-- Dit vereist dat dezelfde MySQL-gebruiker leesrechten heeft op `film_app`.
-- Uncomment en voer uit als je wilt kopiëren:
--
-- INSERT INTO film_app_genre.films_by_genre (id, titel, jaar, genre_naam, beschrijving, poster, rating)
-- SELECT f.id, f.titel, f.jaar, g.genre_naam, f.beschrijving, f.poster, f.rating
-- FROM film_app.films f
-- LEFT JOIN film_app.genres g ON f.genre_id = g.id
-- ON DUPLICATE KEY UPDATE
-- titel = VALUES(titel), jaar = VALUES(jaar), genre_naam = VALUES(genre_naam), beschrijving = VALUES(beschrijving), poster = VALUES(poster), rating = VALUES(rating), updated_at = CURRENT_TIMESTAMP;

-- Index voor snelle zoekopdrachten op genre_naam
CREATE INDEX idx_genre_naam ON films_by_genre(genre_naam);
