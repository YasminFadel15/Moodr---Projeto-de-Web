<?php include('auth.php'); ?>
<!DOCTYPE html>
<html lang="pt-br" class="bg-white text-black dark:bg-gray-900 dark:text-white">
<head>
  <meta charset="UTF-8">
  <title>Calendário de Humor - Moodr</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen p-6">
  <header class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold">📅 Calendário de Humor</h1>
    <a href="dashboard.php" class="text-purple-600 hover:underline">← Voltar</a>
  </header>

  <div id="calendar" class="grid grid-cols-7 gap-2"></div>

  <div id="entry-modal" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow w-96">
      <h2 class="text-xl font-bold mb-2">Detalhes do Humor</h2>
      <p id="modal-date" class="font-semibold"></p>
      <p id="modal-humor" class="mt-2"></p>
      <p id="modal-anotacao" class="mt-1 text-sm italic text-gray-600 dark:text-gray-400"></p>
      <p id="modal-tags" class="mt-2 text-sm"></p>
      <button onclick="closeModal()" class="mt-4 bg-purple-600 text-white px-3 py-1 rounded">Fechar</button>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", async () => {
      const calendarEl = document.getElementById("calendar");
      const today = new Date();
      const year = today.getFullYear();
      const month = today.getMonth();
      const diasNoMes = new Date(year, month + 1, 0).getDate();
      const primeiroDia = new Date(year, month, 1).getDay();

      const res = await fetch("get_moods.php");
      const data = await res.json();

      for (let i = 0; i < primeiroDia; i++) {
        calendarEl.innerHTML += `<div></div>`;
      }

      for (let dia = 1; dia <= diasNoMes; dia++) {
        const dataStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${dia.toString().padStart(2, '0')}`;
        const entry = data[dataStr];
        const bgColor = entry ? humorColor(entry.humor) : "bg-gray-200 dark:bg-gray-700";
        calendarEl.innerHTML += `
          <div class="p-2 text-center rounded cursor-pointer ${bgColor}" onclick="openModal('${dataStr}')">
            ${dia}
          </div>`;
      }

      window.openModal = (date) => {
        const entry = data[date];
        if (!entry) return;
        document.getElementById("modal-date").innerText = date;
        document.getElementById("modal-humor").innerText = "Humor: " + entry.humor;
        document.getElementById("modal-anotacao").innerText = "Anotação: " + (entry.anotacao || "—");
        document.getElementById("modal-tags").innerHTML = "Tags: " + (entry.tags || "—") + `<br><a href="edit_mood.php?data=${date}" class="text-purple-600 underline">Editar</a>`;
        document.getElementById("entry-modal").classList.remove("hidden");
      };

      window.closeModal = () => {
        document.getElementById("entry-modal").classList.add("hidden");
      };

      function humorColor(humor) {
        const map = {
          feliz: "bg-green-300 dark:bg-green-600",
          triste: "bg-blue-300 dark:bg-blue-600",
          ansioso: "bg-yellow-300 dark:bg-yellow-600",
          irritado: "bg-red-300 dark:bg-red-600",
          calmo: "bg-purple-300 dark:bg-purple-600"
        };
        return map[humor] || "bg-gray-300";
      }
    });
  </script>
</body>
</html>
