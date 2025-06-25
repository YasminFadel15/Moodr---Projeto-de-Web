<?php
require_once 'db.php';
session_start();

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
  http_response_code(401);
  echo json_encode(['error' => 'Usuário não autenticado.']);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$nome = trim($data['nome'] ?? '');
$emoji = trim($data['emoji'] ?? '');

if ($nome === '' || $emoji === '') {
  http_response_code(400);
  echo json_encode(['error' => 'Nome e emoji são obrigatórios.']);
  exit;
}

try {
  $stmt = $pdo->prepare("INSERT IGNORE INTO custom_moods (user_id, nome, emoji) VALUES (?, ?, ?)");
  $stmt->execute([$user_id, $nome, $emoji]);
  echo json_encode(['success' => true]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Erro ao salvar no banco de dados.']);
}
