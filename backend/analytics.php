<?php
include('auth.php');
require_once('db.php');

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT humor, COUNT(*) AS total FROM mood_entries WHERE user_id = ? GROUP BY humor");
$stmt->execute([$user_id]);
$frequencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT data, humor FROM mood_entries WHERE user_id = ? ORDER BY data DESC LIMIT 15");
$stmt->execute([$user_id]);
$por_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

function humorToNum($humor) {
    return [
        'triste' => 0,
        'ansioso' => 1,
        'irritado' => 2,
        'calmo' => 3,
        'feliz' => 4
    ][$humor] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="pt-br" class="bg-white text-black dark:bg-gray-900 dark:text-white">
<head>
  <meta charset="UTF-8">
  <title>Gráficos de Humor</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="min-h-screen p-6">
  <header class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold">📊 Gráficos de Humor</h1>
    <a href="dashboard.php" class="text-purple-600 hover:underline">← Voltar</a>
  </header>

  <section class="mb-10 bg-white dark:bg-gray-800 p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Emoções mais frequentes</h2>
    <canvas id="graficoPizza" height="300"></canvas>
  </section>

  <section class="bg-white dark:bg-gray-800 p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Linha do tempo (últimos registros)</h2>
    <canvas id="graficoLinha" height="300"></canvas>
  </section>

  <script>
    const pizzaCtx = document.getElementById('graficoPizza').getContext('2d');
    new Chart(pizzaCtx, {
      type: 'pie',
      data: {
        labels: <?= json_encode(array_column($frequencias, 'humor')) ?>,
        datasets: [{
          data: <?= json_encode(array_column($frequencias, 'total')) ?>,
          backgroundColor: ['#34d399', '#60a5fa', '#facc15', '#f87171', '#c084fc'],
        }]
      }
    });

    const linhaCtx = document.getElementById('graficoLinha').getContext('2d');
    new Chart(linhaCtx, {
      type: 'line',
      data: {
        labels: <?= json_encode(array_reverse(array_column($por_data, 'data'))) ?>,
        datasets: [{
          label: 'Humor',
          data: <?= json_encode(array_reverse(array_map(fn($h) => humorToNum($h['humor']), $por_data))) ?>,
          borderColor: '#7c3aed',
          fill: true,
          tension: 0.3
        }]
      },
      options: {
        scales: {
          y: {
            ticks: {
              callback: function(value) {
                const mapa = ['😢 Triste', '😰 Ansioso', '😠 Irritado', '😌 Calmo', '😊 Feliz'];
                return mapa[value] ?? value;
              },
              stepSize: 1
            },
            min: 0,
            max: 4
          }
        }
      }
    });
  </script>
</body>
</html>
