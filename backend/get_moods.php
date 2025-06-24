<?php
require_once 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    http_response_code(401);
    echo json_encode(["error" => "Usuário não autenticado."]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT me.id, me.data, me.humor, me.anotacao, 
           GROUP_CONCAT(t.tag SEPARATOR ', ') AS tags
    FROM mood_entries me
    LEFT JOIN tags t ON me.id = t.mood_entry_id
    WHERE me.user_id = ?
    GROUP BY me.id
    ORDER BY me.data DESC
");
$stmt->execute([$user_id]);

$entries = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $date = $row['data'];

    if (!isset($entries[$date])) {
        $entries[$date] = [];
    }

    $entries[$date][] = [
        "id" => $row['id'],
        "humor" => $row['humor'],
        "anotacao" => $row['anotacao'],
        "tags" => $row['tags'] ? explode(',', $row['tags']) : []
    ];
}

header('Content-Type: application/json');
echo json_encode($entries);
?>
