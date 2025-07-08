<?php
include('auth.php');
require_once('db.php');

$user_id = $_SESSION['user_id'];
$data = $_GET['data'] ?? null;

if (!$data) {
    die("Data inválida.");
}

$stmt = $pdo->prepare("SELECT id, humor, anotacao FROM mood_entries WHERE user_id = ? AND data = ?");
$stmt->execute([$user_id, $data]);
$registro = $stmt->fetch();

if (!$registro) {
    die("Registro não encontrado.");
}

$stmt = $pdo->prepare("SELECT tag FROM tags WHERE mood_entry_id = ?");
$stmt->execute([$registro['id']]);
$tags = array_column($stmt->fetchAll(), 'tag');
$tags_str = implode(', ', $tags);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Humor - Moodr</title>
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
    <a href="dashboard.php" class="inline-block mb-4 text-purple-primary hover:text-purple-medium transition">
      ← Voltar para o painel
    </a>

    <h1 class="text-3xl font-bold mb-6 text-center">Editar Registro de Humor</h1>

    <form method="POST" action="update_mood.php" class="space-y-6">
      <input type="hidden" name="id" value="<?= $registro['id'] ?>">
      <input type="hidden" name="data" value="<?= htmlspecialchars($data) ?>">

      <!-- Data -->
      <div>
        <label class="block text-sm font-medium mb-1">Data</label>
        <input type="text" value="<?= htmlspecialchars($data) ?>" disabled
          class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary">
      </div>

      <!-- Humor -->
      <div>
        <label class="block text-sm font-medium mb-2">Como você está se sentindo?</label>
        <div id="humor-list" class="flex flex-wrap gap-3 mb-4"></div>
        <input type="hidden" name="humor" id="selectedHumor" value="<?= htmlspecialchars($registro['humor']) ?>" required>
      </div>

      <!-- Anotação -->
      <div>
        <label class="block text-sm font-medium mb-1">Anotação (opcional)</label>
        <textarea name="anotacao" rows="3"
          class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary"
          placeholder="Escreva algo sobre seu dia..."><?= htmlspecialchars($registro['anotacao']) ?></textarea>
      </div>

      <!-- Tags -->
      <div>
        <label class="block text-sm font-medium mb-1">Tags (separadas por vírgula)</label>
        <input type="text" name="tags" value="<?= htmlspecialchars($tags_str) ?>"
          class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary"
          placeholder="Ex: trabalho, família, estudos...">
      </div>

      <!-- Botoes -->
      <div class="text-center space-y-2">
        <button type="submit"
          class="bg-purple-primary hover:bg-purple-medium text-white px-6 py-3 rounded-full font-semibold transition">
          Salvar Alterações
        </button>
        <br>
        <a href="delete_mood.php?id=<?= $registro['id'] ?>" onclick="return confirm('Deseja realmente excluir este registro?')"
          class="inline-block px-4 py-2 rounded-full border border-red-negative text-red-negative hover:bg-red-negative hover:text-white transition">
          🗑️ Excluir Registro
        </a>
      </div>
    </form>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', async () => {
    const humorList = document.getElementById('humor-list');
    const selectedHumorInput = document.getElementById('selectedHumor');
    const humorAtual = selectedHumorInput.value.toLowerCase();

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
        if (h.nome.toLowerCase() === humorAtual) {
          btn.classList.add('ring-2', 'ring-purple-primary');
          selectedHumorInput.value = h.nome;
        }
        btn.onclick = () => {
          document.querySelectorAll('#humor-list button').forEach(b => b.classList.remove('ring-2', 'ring-purple-primary'));
          btn.classList.add('ring-2', 'ring-purple-primary');
          selectedHumorInput.value = h.nome;
        };
        humorList.appendChild(btn);
      });
    }
  });
  </script>
</body>
</html>
