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
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Moodr</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'purple-primary': '#7357C0',
                        'purple-medium': '#8F6FE5',
                        'purple-light': '#C194ED',
                        'purple-dark': '#544E7E',
                        'purple-dark-2': '#423C52',
                        'gray-primary': '#9695AB',
                        'gray-light': '#D1CFE5',
                        'white-primary': '#F9F8FF',
                        'red-negative': '#E64848',
                        'green-positive': '#3FCF92',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Manrope', sans-serif; }
    </style>
</head>

<body class="bg-white-primary min-h-screen flex items-center justify-center p-6">

    <div class="bg-white border border-gray-light rounded-2xl shadow-xl w-full max-w-md p-8">
        <h2 class="text-3xl font-bold text-center mb-6">Entrar</h2>

        <?php if (isset($erro)): ?>
            <p class="text-red-negative text-sm text-center mb-4"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <input type="email" name="email" placeholder="Email"
                   class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary" required>

            <input type="password" name="senha" placeholder="Senha"
                   class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary" required>

            <button type="submit"
                    class="w-full bg-purple-primary hover:bg-purple-medium text-white px-6 py-3 rounded-full font-semibold transition">
                Entrar
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-primary">
            Não tem uma conta?
            <a href="register.php" class="text-purple-primary hover:underline">Cadastre-se</a>
        </p>
    </div>

</body>
</html>
