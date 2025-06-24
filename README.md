# Moodr

Moodr é uma aplicação web para registro de humor. Com ela, o usuário pode registrar seu humor, descrever como foi seu dia e acompanhar sua saúde emocional ao longo do tempo. A plataforma também conta com um bot de suporte emocional, integrado via API Gemini, que permite conversas para auxiliar na reflexão e no autoconhecimento. Todos os registros podem ser visualizados em formato de calendário e gráficos interativos. 

---

## Funcionalidades
📅 Registro diário de humor.

📊 Visualização de estatísticas e gráficos sobre seus humores.

🤖 Bot de suporte emocional com integração na API Gemini.

🗓️ Visualização dos registros em formato de calendário.

---

## Tecnologias utilizadas
⚙️ PHP 

🎨 HTML + CSS 

💨 Tailwind CSS 

🗄️ phpMyAdmin + MySQL 

---

## Pré-requisitos
- Xampp instalado.
- IDE (opcional) para visualização do código.


## Como rodar a aplicação

- Copie a URL deste repositório.
- Clone o projeto dentro da pasta htdocs do XAMPP.
- Inicie o Apache e o MySQL pelo painel do XAMPP.
- No navegador, acesse: http://localhost/moodr (ou o nome da pasta do projeto).
- Importe o arquivo do banco de dados (.sql) pelo phpMyAdmin.

#### Configuração do bot (Gemini)
- Acesse https://ai.google.dev/gemini-api e gere sua chave de API.
- No projeto, localize o arquivo config.php.
- Adicione sua chave na variável GEMINI_API_KEY da seguinte forma:

````
define('GEMINI_API_KEY', 'sua-chave-aqui');
````

---

## Licença
Este projeto é apenas para fins educacionais e não possui licença comercial.
