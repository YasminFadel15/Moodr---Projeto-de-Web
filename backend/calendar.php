<?php include('auth.php'); ?>
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
            'purple-light': '#C194ED',
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
  <div class="flex justify-center items-center mb-4 gap-4">
    <button id="prev" class="text-purple-primary hover:text-purple-medium text-xl">←</button>
    <h2 id="monthLabel" class="text-lg font-semibold"></h2>
    <button id="next" class="text-purple-primary hover:text-purple-medium text-xl">→</button>
  </div>

  <!-- CALENDÁRIO -->
  <div class="grid grid-cols-7 gap-2 w-full max-w-3xl mx-auto text-sm" id="calendar"></div>

  <!-- MODAL -->
  <div id="entry-modal" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
      <h2 class="text-lg font-bold mb-2">Registro do Dia</h2>
      <div id="modal-date" class="text-purple-primary mb-2"></div>
      <div id="modal-content"></div>
      <div class="mt-4 text-right">
        <button onclick="closeModal()" class="bg-purple-primary text-white px-4 py-1 rounded-full text-sm hover:bg-purple-medium transition">Fechar</button>
      </div>
    </div>
  </div>

  <script>
    const calendarEl = document.getElementById("calendar");
    const monthLabel = document.getElementById("monthLabel");
    const hoje = new Date();
    let currentMonth = hoje.getMonth();
    let currentYear = hoje.getFullYear();

    const humorEmojis = {
      felicidade: "😊",
      tristeza: "😢",
      ansiedade: "😰",
      irritação: "😠",
      calmo: "😌",
      medo: "😱",
      normal: "😐"
    };

    let moodData = {};
    let customMoods = [];

    async function fetchData() {
      const [moods, customs] = await Promise.all([
        fetch("get_moods.php").then(res => res.json()),
        fetch("get_custom_moods.php").then(res => res.json())
      ]);

      moodData = moods;
      customMoods = customs;

      // Adiciona os emojis personalizados
      customMoods.forEach(c => {
        const nome = c.nome.trim().toLowerCase();
        humorEmojis[nome] = c.emoji;
      });

      renderCalendar(currentMonth, currentYear);
    }

    function renderCalendar(month, year) {
      calendarEl.innerHTML = "";
      const diasNoMes = new Date(year, month + 1, 0).getDate();
      const primeiroDia = new Date(year, month, 1).getDay();

      const meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
      monthLabel.innerText = `${meses[month]} ${year}`;

      for (let i = 0; i < primeiroDia; i++) {
        calendarEl.innerHTML += `<div></div>`;
      }

      for (let dia = 1; dia <= diasNoMes; dia++) {
        const dataStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
        const registros = moodData[dataStr];
        const cores = ["purple-light", "purple-medium", "purple-primary"];
        let html = "";

        if (registros) {
          const humor = registros[0].humor.trim().toLowerCase();
          const emoji = humorEmojis[humor] || "🔘";
          const bg = cores[Math.floor(Math.random() * cores.length)];

          html = `
            <div onclick="openModal('${dataStr}')" 
              class="rounded-xl bg-${bg} text-white p-3 text-center font-medium shadow cursor-pointer transition hover:scale-105">
              <div class="text-2xl">${emoji}</div>
              <div class="text-xs">${dia}</div>
            </div>
          `;
        } else {
          html = `
            <div class="rounded-xl border border-gray-light p-3 text-center text-xs text-gray-400">
              ${dia}
            </div>
          `;
        }

        calendarEl.innerHTML += html;
      }
    }

    function openModal(date) {
      const registros = moodData[date];
      if (!registros) return;

      document.getElementById("modal-date").innerText = date;
      const html = registros.map(reg => `
        <div class="mb-4 text-sm border-b pb-2 border-gray-light">
          <div><strong>Humor:</strong> ${reg.humor}</div>
          <div><strong>Anotação:</strong> ${reg.anotacao || "—"}</div>
          <div><strong>Tags:</strong> ${reg.tags.length ? reg.tags.join(', ') : "—"}</div>
          <a href="edit_mood.php?data=${date}" class="text-purple-primary text-xs underline">✏️ Editar</a>
        </div>
      `).join('');
      document.getElementById("modal-content").innerHTML = html;
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
      fetchData();
    };

    document.getElementById("next").onclick = () => {
      currentMonth++;
      if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
      }
      fetchData();
    };

    fetchData();
  </script>
</body>
</html>
