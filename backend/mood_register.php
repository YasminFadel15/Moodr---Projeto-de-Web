<?php
include('auth.php');
require_once('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $data = $_POST['data'];
    $humor = $_POST['humor'];
    $anotacao = $_POST['anotacao'] ?? '';
    $tags_raw = $_POST['tags'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO mood_entries (user_id, data, humor, anotacao) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $data, $humor, $anotacao]);
    $mood_entry_id = $pdo->lastInsertId();

    if (!empty($tags_raw)) {
        $tags = explode(",", $tags_raw);
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if ($tag !== "") {
                $stmt = $pdo->prepare("INSERT INTO tags (mood_entry_id, tag) VALUES (?, ?)");
                $stmt->execute([$mood_entry_id, $tag]);
            }
        }
    }

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar Humor - Moodr</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

  <div class="bg-white rounded-2xl border border-gray-light shadow-xl w-full max-w-lg p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Registrar Humor</h1>

    <form method="POST" class="space-y-6">

      <!-- Data -->
      <div>
        <label class="block text-sm font-medium mb-1">Data</label>
        <input type="date" name="data" value="<?= date('Y-m-d') ?>" required
          class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary">
      </div>

<!-- Humor (fixos + personalizados) -->
<div>
  <label class="block text-sm font-medium mb-2">Como você está se sentindo?</label>
  <div id="humor-list" class="flex flex-wrap gap-3 mb-4"></div>

  <!-- Campo de humor personalizado + emoji -->
  <div class="flex gap-2 items-center mb-4">
    <input type="text" id="newHumor" placeholder="Novo humor (ex: inspirado)"
           class="flex-1 px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary" />
    <input type="text" id="newEmoji" placeholder="Emoji (ex: 💡)" maxlength="2"
           class="w-16 px-3 py-2 rounded-xl border border-gray-light bg-gray-light text-center focus:outline-purple-primary" />
    <button type="button" id="addHumor"
            class="bg-purple-primary text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-purple-medium transition">
      +
    </button>
  </div>

  <input type="hidden" name="humor" id="selectedHumor" required>
</div>


      <!-- Anotação -->
      <div>
        <label class="block text-sm font-medium mb-1">Anotação (opcional)</label>
        <textarea name="anotacao" rows="3"
          class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary"
          placeholder="Escreva algo sobre seu dia..."></textarea>
      </div>

      <!-- Tags -->
      <div>
        <label class="block text-sm font-medium mb-1">Tags (separadas por vírgula)</label>
        <input type="text" name="tags"
          class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary"
          placeholder="Ex: trabalho, família, estudos...">
      </div>

      <!-- Botão -->
      <div class="text-center">
        <button type="submit"
          class="bg-purple-primary hover:bg-purple-medium text-white px-6 py-3 rounded-full font-semibold transition">
          Salvar Registro
        </button>
      </div>
    </form>
  </div>

  <script>
document.addEventListener('DOMContentLoaded', async () => {
  const humorList = document.getElementById('humor-list');
  const selectedHumorInput = document.getElementById('selectedHumor');

  const fixos = [
    { nome: 'Felicidade', emoji: '😊' },
    { nome: 'Medo', emoji: '😱' },
    { nome: 'Tristeza', emoji: '😢' },
    { nome: 'Calmo', emoji: '😌' },
    { nome: 'Ansiedade', emoji: '😰' },
    { nome: 'Irritação', emoji: '😠' },
  ];

  let personalizados = [];

try {
  const customRes = await fetch('get_custom_moods.php');
  if (customRes.ok) {
    personalizados = await customRes.json();
  } else {
    console.warn('Erro ao buscar humores personalizados:', customRes.statusText);
  }
} catch (e) {
  console.warn('Erro de rede ao buscar humores personalizados:', e);
}

const allHumores = [...fixos, ...personalizados];
renderHumores(allHumores);


  function renderHumores(lista) {
  humorList.innerHTML = '';
  lista.forEach(h => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.textContent = `${h.emoji} ${h.nome}`;
    btn.className = 'px-4 py-2 rounded-full border border-gray-light bg-gray-light hover:bg-purple-light transition text-sm';
    btn.dataset.value = h.nome;
    btn.onclick = () => {
      document.querySelectorAll('#humor-list button').forEach(b => b.classList.remove('ring-2'));
      btn.classList.add('ring-2', 'ring-purple-primary');
      selectedHumorInput.value = h.nome;
    };
    humorList.appendChild(btn);
  });
}

  renderHumores(allHumores);

  // Adicionar novo humor personalizado
  document.getElementById('addHumor').addEventListener('click', async () => {
    const nome = document.getElementById('newHumor').value.trim().toLowerCase();
    const emoji = document.getElementById('newEmoji').value.trim();

    if (!nome || !emoji) {
      alert('Preencha nome e emoji.');
      return;
    }

    const res = await fetch('save_custom_mood.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ nome, emoji })
    });

    if (res.ok) {
      const novo = { nome, emoji };
      allHumores.push(novo);
      renderHumores(allHumores);
      document.getElementById('newHumor').value = '';
      document.getElementById('newEmoji').value = '';
    } else {
      const r = await res.json();
      alert(r.error || 'Erro ao salvar humor.');
    }
  });
});
</script>

</body>
</html>
