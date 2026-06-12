<?php
/**
 * PAGINA: NIEUWE FILM TOEVOEGEN
 * 
 * Dit script:
 * 1. Toont een leeg formulier
 * 2. Als POST: valideer input via FilmService
 * 3. Als OK: redirect naar list
 * 4. Als fouten: hertoont formulier met foutmeldingen
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

// Initialiseer error-array
$errors = [];

// Controleer of formulier is verzonden (POST-request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Voer validatie en opslaan uit via Service
    $errors = $service->create($_POST);
    
    // Als geen fouten: alle checks passes => redirect naar films-list
    if (empty($errors)) {
        header('Location: ' . $base . '/index.php?page=list');
        exit; // Stop script
    }
    // Als er fouten zijn: formulier herendered met foutberichten
}

// Haal beschikbare genres op voor keuzelijst
$genres = $service->getGenres();
?>
<!-- ============================================
     HTML TEMPLATE: FORMULIER VOOR NIEUWE FILM
     ============================================ -->
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nieuwe film</title>
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
            <h1 class="h4 mb-4">Nieuwe film toevoegen</h1>
            
            <!-- Foutmeldingen tonen (als fouten) -->
            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <?= e(implode(', ', $errors)) ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulier: POST naar index.php?page=create -->
    <form method="post" action="<?= $base ?>/index.php?page=create" onsubmit="return validateForm(this)">
        <!-- TITEL -->
        <div class="mb-3">
            <label class="form-label">Titel</label>
            <input class="form-control" type="text" name="titel" required>
        </div>
        
        <!-- JAAR -->
        <div class="mb-3">
            <label class="form-label">Jaar</label>
            <input class="form-control" type="text" name="jaar">
        </div>

        <!-- RATING -->
        <div class="mb-3">
            <label class="form-label">Rating</label>
            <input class="form-control" type="text" name="rating" placeholder="Bijv. 8.7">
        </div>

        <div class="mb-3">
            <label class="form-label">Genre</label>
            <select class="form-select" name="genre_id">
                <option value="">Kies een genre</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?= e($genre['id']) ?>"><?= e($genre['genre_naam']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- BESCHRIJVING -->
        <div class="mb-3">
            <label class="form-label">Beschrijving</label>
            <textarea class="form-control" name="beschrijving" rows="4"></textarea>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
