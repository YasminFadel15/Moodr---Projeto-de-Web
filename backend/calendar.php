<?php include('auth.php'); ?>
<?php
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    http_response_code(401);
    echo "<p class='text-red-500'>Usuário não autenticado.</p>";
    exit;
}

$stmt = $pdo->prepare("SELECT data, humor, anotacao FROM mood_entries WHERE user_id = ?");
$stmt->execute([$user_id]);
$moods = $stmt->fetchAll(PDO::FETCH_ASSOC);

$entriesByDate = [];
foreach ($moods as $entry) {
    $entriesByDate[$entry['data']][] = $entry;
}

// Busca os custom moods para o usuário atual
$stmtCustom = $pdo->prepare("SELECT nome, emoji FROM custom_moods WHERE user_id = ?");
$stmtCustom->execute([$user_id]);
$customMoods = $stmtCustom->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Calendário de Humor - Moodr</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'purple-primary': '#7357C0',
            'purple-medium': '#8F6FE5',
            'purple-light': '#EDE7F6',
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
    #modal-humor-cards {
      max-height: 200px;
      overflow-y: auto;
    }
  </style>
</head>
<body class="bg-white-primary min-h-screen text-gray-900 p-6">
  <!-- MENU SUPERIOR -->
  <header class="flex justify-between items-center mb-8">
    <h1 class="text-xl font-bold">Calendário de Humor</h1>
    <nav class="flex gap-2 text-sm">
      <a href="dashboard.php" class="px-3 py-1 rounded-full border border-gray-light hover:bg-gray-light">Dashboard</a>
      <a href="calendar.php" class="px-3 py-1 rounded-full border border-purple-medium text-purple-primary font-medium hover:bg-purple-medium hover:text-white transition">Calendário</a>
      <a href="analytics.php" class="px-3 py-1 rounded-full border border-gray-light hover:bg-gray-light">Gráficos</a>
      <a href="profile.php" class="px-3 py-1 rounded-full border border-gray-light hover:bg-gray-light">Perfil</a>
      <a href="logout.php" class="px-3 py-1 rounded-full border border-red-negative text-red-negative hover:bg-red-negative hover:text-white transition">Sair</a>
    </nav>
  </header>

  <!-- CONTROLES DO MÊS -->
  <div class="flex justify-center items-center mb-6 gap-4">
    <button id="prev" class="text-purple-primary hover:text-purple-medium text-2xl font-bold">←</button>
    <h2 id="monthLabel" class="text-lg font-semibold"></h2>
    <button id="next" class="text-purple-primary hover:text-purple-medium text-2xl font-bold">→</button>
  </div>

  <!-- NOMES DOS DIAS DA SEMANA -->
  <div class="grid grid-cols-7 gap-2 w-full max-w-5xl mx-auto text-xs font-semibold text-center text-purple-primary mb-1">
    <div>Dom</div>
    <div>Seg</div>
    <div>Ter</div>
    <div>Qua</div>
    <div>Qui</div>
    <div>Sex</div>
    <div>Sáb</div>
  </div>

  <!-- CALENDÁRIO -->
  <div class="grid grid-cols-7 gap-2 w-full max-w-5xl mx-auto text-sm" id="calendar"></div>

  <!-- MODAL -->
  <div id="entry-modal" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
      <div class="flex justify-between items-center mb-2">
        <h2 class="text-lg font-bold">Registro do Dia</h2>
        <div id="modal-date" class="text-purple-primary text-sm"></div>
      </div>
      <div id="modal-humor-cards" class="space-y-2"></div>
      <div class="mt-4 text-right">
        <button onclick="closeModal()" class="bg-purple-primary text-white px-4 py-1 rounded-full text-sm hover:bg-purple-medium transition">Fechar</button>
      </div>
    </div>
  </div>

  <script>
    const moodEntries = <?php echo json_encode($entriesByDate); ?>;
    const calendar = document.getElementById("calendar");
    const monthLabel = document.getElementById("monthLabel");

    const customEmojis = <?php echo json_encode($customMoods, JSON_UNESCAPED_UNICODE); ?>;

    const customEmojisMap = {};
    customEmojis.forEach(mood => {
      customEmojisMap[mood.nome.trim().toLowerCase()] = mood.emoji;
    });

    const emojis = {
      felicidade: "😊",
      tristeza: "😢",
      ansiedade: "😰",
      irritação: "😠",
      calmo: "😌",
      medo: "😱",
      normal: "😐",
      ...customEmojisMap 
    };

    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();

    function renderCalendar(month, year) {
      calendar.innerHTML = "";
      const diasNoMes = new Date(year, month + 1, 0).getDate();
      const primeiroDia = new Date(year, month, 1).getDay();

      const hoje = new Date();
      const todayStr = `${hoje.getFullYear()}-${String(hoje.getMonth() + 1).padStart(2, '0')}-${String(hoje.getDate()).padStart(2, '0')}`;

      const meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
      monthLabel.innerText = `${meses[month]} ${year}`;

      for (let i = 0; i < primeiroDia; i++) {
        calendar.innerHTML += `<div class='aspect-square rounded-xl border border-transparent'></div>`;
      }

      for (let dia = 1; dia <= diasNoMes; dia++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
        const entries = moodEntries[dateStr] || [];

        let extraClass = "";
        if (dateStr === todayStr) {
          extraClass = "bg-purple-light text-purple-primary font-bold";
        }

        let emojiDisplay = entries.slice(0, 3).map(e => emojis[e.humor.trim().toLowerCase()] || "🔘").join(" ");

        let cell;
        if (entries.length > 0) {
          cell = `<div onclick=\"openModal('${dateStr}')\" class=\"aspect-square flex flex-col justify-center items-center border-2 border-purple-primary rounded-xl text-center shadow hover:scale-105 transition cursor-pointer ${extraClass}\">
                    <div class='text-xl'>${emojiDisplay}</div>
                    <div class='text-xs font-semibold mt-1'>${dia}</div>
                  </div>`;
        } else {
          cell = `<div class=\"aspect-square flex justify-center items-center border border-gray-light rounded-xl text-xs text-gray-400 ${extraClass}\">${dia}</div>`;
        }
        calendar.innerHTML += cell;
      }
    }

    function openModal(date) {
      const entries = moodEntries[date];
      if (!entries) return;
      const [y, m, d] = date.split("-");
      const formatDate = `${d}/${m}/${y}`;
      document.getElementById("modal-date").innerText = formatDate;

      const container = document.getElementById("modal-humor-cards");
      container.innerHTML = "";
      entries.forEach(e => {
        const emoji = emojis[e.humor.trim().toLowerCase()] || "🔘";
        const anotacao = e.anotacao || "—";
        container.innerHTML += `<div class='border border-purple-light rounded-lg p-3 shadow-sm'>
          <div class='font-semibold mb-1'>${emoji} ${e.humor}</div>
          <div class='text-sm text-gray-700'>${anotacao}</div>
        </div>`;
      });

      document.getElementById("entry-modal").classList.remove("hidden");
    }

    function closeModal() {
      document.getElementById("entry-modal").classList.add("hidden");
    }

    document.getElementById("prev").onclick = () => {
      currentMonth--;
      if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
      }
      renderCalendar(currentMonth, currentYear);
    };

    document.getElementById("next").onclick = () => {
      currentMonth++;
      if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
      }
      renderCalendar(currentMonth, currentYear);
    };

    renderCalendar(currentMonth, currentYear);
  </script>
</body>
</html>
