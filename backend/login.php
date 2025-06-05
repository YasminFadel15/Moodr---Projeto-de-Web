<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nome'] = $user['nome'];
        header("Location: dashboard.php");
        exit;
    } else {
        $erro = "Email ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br" class="bg-white text-black dark:bg-gray-900 dark:text-white">
<head>
    <meta charset="UTF-8">
    <title>Login - Moodr</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center">
<div class="bg-white dark:bg-gray-800 p-8 rounded shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold text-center mb-6">Entrar</h2>
    <?php if (isset($erro)): ?>
        <p class="text-red-500 text-sm mb-4 text-center"><?= $erro ?></p>
    <?php endif; ?>
    <form method="POST" class="space-y-4">
        <input type="email" name="email" placeholder="Email" required class="w-full p-2 rounded border dark:bg-gray-700" />
        <input type="password" name="senha" placeholder="Senha" required class="w-full p-2 rounded border dark:bg-gray-700" />
        <button type="submit" class="w-full bg-purple-600 text-white p-2 rounded hover:bg-purple-700 transition">Entrar</button>
    </form>
    <p class="mt-4 text-center text-sm">Não tem uma conta? <a href="register.php" class="text-purple-600 underline">Cadastre-se</a></p>
</div>
</body>
</html>
