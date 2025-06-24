<?php
include('auth.php');
require_once('db.php');

// ZenQuotes API
$frase = "Seja bem-vindo!";
$autor = "";

try {
    $response = file_get_contents("https://zenquotes.io/api/random");
    $data = json_decode($response, true);
    if (isset($data[0])) {
        $frase = $data[0]['q'];
    }
} catch (Exception $e) {
    $frase = "Frase indisponível no momento.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Moodr - Dashboard</title>
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

<body class="bg-white-primary text-gray-900 min-h-screen p-6">

    <!-- HEADER -->
    <header class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold">Olá, <?= $_SESSION['nome'] ?> 👋</h1>
        </div>
        <nav class="flex gap-4">
            <a href="dashboard.php" class="px-4 py-2 rounded-full border border-purple-medium text-purple-primary font-medium hover:bg-purple-medium hover:text-white transition">Dashboard</a>
            <a href="calendar.php" class="px-4 py-2 rounded-full border border-gray-light hover:bg-gray-light">Calendário</a>
            <a href="analytics.php" class="px-4 py-2 rounded-full border border-gray-light hover:bg-gray-light">Gráficos</a>
            <a href="profile.php" class="px-4 py-2 rounded-full border border-gray-light hover:bg-gray-light">Perfil</a>
            <a href="logout.php" class="px-4 py-2 rounded-full border border-red-negative text-red-negative hover:bg-red-negative hover:text-white transition">Sair</a>
        </nav>
    </header>

    <!-- TOPO COM FRASE E BOTÃO -->
    <div class="flex justify-between items-center mb-8">
        <div class="bg-white border border-gray-light rounded-xl px-6 py-3 shadow-sm text-sm max-w-md">
            <?= $frase ?>
        </div>
        <a href="mood_register.php"
           class="bg-purple-primary hover:bg-purple-medium text-white px-5 py-2 rounded-full text-sm font-semibold transition">
           Registrar Humor
        </a>
    </div>

    <!-- GRID PRINCIPAL COM 4 CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- CARD CALENDÁRIO -->
        <div class="bg-white rounded-2xl border border-gray-light shadow-sm p-6 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold">Atividade de Humor</h2>
                <button class="px-3 py-1 border border-purple-medium rounded-full text-xs hover:bg-purple-medium hover:text-white">
                    Alterar Período
                </button>
            </div>
            <div class="flex gap-6 mb-4">
                <div class="text-center">
                    <p class="text-xl font-bold">32</p>
                    <p class="text-xs text-gray-primary">Feliz</p>
                </div>
                <div class="text-center">
                    <p class="text-xl font-bold">24</p>
                    <p class="text-xs text-gray-primary">Triste</p>
                </div>
                <div class="text-center">
                    <p class="text-xl font-bold">18</p>
                    <p class="text-xs text-gray-primary">Ansioso</p>
                </div>
            </div>
            <!-- Mini calendário simulado -->
            <div class="grid grid-cols-7 gap-2 text-center text-xs">
                <?php
                $dias = range(1, 31);
                $diasComRegistro = [2, 5, 9, 15, 18, 22, 27]; // Simulado
                foreach ($dias as $dia) {
                    $temRegistro = in_array($dia, $diasComRegistro);
                    echo '<div class="rounded-full py-2 ' . 
                        ($temRegistro ? 'bg-purple-primary text-white' : 'bg-gray-light text-gray-primary') . 
                        '">' . $dia . '</div>';
                }
                ?>
            </div>
        </div>

        <!-- CARD CHAT -->
        <div class="bg-white rounded-2xl border border-gray-light shadow-sm p-6 flex flex-col">
            <h2 class="font-semibold mb-4">Suporte Moodr</h2>
            <div id="chatBox" class="flex-1 mb-4 text-sm space-y-2 overflow-y-auto max-h-72">
                <div class="text-center text-gray-primary">Comece a conversar...</div>
            </div>
            <form id="chatForm" class="flex gap-2">
                <input type="text" id="userInput"
                    placeholder="Digite uma mensagem..."
                    class="flex-1 px-3 py-2 rounded-full bg-gray-light focus:outline-none text-sm" required>
                <button type="submit"
                    class="bg-purple-primary hover:bg-purple-medium text-white px-4 py-2 rounded-full text-sm transition">Enviar</button>
            </form>
        </div>

        <!-- CARD GRÁFICO HUMORES -->
        <div class="bg-white rounded-2xl border border-gray-light shadow-sm p-6 flex flex-col">
            <h2 class="font-semibold mb-4">Humores Mais Registrados</h2>
            <div class="flex flex-col gap-4">
                <div class="flex justify-between items-center">
                    <span>Feliz</span>
                    <div class="flex-1 mx-4 h-2 rounded-full bg-gray-light">
                        <div class="h-2 bg-green-positive rounded-full w-[70%]"></div>
                    </div>
                    <span>70%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span>Triste</span>
                    <div class="flex-1 mx-4 h-2 rounded-full bg-gray-light">
                        <div class="h-2 bg-red-negative rounded-full w-[40%]"></div>
                    </div>
                    <span>40%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span>Ansioso</span>
                    <div class="flex-1 mx-4 h-2 rounded-full bg-gray-light">
                        <div class="h-2 bg-purple-primary rounded-full w-[30%]"></div>
                    </div>
                    <span>30%</span>
                </div>
            </div>
        </div>

        <!-- CARD TOTAL DE REGISTROS -->
        <div class="bg-white rounded-2xl border border-gray-light shadow-sm p-6 flex flex-col justify-center items-center">
            <h2 class="font-semibold mb-4">Total de Registros</h2>
            <p class="text-5xl font-extrabold text-purple-primary">154</p>
            <p class="text-sm text-gray-primary">Registros de humor até agora</p>
        </div>
    </div>

    <!-- SCRIPT DO CHAT -->
    <script>
        const chatBox = document.getElementById('chatBox');

        document.getElementById('chatForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const userInput = document.getElementById('userInput').value.trim();
            if (!userInput) return;

            chatBox.innerHTML += `<div><strong class="text-purple-primary">Você:</strong> ${userInput}</div>`;
            chatBox.scrollTop = chatBox.scrollHeight;

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: userInput })
            })
                .then(response => response.json())
                .then(data => {
                    const reply = data.reply || data.error || 'Erro na resposta';
                    chatBox.innerHTML += `<div><strong class="text-green-positive">Suporte:</strong> ${reply}</div>`;
                    chatBox.scrollTop = chatBox.scrollHeight;
                })
                .catch(() => {
                    chatBox.innerHTML += `<div class="text-red-negative"><strong>Suporte:</strong> Erro na requisição</div>`;
                });

            document.getElementById('userInput').value = '';
        });
    </script>
</body>
</html>
