<?php
require_once 'db.php';
session_start();

$user_id = $_SESSION['user_id'];
$mood_id = $_POST['id'];
$data = $_POST['data'];
$humor = $_POST['humor'];
$anotacao = $_POST['anotacao'] ?? '';
$tags_raw = $_POST['tags'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM mood_entries WHERE id = ? AND user_id = ?");
$stmt->execute([$mood_id, $user_id]);
$exists = $stmt->fetch();

if (!$exists) {
    die("Registro inválido.");
}

$stmt = $pdo->prepare("UPDATE mood_entries SET humor = ?, anotacao = ? WHERE id = ?");
$stmt->execute([$humor, $anotacao, $mood_id]);

$pdo->prepare("DELETE FROM tags WHERE mood_entry_id = ?")->execute([$mood_id]);

$tags = array_filter(array_map('trim', explode(",", $tags_raw)));
foreach ($tags as $tag) {
    $pdo->prepare("INSERT INTO tags (mood_entry_id, tag) VALUES (?, ?)")->execute([$mood_id, $tag]);
}

header("Location: dashboard.php");
exit;

