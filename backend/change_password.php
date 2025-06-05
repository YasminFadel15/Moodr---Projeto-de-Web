<?php
require_once 'db.php';
session_start();

$user_id = $_SESSION['user_id'];

$senha_atual = $_POST['senha_atual'];
$nova = $_POST['nova_senha'];
$confirmar = $_POST['confirmar_senha'];

if ($nova !== $confirmar) {
    die("As novas senhas não coincidem.");
}

$stmt = $pdo->prepare("SELECT senha_hash FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$hash = $stmt->fetchColumn();

if (!password_verify($senha_atual, $hash)) {
    die("Senha atual incorreta.");
}

$novo_hash = password_hash($nova, PASSWORD_DEFAULT);
$update = $pdo->prepare("UPDATE users SET senha_hash = ? WHERE id = ?");
$update->execute([$novo_hash, $user_id]);

header("Location: profile.php");
exit;
