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

<body class="bg-white-primary text-gray-900 min-h-screen p-6">

  <header class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold">📅 Calendário de Humor</h1>
    <a href="dashboard.php" class="text-purple-primary hover:underline">← Voltar</a>
  </header>

  <!-- Calendário -->
  <div id="calendar" class="grid grid-cols-7 gap-3 bg-white border border-gray-light rounded-2xl p-6 shadow-sm"></div>

  <!-- Modal -->
  <div id="entry-modal" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md text-sm">
      <h2 class="text-xl font-bold mb-3">Detalhes do Dia</h2>
      <div id="modal-date" class="mb-2 text-purple-primary font-semibold"></div>
      <div id="modal-content"></div>
      <div class="mt-4 text-right">
        <button onclick="closeModal()" class="bg-purple-primary hover:bg-purple-medium text-white px-4 py-2 rounded-full text-sm">Fechar</button>
      </div>
    </div>
  </div>

  <!-- Script principal -->
 <script>
document.addEventListener("DOMContentLoaded", async () => {
  const calendarEl = document.getElementById("calendar");
  const today = new Date();
  const year = today.getFullYear();
  const month = today.getMonth();
  const diasNoMes = new Date(year, month + 1, 0).getDate();
  const primeiroDia = new Date(year, month, 1).getDay();

  const [moodsRes, customsRes] = await Promise.all([
    fetch("get_moods.php"),
    fetch("get_custom_moods.php")
  ]);

  const data = await moodsRes.json();
  const customMoods = await customsRes.json();

  // Emojis fixos
  const humorEmojis = {
    feliz: "😊",
    triste: "😢",
    ansioso: "😰",
    irritado: "😠",
    calmo: "😌"
  };

  // Adiciona humores personalizados
  customMoods.forEach(item => {
    humorEmojis[item.nome.trim().toLowerCase()] = item.emoji;
  });

  // Renderizar calendário
  for (let i = 0; i < primeiroDia; i++) {
    calendarEl.innerHTML += `<div></div>`;
  }

  for (let dia = 1; dia <= diasNoMes; dia++) {
    const dataStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
    const registros = data[dataStr];

    let html = `<div class="rounded-xl p-2 text-center border border-gray-light shadow-sm cursor-pointer transition hover:scale-105" onclick="openModal('${dataStr}')`;

    if (registros && registros.length > 0) {
      const humor = registros[0].humor.toLowerCase();
      const emoji = humorEmojis[humor] || "🔘";
      html += `">
                <div class="text-xl">${emoji}</div>
                <div class="text-xs mt-1 font-semibold">${dia}</div>
              </div>`;
    } else {
      html += `"><div class="text-gray-400">${dia}</div></div>`;
    }

    calendarEl.innerHTML += html;
  }

  // Modal com múltiplos registros
  window.openModal = (date) => {
    const registros = data[date];
    if (!registros) return;

    document.getElementById("modal-date").innerText = `📅 ${date}`;
    const content = registros.map(reg => `
      <div class="mb-4 border-b border-gray-light pb-2">
        <div><strong>Humor:</strong> ${reg.humor}</div>
        <div><strong>Anotação:</strong> ${reg.anotacao || "—"}</div>
        <div><strong>Tags:</strong> ${reg.tags.length ? reg.tags.join(', ') : "—"}</div>
        <div class="mt-2 space-x-3">
          <a href="edit_mood.php?data=${date}" class="text-purple-primary underline text-sm">✏️ Editar</a>
          <a href="delete_mood.php?id=${reg.id}" class="text-red-negative underline text-sm" onclick="return confirm('Tem certeza que deseja excluir este registro?')">🗑️ Excluir</a>
        </div>
      </div>
    `).join('');

    document.getElementById("modal-content").innerHTML = content;
    document.getElementById("entry-modal").classList.remove("hidden");
  };

  window.closeModal = () => {
    document.getElementById("entry-modal").classList.add("hidden");
  };
});
</script>

</body>
</html>
