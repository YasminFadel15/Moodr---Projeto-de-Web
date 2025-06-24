<?php
include('auth.php');
require_once('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $data = $_POST['data'];
    $humor = $_POST['humor'] === 'custom' ? $_POST['custom_humor'] : $_POST['humor'];
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

      <!-- Humor -->
      <div>
        <label class="block text-sm font-medium mb-2">Como você está se sentindo?</label>
        <div class="flex flex-wrap gap-3">
          <?php
          $humores = [
            'feliz' => '😊',
            'triste' => '😢',
            'ansioso' => '😰',
            'irritado' => '😠',
            'calmo' => '😌'
          ];
          foreach ($humores as $key => $emoji) {
            echo '<label class="flex items-center gap-2 px-4 py-2 border border-gray-light rounded-full cursor-pointer hover:bg-gray-light">
                  <input type="radio" name="humor" value="' . $key . '" class="hidden">
                  <span>' . $emoji . '</span> <span class="capitalize">' . $key . '</span>
                  </label>';
          }
          ?>
          <!-- Opção de humor personalizado -->
          <label class="flex items-center gap-2 px-4 py-2 border border-gray-light rounded-full cursor-pointer hover:bg-gray-light">
            <input type="radio" name="humor" value="custom" class="hidden">
            <span>✨</span> <span>Personalizado</span>
          </label>
        </div>

        <!-- Campo para humor personalizado -->
        <div class="mt-3">
          <input type="text" name="custom_humor" placeholder="Digite sua emoção"
            class="w-full px-4 py-2 rounded-xl border border-gray-light bg-gray-light focus:outline-purple-primary">
        </div>
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

</body>
</html>
