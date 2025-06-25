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
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Moodr</title>
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
        <h2 class="text-3xl font-bold text-center mb-6">Criar Conta</h2>

        <form method="POST" class="space-y-5">
            <input type="text" name="nome" placeholder="Nome"
                   class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary" required>

            <input type="email" name="email" placeholder="Email"
                   class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary" required>

            <input type="password" name="senha" placeholder="Senha"
                   class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary" required>

            <input type="password" name="confirmar" placeholder="Confirmar Senha"
                   class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary" required>

            <button type="submit"
                    class="w-full bg-purple-primary hover:bg-purple-medium text-white px-6 py-3 rounded-full font-semibold transition">
                Criar Conta
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-primary">
            Já tem uma conta?
            <a href="login.php" class="text-purple-primary hover:underline">Entrar</a>
        </p>
    </div>

</body>
</html>
