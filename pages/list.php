<?php
/**
 * PAGINA: FILMS WEERGEVEN (Overzicht)
 * 
 * Dit script:
 * 1. Haalt alle films op uit database (optioneel gefilterd door zoekterm)
 * 2. Rendert een HTML-tabel met films
 * 3. Laat zoekformulier zien en knoppen voor add/edit/delete
 * 4. Laat externe API-data zien (via OMDB)
 */

// Laad helper-functies
require_once __DIR__ . '/../scripts/functions.php';

// Laad config en bepaal basis-URL zodat links en assets altijd werken
// Dit is nodig als pagina direct wordt geopend (niet via index.php)
$config = require __DIR__ . '/../config.php';

// Bepaal basis-URL: /film_app of /
$script_dir = dirname($_SERVER['SCRIPT_NAME']);
if (basename($script_dir) === 'pages') {
    // Pagina direct geopend: /film_app/pages/list.php => base = /film_app
    $base = dirname($script_dir);
} else {
    // Via index.php: /film_app/index.php => base = /film_app
    $base = $script_dir;
}

// Override basis-URL als ingesteld in config.php
if (!empty($config->base_url)) {
    $base = rtrim($config->base_url, '/');
}

// Laad services als pagina direct wordt geopend
if (!class_exists('FilmService')) {
    require_once __DIR__ . '/../scripts/FilmService.php';
}
if (!class_exists('ApiService')) {
    require_once __DIR__ . '/../scripts/ApiService.php';
}

// Instantieer services
$service = new FilmService();
$api = new ApiService();

// Haal zoekterm op uit URL (?s=zoekterm)
$search = $_GET['s'] ?? null;

// Haal alle films op (gefilterd als zoekterm ingevoerd)
$films = $service->list($search);
?>
<!-- ============================================
     HTML TEMPLATES
     Hieronder begint de HTML die in browser renderen
     ============================================ -->
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Filmbeheer - Overzicht</title>
    
    <!-- Bootstrap CSS: moderne, responsive styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $base ?>/stylesheet/style.css">
    
    <!-- Custom JavaScript -->
    <script src="<?= $base ?>/scripts/main.js" defer></script>
</head>
<body>
<div class="container my-4">
    <?php include __DIR__ . '/_nav.php'; ?>

    <!-- ========== ZOEKFORMULIER ========== -->
    <form class="row g-2 mb-3" method="get" action="<?= $base ?>/index.php">
        <!-- Zeg tegen router dat we 'list' willen -->
        <input type="hidden" name="page" value="list">
        
        <div class="col-auto flex-grow-1">
            <!-- Zoekterm-veld -->
            <input class="form-control" type="text" name="s" placeholder="Zoek op titel" value="<?= e($search) ?>">
        </div>
        
        <div class="col-auto">
            <!-- Zoek-button -->
            <button class="btn btn-primary" type="submit">Zoek</button>
            
            <!-- Link naar 'Nieuwe film toevoegen' pagina -->
            <a class="btn btn-success ms-2" href="<?= $base ?>/index.php?page=create">Nieuwe film</a>
            <a class="btn btn-outline-secondary ms-2" href="<?= $base ?>/index.php?page=genre">Zoek op genre</a>
        </div>
    </form>

    <!-- ========== FILMS-TABEL ========== -->
    <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th style="width:80px;">Poster</th>
                <th>Titel</th>
                <th>Jaar</th>
                <th>Genre</th>
                <th>Beschrijving</th>
                <th>Rating</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
        <!-- Loop door alle films en render als rij -->
        <?php foreach ($films as $f): ?>
            <tr>
                <!-- Poster thumbnail -->
                <td style="width:80px;">
                    <?php if (!empty($f['poster'])): ?>
                        <img src="<?= e($f['poster']) ?>" alt="Poster" style="height:60px;object-fit:cover;border-radius:4px;" />
                    <?php else: ?>
                        <img src="https://via.placeholder.com/60x90?text=No" alt="Geen poster" style="height:60px;object-fit:cover;border-radius:4px;" />
                    <?php endif; ?>
                </td>
                <!-- Titel met defensieve null-check (?? '') -->
                <td><?= e($f['titel'] ?? '') ?></td>
                
                <!-- Jaar -->
                <td><?= e($f['jaar'] ?? '') ?></td>

                <!-- Genre (uit LEFT JOIN met genres) -->
                <td><?= e($f['genre_naam'] ?? '-') ?></td>
                
                <!-- Beschrijving -->
                <td><?= e($f['beschrijving'] ?? '') ?></td>

                <!-- Rating -->
                <td><?= e($f['rating'] ?? '-') ?></td>
                
                <!-- Acties: Bewerk en Verwijder knoppen -->
                <td class="text-nowrap">
                    <!-- Bewerk-link -->
                    <a class="btn btn-sm btn-outline-primary" href="<?= $base ?>/index.php?page=edit&id=<?= e($f['id'] ?? '') ?>">Bewerk</a>
                    
                    <!-- Verwijder-link met bevestigings-dialoog -->
                    <a class="btn btn-sm btn-outline-danger ms-1" href="<?= $base ?>/index.php?page=delete&id=<?= e($f['id'] ?? '') ?>" onclick="return confirm('Weet je het zeker?')">Verwijder</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <div class="card mt-4">
      <div class="card-body">
        <h5 class="card-title">Externe info ophalen (optioneel)</h5>
        <form class="row g-2" method="post" action="<?= $base ?>/index.php?page=list">
          <div class="col-sm-8">
            <input class="form-control" type="text" name="title" placeholder="Voer filmtitel in">
          </div>
          <div class="col-auto">
            <button class="btn btn-secondary" type="submit">Haal op</button>
          </div>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title'])) {
            $res = $api->fetchByTitle($_POST['title']);
            if ($res) {
                // Zorg voor fallback-afbeelding wanneer geen poster beschikbaar is
                $poster = !empty($res['Poster']) && $res['Poster'] !== 'N/A' ? $res['Poster'] : 'https://via.placeholder.com/300x450?text=No+Poster';
                ?>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="card">
                            <img src="<?= e($poster) ?>" class="card-img-top" alt="Poster: <?= e($res['Title'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= e($res['Title'] ?? 'Onbekende titel') ?> <small class="text-muted">(<?= e($res['Year'] ?? '-') ?>)</small></h5>
                                <p class="card-text"><strong>Genre:</strong> <?= e($res['Genre'] ?? '-') ?></p>
                                <p class="card-text"><strong>Regisseur:</strong> <?= e($res['Director'] ?? '-') ?></p>
                                <p class="card-text"><strong>Acteurs:</strong> <?= e($res['Actors'] ?? '-') ?></p>
                                <p class="card-text"><strong>IMDb:</strong> <?= e($res['imdbRating'] ?? '-') ?></p>
                                <p class="card-text mt-2"><?= e($res['Plot'] ?? '') ?></p>
                                <form method="post" action="<?= $base ?>/index.php?page=import" class="mt-3">
                                    <input type="hidden" name="Title" value="<?= e($res['Title'] ?? '') ?>">
                                    <input type="hidden" name="Year" value="<?= e($res['Year'] ?? '') ?>">
                                    <input type="hidden" name="Plot" value="<?= e($res['Plot'] ?? '') ?>">
                                    <input type="hidden" name="Genre" value="<?= e($res['Genre'] ?? '') ?>">
                                    <input type="hidden" name="Poster" value="<?= e($res['Poster'] ?? '') ?>">
                                    <input type="hidden" name="imdbRating" value="<?= e($res['imdbRating'] ?? '') ?>">
                                    <button class="btn btn-success" type="submit">Importeer naar database</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                echo '<div class="mt-3 text-danger">Geen externe data (controleer config.php voor API-key).</div>';
            }
        }
        ?>
      </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
