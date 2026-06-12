<?php
/**
 * PAGINA: IMPORTEREN VAN API-FILM NAAR DATABASE
 *
 * Verwacht POST velden: Title, Year, Plot, Genre, Poster, imdbRating
 */

require_once __DIR__ . '/../scripts/functions.php';

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

if (!class_exists('FilmService')) {
    require_once __DIR__ . '/../scripts/FilmService.php';
}
$service = new FilmService();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['Title'])) {
    // Map POST naar API-achtig array en importeer
    $apiData = [
        'Title' => $_POST['Title'] ?? '',
        'Year' => $_POST['Year'] ?? null,
        'Plot' => $_POST['Plot'] ?? null,
        'Genre' => $_POST['Genre'] ?? null,
        'Poster' => $_POST['Poster'] ?? null,
        'imdbRating' => $_POST['imdbRating'] ?? null,
    ];

    $errors = $service->importFromApi($apiData);
    if (empty($errors)) {
        header('Location: ' . $base . '/index.php?page=list');
        exit;
    }
}

// Als fouten: toon eenvoudige foutmelding en link terug
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Importeer film</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/stylesheet/style.css">
</head>
<body>
<div class="container my-4">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div class="card">
        <div class="card-body">
            <h1 class="h4 mb-4">Importeren mislukt</h1>
            <?php if ($errors): ?>
                <div class="alert alert-danger"><?= e(implode(', ', $errors)) ?></div>
            <?php else: ?>
                <div class="alert alert-warning">Geen gegevens om te importeren.</div>
            <?php endif; ?>
            <a class="btn btn-primary" href="<?= $base ?>/index.php?page=list">Terug naar overzicht</a>
        </div>
    </div>
</div>
</body>
</html>
