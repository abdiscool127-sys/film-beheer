<?php
// PAGINA: Zoek films op genre
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

$genre = $_GET['genre'] ?? null;

$genres = $service->getGenres();

$films = [];
$api_error = null;

if ($genre) {
    // Gebruik de bestaande repository-logica direct, zonder extra HTTP-call.
    $films = $service->listByGenre($genre);
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zoek op genre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/stylesheet/style.css">
</head>

<body>
<div class="container my-4">

<?php include __DIR__ . '/_nav.php'; ?>

<form class="row g-2 mb-3" method="get" action="<?= $base ?>/index.php">
    <input type="hidden" name="page" value="genre">

    <div class="col-md-6 col-sm-8">
        <select name="genre" class="form-select">
            <option value="">-- Kies genre --</option>
            <?php foreach ($genres as $g): ?>
                <option value="<?= e($g['genre_naam']) ?>" <?= ($genre == $g['genre_naam']) ? 'selected' : '' ?>>
                    <?= e($g['genre_naam']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-auto">
        <button class="btn btn-primary" type="submit">Zoek</button>
    </div>
</form>

<div class="mb-3">
    <?php foreach ($genres as $g): ?>
        <a class="btn btn-sm btn-outline-secondary me-1 mb-1"
           href="<?= $base ?>/index.php?page=genre&genre=<?= urlencode($g['genre_naam']) ?>">
            <?= e($g['genre_naam']) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if ($genre && empty($films)): ?>
    <div class="alert alert-info">Geen films gevonden voor dit genre.</div>
<?php endif; ?>

<?php if (!empty($films)): ?>
<div class="table-responsive">
<table class="table table-striped table-bordered align-middle">
    <thead class="table-light">
    <tr>
        <th>Poster</th>
        <th>Titel</th>
        <th>Jaar</th>
        <th>Genre</th>
        <th>Beschrijving</th>
        <th>Rating</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($films as $f): ?>
        <tr>
            <td>
                <?php if (!empty($f['poster'])): ?>
                    <img src="<?= e($f['poster']) ?>" style="height:60px;border-radius:4px;">
                <?php else: ?>
                    <img src="https://via.placeholder.com/60x90?text=No" style="height:60px;">
                <?php endif; ?>
            </td>
            <td><?= e($f['titel'] ?? '') ?></td>
            <td><?= e($f['jaar'] ?? '') ?></td>
            <td><?= e($f['genre_naam'] ?? '-') ?></td>
            <td><?= e($f['beschrijving'] ?? '') ?></td>
            <td><?= e($f['rating'] ?? '-') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>