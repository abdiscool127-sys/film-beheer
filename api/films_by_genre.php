<?php
header('Content-Type: application/json; charset=utf-8');

$genre = $_GET['genre'] ?? '';

if ($genre === '') {
    echo json_encode([
        'error' => 'Geen genre opgegeven',
        'data' => []
    ]);
    exit;
}

require_once __DIR__ . '/../scripts/FilmService.php';

try {
    $service = new FilmService();
    $rows = $service->listByGenre($genre);

    echo json_encode([
        'error' => null,
        'data' => $rows
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Kon genregegevens niet ophalen.',
        'data' => []
    ]);
}