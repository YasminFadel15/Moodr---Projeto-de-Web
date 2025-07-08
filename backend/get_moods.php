<?php
require_once 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado.']);
    exit;
}

$stmt = $pdo->prepare("SELECT nome, emoji FROM custom_moods WHERE user_id = ?");
$stmt->execute([$user_id]);
$customMoods = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($customMoods);
