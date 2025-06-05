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
<html lang="pt-br" class="bg-white text-black dark:bg-gray-900 dark:text-white">
<head>
  <meta charset="UTF-8">
  <title>Editar Humor - Moodr</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
  <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-lg">
    <h1 class="text-2xl font-bold mb-4 text-center">Editar Registro de Humor</h1>
    
    <form method="POST" action="update_mood.php" class="space-y-4">
      <input type="hidden" name="id" value="<?= $registro['id'] ?>">
      <input type="hidden" name="data" value="<?= htmlspecialchars($data) ?>">

      <div>
        <label class="block mb-1 font-medium">Data</label>
        <input type="text" value="<?= htmlspecialchars($data) ?>" disabled class="w-full p-2 rounded border dark:bg-gray-700" />
      </div>
      <div>
        <label class="block mb-1 font-medium">Humor</label>
        <div class="flex gap-2">
          <?php
          $emocoes = ['feliz' => '😊', 'triste' => '😢', 'ansioso' => '😰', 'irritado' => '😠', 'calmo' => '😌'];
          foreach ($emocoes as $valor => $emoji): ?>
            <label>
              <input type="radio" name="humor" value="<?= $valor ?>" <?= $registro['humor'] === $valor ? 'checked' : '' ?>> <?= $emoji ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div>
        <label class="block mb-1 font-medium">Anotação</label>
        <textarea name="anotacao" rows="3" class="w-full p-2 rounded border dark:bg-gray-700"><?= htmlspecialchars($registro['anotacao']) ?></textarea>
      </div>
      <div>
        <label class="block mb-1 font-medium">Tags</label>
        <input type="text" name="tags" value="<?= htmlspecialchars($tags_str) ?>" class="w-full p-2 rounded border dark:bg-gray-700">
      </div>
      <div class="text-center space-y-2">
        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition">Salvar Alterações</button><br>
        <a href="delete_mood.php?id=<?= $registro['id'] ?>" onclick="return confirm('Deseja realmente excluir este registro?')" class="text-red-600 hover:underline">🗑️ Excluir Registro</a>
      </div>
    </form>
  </div>
</body>
</html>
