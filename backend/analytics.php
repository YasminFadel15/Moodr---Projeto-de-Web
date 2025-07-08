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

// Formata datas para dd/mm/aaaa
$datas_formatadas = array_map(fn($d) => date('d/m/Y', strtotime($d['data'])), array_reverse($por_data));
$valores_numericos = array_reverse(array_map(fn($h) => humorToNum($h['humor']), $por_data));
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

    <!-- Gráfico de Linha -->
    <section>
      <h2 class="text-lg font-semibold mb-2">Linha do tempo (últimos registros)</h2>
      <div class="bg-gray-light rounded-xl p-4">
        <canvas id="graficoLinha" height="220"></canvas>
      </div>
    </section>

  </div>

  <script>
    // Gráfico de Pizza (compacto e com tons de roxo)
    const pizzaCtx = document.getElementById('graficoPizza').getContext('2d');
    new Chart(pizzaCtx, {
      type: 'pie',
      data: {
        labels: <?= json_encode(array_column($frequencias, 'humor')) ?>,
        datasets: [{
          data: <?= json_encode(array_column($frequencias, 'total')) ?>,
          backgroundColor: [
            '#7357C0', '#8F6FE5', '#C194ED', '#544E7E', '#423C52'
          ]
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

    // Gráfico de Linha com datas formatadas
    const linhaCtx = document.getElementById('graficoLinha').getContext('2d');
    new Chart(linhaCtx, {
      type: 'line',
      data: {
        labels: <?= json_encode($datas_formatadas) ?>,
        datasets: [{
          label: 'Humor',
          data: <?= json_encode($valores_numericos) ?>,
          borderColor: '#7357C0',
          backgroundColor: 'rgba(115, 87, 192, 0.1)',
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointBackgroundColor: '#7357C0'
        }]
      },
      options: {
        scales: {
          y: {
            min: 0,
            max: 4,
            ticks: {
              stepSize: 1,
              callback: function(value) {
                const mapa = ['😢 Triste', '😰 Ansioso', '😠 Irritado', '😌 Calmo', '😊 Feliz'];
                return mapa[value] ?? value;
              },
              color: '#423C52',
              font: { size: 12 }
            },
            grid: {
              color: '#E5E5F5'
            }
          },
          x: {
            ticks: {
              color: '#423C52',
              font: { size: 12 }
            },
            grid: {
              display: false
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  </script>
</body>
</html>
