<?php
require_once 'db.php';
session_start();

$user_id = $_SESSION['user_id'];
$mood_id = $_GET['id'] ?? null;

if (!$mood_id) {
    die("ID inválido.");
}

$stmt = $pdo->prepare("SELECT id FROM mood_entries WHERE id = ? AND user_id = ?");
$stmt->execute([$mood_id, $user_id]);

if (!$stmt->fetch()) {
    die("Registro não encontrado ou não autorizado.");
}

$pdo->prepare("DELETE FROM mood_entries WHERE id = ?")->execute([$mood_id]);

header("Location: dashboard.php");
exit;
