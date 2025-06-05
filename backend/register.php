<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar'];

    if ($senha !== $confirmar) {
        die("Senhas não coincidem.");
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha_hash) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$nome, $email, $senha_hash]);
        header("Location: login.php");
    } catch (PDOException $e) {
        echo "Erro ao cadastrar: " . $e->getMessage();
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br" class="bg-white text-black dark:bg-gray-900 dark:text-white">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Moodr</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center">
<div class="bg-white dark:bg-gray-800 p-8 rounded shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold text-center mb-6">Criar Conta</h2>
    <form method="POST" class="space-y-4">
        <input type="text" name="nome" placeholder="Nome" required class="w-full p-2 rounded border dark:bg-gray-700" />
        <input type="email" name="email" placeholder="Email" required class="w-full p-2 rounded border dark:bg-gray-700" />
        <input type="password" name="senha" placeholder="Senha" required class="w-full p-2 rounded border dark:bg-gray-700" />
        <input type="password" name="confirmar" placeholder="Confirmar senha" required class="w-full p-2 rounded border dark:bg-gray-700" />
        <button type="submit" class="w-full bg-purple-600 text-white p-2 rounded hover:bg-purple-700 transition">Criar conta</button>
    </form>
    <p class="mt-4 text-center text-sm">Já tem uma conta? <a href="login.php" class="text-purple-600 underline">Entrar</a></p>
</div>
</body>
</html>
