<?php
header('Content-Type: application/json; charset=utf-8');

$search = $_GET['s'] ?? null;

require_once __DIR__ . '/../scripts/ActorService.php';

try {
    $service = new ActorService();
    $rows = $service->list($search);

    echo json_encode([
        'error' => null,
        'data' => $rows
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Kon acteurs niet ophalen',
        'data' => []
    ]);
}
