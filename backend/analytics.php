<?php
include('auth.php');
require_once('db.php');

$user_id = $_SESSION['user_id'];

// Frequência total por humor (para gráfico de pizza)
$stmt = $pdo->prepare("SELECT humor, COUNT(*) AS total FROM mood_entries WHERE user_id = ? GROUP BY humor");
$stmt->execute([$user_id]);
$frequencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Humores fixos com emojis
$fixos = [
    ['nome' => 'triste',   'emoji' => '😢'],
    ['nome' => 'ansioso',  'emoji' => '😰'],
    ['nome' => 'irritado', 'emoji' => '😠'],
    ['nome' => 'calmo',    'emoji' => '😌'],
    ['nome' => 'feliz',    'emoji' => '😊'],
];

// Personalizados
$stmt = $pdo->prepare("SELECT nome, emoji FROM custom_moods WHERE user_id = ?");
$stmt->execute([$user_id]);
$personalizados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mapeia humores
$todosHumores = array_merge($fixos, $personalizados);
$humorMap = [];
foreach ($todosHumores as $h) {
    $humorMap[$h['nome']] = "{$h['emoji']} " . ucfirst($h['nome']);
}

// Últimos 30 registros
$stmt = $pdo->prepare("SELECT data, humor FROM mood_entries WHERE user_id = ? ORDER BY data DESC LIMIT 30");
$stmt->execute([$user_id]);
$registros = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

// Gera dados para o gráfico
$labels = [];
$humoresExibidos = [];
foreach ($registros as $r) {
    $labels[] = date('d/m/Y', strtotime($r['data']));
    $humoresExibidos[] = $humorMap[$r['humor']] ?? ucfirst($r['humor']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Gráficos de Humor - Moodr</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
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
  <div class="bg-white rounded-2xl border border-gray-light shadow-xl w-full max-w-3xl p-6 space-y-6">

    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-2">
      <h1 class="text-3xl font-bold">Gráficos de Humor</h1>
      <a href="dashboard.php" class="text-purple-primary font-semibold hover:underline text-sm">← Voltar</a>
    </div>

    <!-- Gráfico de Pizza -->
    <section>
      <h2 class="text-lg font-semibold mb-2">Humores mais frequentes</h2>
      <div class="bg-gray-light rounded-xl p-3 max-w-sm mx-auto">
        <canvas id="graficoPizza" width="200" height="200"></canvas>
      </div>
    </section>

<!-- Gráfico de Barras Verticais Compacto -->
<section>
  <h2 class="text-lg font-semibold mb-2">Histórico de Humor</h2>
  <div class="bg-gray-light rounded-xl p-2">
    <canvas id="graficoBarrasCompacto" height="180"></canvas>
  </div>
</section>



  <script>
    // Gráfico de Pizza
    const pizzaCtx = document.getElementById('graficoPizza').getContext('2d');
    new Chart(pizzaCtx, {
      type: 'pie',
      data: {
        labels: <?= json_encode(array_column($frequencias, 'humor')) ?>,
        datasets: [{
          data: <?= json_encode(array_column($frequencias, 'total')) ?>,
          backgroundColor: ['#7357C0', '#8F6FE5', '#C194ED', '#544E7E', '#423C52']
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#423C52',
              font: { size: 12 }
            }
          }
        }
      }
    });


  const humorLabels = <?= json_encode($humoresExibidos, JSON_UNESCAPED_UNICODE) ?>;
  const humorDatas = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;


    // Gráfico de Barras Verticais (compacto)
const ctxCompacto = document.getElementById('graficoBarrasCompacto').getContext('2d');

new Chart(ctxCompacto, {
  type: 'bar',
  data: {
    labels: humorLabels,
    datasets: [{
      label: 'Humor',
      data: Array(humorLabels.length).fill(1),
      backgroundColor: '#7357C0',
      barPercentage: 0.5,
      categoryPercentage: 0.6
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          title: function(context) {
            return `Humor: ${context[0].label}`;
          },
          label: function(context) {
            return `Data: ${humorDatas[context.dataIndex]}`;
          }
        }
      }
    },
    scales: {
      x: {
        ticks: {
          color: '#423C52',
          font: { size: 9 },
          maxRotation: 45,
          minRotation: 45
        },
        grid: { display: false }
      },
      y: {
        display: false,
        grid: { display: false }
      }
    }
  }
});


  </script>
</body>
</html>
