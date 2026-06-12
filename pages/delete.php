<?php
/**
 * PAGINA: VERWIJDER FILM
 * 
 * Dit script:
 * 1. Haalt film-ID op uit URL (?id=2)
 * 2. Verwijdert film uit database
 * 3. Redirect terug naar films-list
 * 
 * Geen HTML output: alleen backend-logica!
 */

// Laad helper-functies
require_once __DIR__ . '/../scripts/functions.php';

// Laad config en bepaal basis-URL
$config = require __DIR__ . '/../config.php';
$script_dir = dirname($_SERVER['SCRIPT_NAME']);
if (basename($script_dir) === 'pages') {
    $base = dirname($script_dir);
} else {
    $base = $script_dir;
}
if (!empty($config->base_url)) {
    $base = rtrim($config->base_url, '/');
}

// Laad service
if (!class_exists('FilmService')) {
    require_once __DIR__ . '/../scripts/FilmService.php';
}
$service = new FilmService();

// Haal film-ID op uit URL
$id = $_GET['id'] ?? null;

// Als ID beschikbaar: verwijder de film
if ($id) {
    $service->delete($id);
}

// Redirect naar list (ongeacht of verwijdering geslaagd was)
header('Location: ' . $base . '/index.php?page=list');
exit;
