-- ================================================
-- SQL DATABASE SCHEMA voor Filmbeheer-applicatie
-- ================================================

-- Maak database aan (IF NOT EXISTS = voorkomen dubbel)
CREATE DATABASE IF NOT EXISTS film_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Selecteer de database voor verdere operaties
USE film_app;

-- ================================================
-- TABEL: GENRES (Filmgenres)
-- ================================================
-- Deze tabel bevat alle mogelijke filmgenres (Actie, Drama, etc.)
-- Vervolgens kan elke film gekoppeld worden aan één genre
CREATE TABLE IF NOT EXISTS genres (
  id INT AUTO_INCREMENT PRIMARY KEY,
  genre_naam VARCHAR(100) NOT NULL
);

-- ================================================
-- TABEL: FILMS (Films)
-- ================================================
-- Dit is de hoofdtabel met alle films
CREATE TABLE IF NOT EXISTS films (
  id INT AUTO_INCREMENT PRIMARY KEY,              -- Unieke film-ID (auto-increment)
  titel VARCHAR(255) NOT NULL,                    -- Filmtitel (verplicht)
  jaar VARCHAR(10) DEFAULT NULL,                  -- Jaar (optioneel)
  genre_id INT DEFAULT NULL,                      -- Referentie naar genres-tabel
  beschrijving TEXT,                              -- Filmplot/beschrijving (optioneel)
  poster VARCHAR(512) DEFAULT NULL,
  rating VARCHAR(10) DEFAULT NULL,
  
  -- Foreign Key: genre_id verwijst naar genres.id
  -- ON DELETE SET NULL = als genre verwijderd, zet genre_id op NULL
  FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE SET NULL
);

-- ================================================
-- SEED DATA: Standaard genres
-- ================================================
-- Voeg standaard genres in
-- ON DUPLICATE KEY UPDATE = update als al bestaat
INSERT INTO genres (genre_naam) VALUES ('Actie'), ('Drama'), ('Komedie') 
ON DUPLICATE KEY UPDATE genre_naam=genre_naam;
