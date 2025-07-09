<?php
header('Content-Type: application/json');
session_start();
require_once 'db.php';

ini_set('display_errors', 0);
error_reporting(0);

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT nome, emoji FROM custom_moods WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro no servidor"]);
}
