<?php
/**
 * ROUTER / ENTRY POINT
 * 
 * Dit is het hoofd-bestand dat voor ALLE requests wordt geopend:
 * - http://localhost/film_app/index.php?page=list
 * - http://localhost/film_app/index.php?page=create
 * - etc.
 * 
 * De router bepaalt welk page-bestand uit /pages/ wordt geladen.
 */

// Laad config (database-, API-instellingen)
$config = require __DIR__ . '/config.php';

// Haal 'page' parameter op uit URL (?page=list)
// Default page: 'list'
$page = $_GET['page'] ?? 'list';

// Lijst van toegestane pages (veiligheid: voorkoom directory traversal)
$allowed = ['list','create','edit','delete','import','genre','actors','directors'];

// Als page niet in allowed-list: fout geven
if (!in_array($page, $allowed)) {
    http_response_code(404);
    echo "Pagina niet gevonden.";
    exit;
}

// Laad de core-services
require_once __DIR__ . '/scripts/FilmService.php';
require_once __DIR__ . '/scripts/ApiService.php';

// Laad het juiste page-bestand uit /pages/ map
include __DIR__ . '/pages/' . $page . '.php';
