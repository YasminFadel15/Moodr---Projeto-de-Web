<?php
require_once 'db.php';
session_start();

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT me.data, me.humor, me.anotacao, GROUP_CONCAT(t.tag SEPARATOR ', ') AS tags
                       FROM mood_entries me
                       LEFT JOIN tags t ON me.id = t.mood_entry_id
                       WHERE me.user_id = ?
                       GROUP BY me.id");
$stmt->execute([$user_id]);

$entries = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $entries[$row['data']] = [
        "humor" => $row['humor'],
        "anotacao" => $row['anotacao'],
        "tags" => $row['tags']
    ];
}

header('Content-Type: application/json');
echo json_encode($entries);
