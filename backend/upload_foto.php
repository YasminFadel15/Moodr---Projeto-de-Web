<?php
include('auth.php');
require_once('db.php');

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['foto'])) {
    $foto = $_FILES['foto'];
    
    if ($foto['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $nomeArquivo = 'foto_' . $user_id . '_' . time() . '.' . $ext;
        $caminho = 'uploads/' . $nomeArquivo;

        move_uploaded_file($foto['tmp_name'], $caminho);

        $stmt = $pdo->prepare("UPDATE users SET foto = ? WHERE id = ?");
        $stmt->execute([$nomeArquivo, $user_id]);
    }
}

header('Location: profile.php');
exit;
