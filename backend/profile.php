<?php
include('auth.php');
require_once('db.php');

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT nome, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$total = $pdo->query("SELECT COUNT(*) FROM mood_entries WHERE user_id = $user_id")->fetchColumn();

$frequencias = $pdo->query("
  SELECT humor, COUNT(*) AS total 
  FROM mood_entries 
  WHERE user_id = $user_id 
  GROUP BY humor 
  ORDER BY total DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br" class="bg-white text-black dark:bg-gray-900 dark:text-white">
<head>
  <meta charset="UTF-8">
  <title>Perfil - Moodr</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen p-6">
  <header class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">👤 Meu Perfil</h1>
    <a href="dashboard.php" class="text-purple-600 hover:underline">← Voltar</a>
  </header>

  <section class="bg-white dark:bg-gray-800 p-6 rounded shadow mb-6">
    <h2 class="text-xl font-semibold mb-2">Informações</h2>
    <p><strong>Nome:</strong> <?= htmlspecialchars($user['nome']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
  </section>

  <section class="bg-white dark:bg-gray-800 p-6 rounded shadow mb-6">
    <h2 class="text-xl font-semibold mb-2">Estatísticas</h2>
    <p><strong>Total de registros:</strong> <?= $total ?></p>
    
    <h3 class="mt-4 font-medium">Emoções mais frequentes:</h3>
    <ul class="mt-2 list-disc list-inside">
      <?php foreach ($frequencias as $f): ?>
        <li><?= ucfirst($f['humor']) ?>: <?= $f['total'] ?> registro(s)</li>
      <?php endforeach; ?>
    </ul>
  </section>

  <section class="bg-white dark:bg-gray-800 p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-2">Alterar Senha</h2>
    <form method="POST" action="change_password.php" class="space-y-3">
      <input type="password" name="senha_atual" placeholder="Senha atual" required class="w-full p-2 rounded border dark:bg-gray-700">
      <input type="password" name="nova_senha" placeholder="Nova senha" required class="w-full p-2 rounded border dark:bg-gray-700">
      <input type="password" name="confirmar_senha" placeholder="Confirmar nova senha" required class="w-full p-2 rounded border dark:bg-gray-700">
      <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition">Atualizar</button>
    </form>
  </section>
</body>
</html>
