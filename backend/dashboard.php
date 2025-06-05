<?php
include('auth.php');
require_once('db.php');

// ZenQuotes API
$frase = "Seja bem-vindo!";
$autor = "";

try {
    $response = file_get_contents("https://zenquotes.io/api/random");
    $data = json_decode($response, true);
    if (isset($data[0])) {
        $frase = $data[0]['q'];
        $autor = $data[0]['a'];
    }
} catch (Exception $e) {
    $frase = "Frase indisponível no momento.";
}
?>

<!DOCTYPE html>
<html lang="pt-br" class="bg-white text-black dark:bg-gray-900 dark:text-white">
<head>
  <meta charset="UTF-8">
  <title>Moodr - Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen p-6">
  <header class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Olá, <?= $_SESSION['nome'] ?> 👋</h1>
    <div class="flex items-center gap-4">
      <a href="calendar.php" class="text-purple-600 hover:underline">Calendário</a>
      <a href="analytics.php" class="text-purple-600 hover:underline">Gráficos</a>
      <a href="profile.php" class="text-purple-600 hover:underline">Perfil</a>
      <a href="logout.php" class="text-red-500 hover:underline">Sair</a>
    </div>
  </header>

  <!-- Frase motivacional -->
  <section class="bg-white dark:bg-gray-800 p-4 rounded shadow mb-6">
    <h2 class="text-xl font-semibold mb-2">📝 Frase do Dia</h2>
    <p class="italic">"<?= $frase ?>"</p>
    <p class="text-right mt-2 text-sm">— <?= $autor ?></p>
  </section>

  <!-- Ações -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
      <h2 class="text-lg font-semibold mb-2">📈 Estatísticas rápidas</h2>
      <p>Acesse seu <a href="analytics.php" class="text-purple-600 underline">relatório visual</a>.</p>
    </div>
    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow flex flex-col justify-center items-center">
      <h2 class="text-lg font-semibold mb-4">➕ Registrar Humor</h2>
      <a href="mood_register.php" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition">Registrar agora</a>
    </div>
  </div>
</body>
</html>
