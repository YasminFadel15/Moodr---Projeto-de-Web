<?php
include('auth.php');
require_once('db.php');

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['atualizar_dados'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $user_id]);

    $_SESSION['nome'] = $nome;
}

$stmt = $pdo->prepare("SELECT nome, email, foto FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Perfil - Moodr</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            'purple-primary': '#7357C0',
            'purple-medium': '#8F6FE5',
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

<body class="bg-gradient-to-br from-purple-primary via-white-primary to-gray-light min-h-screen text-gray-900 p-6">

<!-- HEADER -->
<header class="flex justify-between items-center mb-10">
  <div></div> 
  <nav class="flex gap-2 text-sm">
    <a href="dashboard.php" class="px-3 py-1.5 rounded-full border border-gray-light hover:bg-gray-light transition">Dashboard</a>
    <a href="calendar.php" class="px-3 py-1.5 rounded-full border border-gray-light hover:bg-gray-light transition">Calendário</a>
    <a href="analytics.php" class="px-3 py-1.5 rounded-full border border-gray-light hover:bg-gray-light transition">Gráficos</a>
    <a href="profile.php" class="px-3 py-1.5 rounded-full border border-purple-medium text-purple-primary font-medium hover:bg-purple-medium hover:text-white transition">Perfil</a>
    <a href="logout.php" class="px-3 py-1.5 rounded-full border border-red-negative text-red-negative hover:bg-red-negative hover:text-white transition">Sair</a>
  </nav>
</header>


  <!-- BLOCO CENTRAL COM PERFIL -->
  <div class="flex justify-center">
    <div class="bg-white w-full max-w-md p-6 rounded-2xl shadow-lg border border-gray-light">
      <h2 class="text-center text-xl font-bold mb-6 text-purple-primary">Informações do Usuário</h2>

      <!-- FOTO -->
      <div class="flex flex-col items-center mb-4">
        <img src="<?= $user['foto'] ? 'uploads/' . htmlspecialchars($user['foto']) : 'https://via.placeholder.com/100?text=Foto' ?>"
             class="w-24 h-24 rounded-full object-cover border mb-2" alt="Foto de Perfil">
        <form action="upload_foto.php" method="POST" enctype="multipart/form-data" class="text-center">
          <input type="file" name="foto" accept="image/*" class="text-sm mb-2">
          <button type="submit" class="text-xs text-white bg-purple-primary hover:bg-purple-600 px-3 py-1 rounded-full">Atualizar Foto</button>
        </form>
      </div>

      <!-- FORMULÁRIO DE NOME/EMAIL -->
      <form method="POST" class="space-y-3">
        <input type="hidden" name="atualizar_dados" value="1">
        <input type="text" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" class="w-full p-2 rounded-full border bg-gray-light text-sm" required>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full p-2 rounded-full border bg-gray-light text-sm" required>
        <button type="submit" class="w-full bg-purple-primary text-white py-2 rounded-full text-sm font-medium hover:bg-purple-600 transition">Salvar Alterações</button>
      </form>

      <!-- FORMULÁRIO DE SENHA -->
      <form method="POST" action="change_password.php" class="mt-6 space-y-2">
        <input type="password" name="senha_atual" placeholder="Senha atual" required class="w-full p-2 rounded-full border bg-gray-light text-sm">
        <input type="password" name="nova_senha" placeholder="Nova senha" required class="w-full p-2 rounded-full border bg-gray-light text-sm">
        <input type="password" name="confirmar_senha" placeholder="Confirmar nova senha" required class="w-full p-2 rounded-full border bg-gray-light text-sm">
        <button type="submit" class="w-full bg-purple-primary text-white py-2 rounded-full text-sm font-medium hover:bg-purple-600 transition">Atualizar Senha</button>
      </form>
    </div>
  </div>

</body>
</html>
