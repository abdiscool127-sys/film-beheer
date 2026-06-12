<?php
/**
 * PAGINA: FILM BEWERKEN
 * 
 * Dit script:
 * 1. Haalt film-ID op uit URL (?id=2)
 * 2. Laadt de film uit database
 * 3. Toont formulier met huidigegegevens
 * 4. Als POST: valideer input en sla op
 * 5. Als OK: redirect naar list
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

// Laad services
if (!class_exists('FilmService')) {
    require_once __DIR__ . '/../scripts/FilmService.php';
}
if (!class_exists('ApiService')) {
    require_once __DIR__ . '/../scripts/ApiService.php';
}
$service = new FilmService();

// Haal film-ID op uit URL (?id=2)
$id = $_GET['id'] ?? null;

// Als geen ID: terug naar list
if (!$id) { 
    header('Location: ' . $base . '/index.php?page=list'); 
    exit; 
}

// Laad film uit database
$film = $service->get($id);

// Haal beschikbare genres op voor keuzelijst
$genres = $service->getGenres();

// Als film niet gevonden: foutmelding
if (!$film) { 
    echo 'Film niet gevonden'; 
    exit; 
}

// Initialiseer error-array
$errors = [];

// Controleer of formulier is verzonden (POST-request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Voer validatie en update uit
    $errors = $service->update($id, $_POST);
    
    // Als geen fouten: redirect naar films-list
    if (empty($errors)) {
        header('Location: ' . $base . '/index.php?page=list');
        exit;
    }
}
?>
<!-- ============================================
     HTML TEMPLATE: FORMULIER VOOR BESTAANDE FILM
     ============================================ -->
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Film bewerken</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $base ?>/stylesheet/style.css">
</head>
<body>
<div class="container my-4">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div class="card">
        <div class="card-body">
            <h1 class="h4 mb-4">Film bewerken</h1>
            
            <!-- Foutmeldingen tonen (als fouten) -->
            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <?= e(implode(', ', $errors)) ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulier: POST naar index.php?page=edit&id=X -->
    <form method="post" action="<?= $base ?>/index.php?page=edit&id=<?= e($film['id'] ?? '') ?>" onsubmit="return validateForm(this)">
        <!-- TITEL (ingevuld met huidigegegevens) -->
        <div class="mb-3">
            <label class="form-label">Titel</label>
            <input class="form-control" type="text" name="titel" value="<?= e($film['titel'] ?? '') ?>" required>
        </div>
        
        <!-- JAAR -->
        <div class="mb-3">
            <label class="form-label">Jaar</label>
            <input class="form-control" type="text" name="jaar" value="<?= e($film['jaar'] ?? '') ?>">
        </div>

        <!-- RATING -->
        <div class="mb-3">
            <label class="form-label">Rating</label>
            <input class="form-control" type="text" name="rating" value="<?= e($film['rating'] ?? '') ?>" placeholder="Bijv. 8.7">
        </div>

        <div class="mb-3">
            <label class="form-label">Genre</label>
            <select class="form-select" name="genre_id">
                <option value="">Kies een genre</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?= e($genre['id']) ?>" <?= (string)($film['genre_id'] ?? '') === (string)$genre['id'] ? 'selected' : '' ?>>
                        <?= e($genre['genre_naam']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- BESCHRIJVING -->
        <div class="mb-3">
            <label class="form-label">Beschrijving</label>
            <textarea class="form-control" name="beschrijving" rows="4"><?= e($film['beschrijving'] ?? '') ?></textarea>
        </div>
        
        <!-- ACTIES: Opslaan en Terug -->
        <button class="btn btn-primary" type="submit">Opslaan</button>
        <a class="btn btn-link" href="<?= $base ?>/index.php?page=list">Terug</a>
    </form>
        </div>
    </div>
</div>

<!-- Custom JavaScript (client-side validatie) -->
<script src="<?= $base ?>/scripts/main.js"></script>
<!-- Bootstrap JavaScript Bundle -->
</body>
</html>
