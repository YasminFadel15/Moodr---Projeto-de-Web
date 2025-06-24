<?php
session_start();
header('Content-Type: application/json');

include 'config.php';

$contexto = 'Você é um assistente de bem-estar social. Seu objetivo é ajudar com questões de saúde mental, apoio emocional e orientação social. Sempre responda com empatia, acolhimento e com foco no bem-estar da pessoa. Evite diagnósticos médicos, mas forneça apoio emocional.';

// Função que faz requisição cURL para a Gemini API
function callGeminiAPI($messages) {
    $url = GEMINI_API_URL;

    $postData = [
        "contents" => $messages
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return ['error' => curl_error($ch)];
    }

    curl_close($ch);

    return json_decode($response, true);
}

// Recupera a mensagem do usuário
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

if (!$userMessage) {
    echo json_encode(['error' => 'Mensagem vazia']);
    exit;
}

// Recupera ou inicializa o histórico da sessão
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [
        [
            'role' => 'user',  
            'parts' => [[
                'text' => $contexto
            ]]
        ]
    ];
}

// Adiciona a nova pergunta do usuário
$_SESSION['chat_history'][] = [
    'role' => 'user',
    'parts' => [['text' => $userMessage]]
];

// Faz a chamada para a API Gemini com o histórico completo
$response = callGeminiAPI($_SESSION['chat_history']);

if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
    $botReply = $response['candidates'][0]['content']['parts'][0]['text'];

    // Salva a resposta do bot no histórico
    $_SESSION['chat_history'][] = [
        'role' => 'model',
        'parts' => [['text' => $botReply]]
    ];

    echo json_encode(['reply' => $botReply]);
} else {
    echo json_encode(['error' => 'Erro ao obter resposta da API.']);
}
?>
