<?php
/**
 * CONFIGURATIE
 * 
 * Dit bestand bevat alle instellingen die nodig zijn:
 * - Database verbinding (host, naam, gebruiker, wachtwoord)
 * - Externe API (OMDB) instellingen
 * - Basis-URL van de app
 * 
 * VEILIGHEID: voeg dit bestand NOOIT toe aan versiebeheer (git)!
 * In productie: gebruik omgevingsvariabelen in plaats van hardcoded waarden.
 */

return (object) [
    // ========== DATABASE-INSTELLINGEN ==========
    'db_host' => '127.0.0.1',         // MySQL-server host
    'db_name' => 'film_app',          // Database-naam
    'db_user' => 'root',              // MySQL-gebruiker
    'db_pass' => '',                  // MySQL-wachtwoord (standaard leeg voor XAMPP)
    
    // ========== EXTERNE API (OMDB) ==========
    // Vraag een gratis key aan op: http://www.omdbapi.com/apikey.aspx
    'api_key' => '40185109',          // Je OMDB API-key hier
    'api_url' => 'http://www.omdbapi.com/', // OMDB API-endpoint
    
    // ========== BASIS-URL ==========
    // Stel in waar je app draait (bijv. /film_app of /)
    // Laat leeg als je app in de root staat
    'base_url' => '/film_app'
];
