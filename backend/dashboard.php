<?php
include('auth.php');
require_once('db.php');

$user_id = $_SESSION['user_id'];

// Frase motivacional
$frase = "Seja bem-vindo!";
$autor = "";
try {
    $response = file_get_contents("https://zenquotes.io/api/random");
    $data = json_decode($response, true);
    if (isset($data[0])) {
        $frase = $data[0]['q'];
        $autor = $data[0]['a'] ?? '';
    }
} catch (Exception $e) {
    $frase = "Frase indisponível no momento.";
}

// Frequência dos humores
$stmt = $pdo->prepare("SELECT humor, COUNT(*) as total FROM mood_entries WHERE user_id = ? GROUP BY humor");
$stmt->execute([$user_id]);
$humores = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$total_registros = array_sum($humores);

// Pegando registros de humor com datas do mês atual
$currentMonth = date('Y-m');
$stmt = $pdo->prepare("SELECT id, DAY(data) as dia, humor FROM mood_entries WHERE user_id = ? AND DATE_FORMAT(data, '%Y-%m') = ?");
$stmt->execute([$user_id, $currentMonth]);
$registrosMes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$registrosPorDia = [];
foreach ($registrosMes as $r) {
    $registrosPorDia[$r['dia']] = [
        'id' => $r['id'],
        'humor' => $r['humor']
    ];
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Moodr - Dashboard</title>
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
            'gray-light': '#D1CFE5',
            'gray-primary': '#9695AB',
            'white-primary': '#F9F8FF',
            'red-negative': '#E64848',
            'green-positive': '#3FCF92',
          }
        }
      }
    }
  </script>
  <style> body { font-family: 'Manrope', sans-serif; } </style>
</head>

<body class="bg-white-primary text-gray-900 min-h-screen p-6">
  <!-- HEADER -->
  <header class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Olá, <?= $_SESSION['nome'] ?> 👋</h1>
    <nav class="flex gap-2 text-sm">
      <a href="dashboard.php" class="px-3 py-1.5 rounded-full border border-gray-light hover:bg-gray-light transition">Dashboard</a>
      <a href="calendar.php" class="px-3 py-1.5 rounded-full border border-gray-light hover:bg-gray-light transition">Calendário</a>
      <a href="analytics.php" class="px-3 py-1.5 rounded-full border border-gray-light hover:bg-gray-light transition">Gráficos</a>
      <a href="profile.php" class="px-3 py-1.5 rounded-full border border-purple-medium text-purple-primary font-medium hover:bg-purple-medium hover:text-white transition">Perfil</a>
      <a href="logout.php" class="px-3 py-1.5 rounded-full border border-red-negative text-red-negative hover:bg-red-negative hover:text-white transition">Sair</a>
    </nav>
  </header>

  <!-- FRASE MOTIVACIONAL -->
  <div class="flex justify-between items-center mb-8">
    <div class="bg-white border border-gray-light rounded-xl px-6 py-3 shadow-sm text-sm max-w-xl">
      “<?= $frase ?>”
      <?php if ($autor): ?><div class="mt-1 text-right text-xs text-gray-500">— <?= htmlspecialchars($autor) ?></div><?php endif; ?>
    </div>
    <a href="mood_register.php" class="bg-purple-primary hover:bg-purple-medium text-white px-5 py-2 rounded-full text-sm font-semibold transition">Registrar Humor</a>
  </div>

  <!-- GRID PRINCIPAL -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- ATIVIDADE DE HUMOR -->
    <div class="bg-white rounded-2xl border border-gray-light shadow-sm p-6 flex flex-col">
      <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold">Atividade de Humor</h2>
        <span class="px-3 py-1 border border-purple-medium rounded-full text-xs text-purple-primary">Mês Atual</span>
      </div>

      <!-- Mini calendário -->
      <div class="grid grid-cols-7 gap-2 text-center text-xs">
        <?php
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        for ($i = 1; $i <= $daysInMonth; $i++) {
            if (isset($registrosPorDia[$i])) {
                $humor = $registrosPorDia[$i]['humor'];
                $id = $registrosPorDia[$i]['id'];
                $bg = 'bg-purple-medium';
                echo "<a href='edit_mood.php?data=" . date("Y-m") . "-". str_pad($i, 2, '0', STR_PAD_LEFT) . "' class='rounded-xl py-2 px-2 text-white {$bg} hover:scale-105 transition cursor-pointer' title='{$humor}'>{$i}</a>";
            } else {
                echo "<div class='rounded-xl py-2 px-2 text-gray-400 bg-gray-light'>{$i}</div>";
            }
        }
        ?>
      </div>
    </div>

    <!-- SUPORTE -->
    <div class="bg-white rounded-2xl border border-gray-light shadow-sm p-6 flex flex-col">
      <h2 class="font-semibold mb-4">Suporte Moodr</h2>
      <div id="chatBox" class="flex-1 mb-4 text-sm space-y-2 overflow-y-auto max-h-72">
        <div class="text-center text-gray-primary">Comece a conversar...</div>
      </div>
      <form id="chatForm" class="flex gap-2">
        <input type="text" id="userInput" placeholder="Digite uma mensagem..." class="flex-1 px-3 py-2 rounded-full bg-gray-light focus:outline-none text-sm" required>
        <button type="submit" class="bg-purple-primary hover:bg-purple-medium text-white px-4 py-2 rounded-full text-sm transition">Enviar</button>
      </form>
    </div>

    <!-- HUMORES MAIS REGISTRADOS -->
    <div class="bg-white rounded-2xl border border-gray-light shadow-sm p-6">
      <h2 class="font-semibold mb-4">Humores Mais Registrados</h2>
      <div class="flex flex-col gap-4">
        <?php foreach ($humores as $humor => $count):
          $percent = $total_registros > 0 ? round(($count / $total_registros) * 100) : 0;
          echo "
          <div class='flex justify-between items-center text-sm'>
            <span>" . $humor . "</span>
            <div class='flex-1 mx-4 h-2 rounded-full bg-gray-light'>
              <div class='h-2 bg-purple-medium rounded-full' style='width: {$percent}%;'></div>
            </div>
            <span>{$percent}%</span>
          </div>";
        endforeach; ?>
      </div>
    </div>

    <!-- TOTAL -->
    <div class="bg-white rounded-2xl border border-gray-light shadow-sm p-6 flex flex-col justify-center items-center">
      <h2 class="font-semibold mb-4">Total de Registros</h2>
      <p class="text-5xl font-extrabold text-purple-primary"><?= $total_registros ?></p>
      <p class="text-sm text-gray-primary">Registros de humor até agora</p>
    </div>
  </div>

  <!-- SCRIPT DO CHAT -->
  <script>
    const chatBox = document.getElementById('chatBox');
    document.getElementById('chatForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const userInput = document.getElementById('userInput').value.trim();
      if (!userInput) return;

      chatBox.innerHTML += `<div><strong class="text-purple-primary">Você:</strong> ${userInput}</div>`;
      chatBox.scrollTop = chatBox.scrollHeight;

      fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: userInput })
      })
        .then(response => response.json())
        .then(data => {
          const reply = data.reply || data.error || 'Erro na resposta';
          chatBox.innerHTML += `<div><strong class="text-green-positive">Suporte:</strong> ${reply}</div>`;
          chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(() => {
          chatBox.innerHTML += `<div class="text-red-negative"><strong>Suporte:</strong> Erro na requisição</div>`;
        });

      document.getElementById('userInput').value = '';
    });
  </script>
</body>
</html>
