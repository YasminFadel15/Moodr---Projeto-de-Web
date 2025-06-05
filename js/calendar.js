// calendar.js
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
