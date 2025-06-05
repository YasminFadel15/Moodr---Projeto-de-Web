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
<html lang="pt-br" class="bg-white text-black dark:bg-gray-900 dark:text-white">
<head>
  <meta charset="UTF-8">
  <title>Registrar Humor - Moodr</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
  <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-lg">
    <h1 class="text-2xl font-bold mb-4 text-center">Registrar Humor</h1>
    
    <form method="POST" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Data</label>
        <input type="date" name="data" value="<?= date('Y-m-d') ?>" required class="w-full p-2 rounded border dark:bg-gray-700">
      </div>
      <div>
        <label class="block mb-1 font-medium">Humor</label>
        <div class="flex gap-2">
          <label><input type="radio" name="humor" value="feliz" required> 😊</label>
          <label><input type="radio" name="humor" value="triste"> 😢</label>
          <label><input type="radio" name="humor" value="ansioso"> 😰</label>
          <label><input type="radio" name="humor" value="irritado"> 😠</label>
          <label><input type="radio" name="humor" value="calmo"> 😌</label>
        </div>
      </div>
      <div>
        <label class="block mb-1 font-medium">Anotação (opcional)</label>
        <textarea name="anotacao" rows="3" class="w-full p-2 rounded border dark:bg-gray-700"></textarea>
      </div>
      <div>
        <label class="block mb-1 font-medium">Tags (separadas por vírgula)</label>
        <input type="text" name="tags" class="w-full p-2 rounded border dark:bg-gray-700" />
      </div>
      <div class="text-center">
        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition">Salvar</button>
      </div>
    </form>
  </div>
</body>
</html>
