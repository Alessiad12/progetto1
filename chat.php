<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])) {
    header('Location: login.php');
    exit;
}

$utente = $_SESSION['id_utente'];
$viaggio_id = $_GET['viaggio_id'];


$query = "SELECT n.*, p.nome AS mittente_nome, p.immagine_profilo AS immagine_mittente 
          FROM chat_viaggio n
          JOIN profili p ON n.utente_id = p.id 
          WHERE n.viaggio_id = $1 
          ORDER BY n.data_creazione";

$res = pg_query_params($dbconn, $query, [$viaggio_id]);
$messaggi = [];
while ($row = pg_fetch_assoc($res)) {
    $messaggi[] = $row;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Chat Viaggio</title>
  <style>
    body {
      font-family:"Inter", sans-serif;
      background-color: #f2f4f8;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .chat-container {
      width: 90%;
      max-width: 600px;
      height: 80vh;
      background: #FFFFFF ;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    .chat-messages {
      padding: 1rem;
      gap: 0.5 rem;
      display: flex;
      flex-direction: column;
      scroll-behavior: smooth;
      flex: 1;
      padding: 10px;
      overflow-y: auto;
      border-bottom: 1px solid #ccc;
    }

    .messaggio {
      display: flex;
      align-items: fflex-start;
      margin-bottom: 10px;
    }

    .messaggio img.avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }

    .testo-messaggio {
      margin-left: 10px;
      background-color: #f1f3f5;
      color:333;
      margin-left: 0.5rem;
      padding: 8px 12px;
      border-top-left-radius: 0;
    }

    .proprio {
      align-items: flex-end
      display: flex;
      justify-content: flex-end;
    }

    .proprio .testo-messaggio {
      background-color: #d1e7ff;
      color:#0B3175;
      margin-right: o.5rem;
      border-top-right-radius: 0;
    }

          
      /* 4) Scrollbar custom */
      .chat-messages::-webkit-scrollbar {
        width: 6px;
      }
      .chat-messages::-webkit-scrollbar-track {
        background: transparent;
      }
      .chat-messages::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.2);
        border-radius: 3px;
      }

    .chat-input {
      display: flex;
      padding: 10px;
      border-top: 1px solid #ccc;
    }

    .chat-input input {
      flex: 1;
      padding: 10px;
     
      border-radius: 5px;
    }

    .chat-input button {
      margin-left: 10px;
      padding: 10px;
      background-color: #007BFF;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .chat-input button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

<div class="chat-container">
  <div id="chat-container" class="chat-messages">
    <?php if (empty($messaggi)): ?>
      <p>Inizia la chat.</p>
    <?php else: ?>
      <?php foreach ($messaggi as $messaggio): 
        $is_own = $messaggio['utente_id'] == $utente;
        $classe = $is_own ? 'proprio messaggio' : 'messaggio';
        $img = !empty($messaggio['immagine_mittente']) ? htmlspecialchars($messaggio['immagine_mittente']) : 'immagini/default.png';
        $nome = htmlspecialchars($messaggio['mittente_nome']);
        $testo = htmlspecialchars($messaggio['messaggio']);
      ?>
        <div class="<?= $classe ?>">
          <?php if (!$is_own): ?>
            <img src="<?= $img ?>" alt="Profilo" class="avatar">
          <?php endif; ?>
          <div class="testo-messaggio"><strong><?= $nome ?></strong>: <?= $testo ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="chat-input">
    <input type="text" id="messageInput" placeholder="Scrivi un messaggio...">
    <button onclick="sendMessage(<?= $viaggio_id ?>, <?= $utente ?>)">Invia</button>
  </div>
</div>

<script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
<script>
  const socket = io('http://localhost:4000'); // Cambia URL se in produzione

  function sendMessage(viaggioId, mittenteId) {
    const input = document.getElementById('messageInput');
    const msg = input.value.trim();
    if (msg === '') return;

    socket.emit('sendMessage', {
      viaggio_id: viaggioId,
      mittente_id: mittenteId,
      messaggio: msg
    });

    input.value = '';
  }
  socket.on('newMessage', (data) => {

    const chat = document.getElementById('chat-container');
    const div = document.createElement('div');
    div.className = (data.mittente_id == <?= $utente ?>) ? 'proprio messaggio' : 'messaggio';

    div.innerHTML = `
      <div class="testo-messaggio">
        <strong>Utente ${data.mittente_id}</strong>: ${data.messaggio}
      </div>
    `;
    chat.appendChild(div);
    chat.scrollTop = chat.scrollHeight;
  });
  socket.on('newMessage', () => {
  const chat = document.getElementById('chat-container');
  const scrollPos = chat.scrollHeight;

  localStorage.setItem('chatScrollPos', scrollPos);

  location.reload();
});
window.onload = () => {
  const chat = document.getElementById('chat-container');
  const savedPos = localStorage.getItem('chatScrollPos');
  if (savedPos) {
    chat.scrollTop = savedPos;
    localStorage.removeItem('chatScrollPos');
  }
};

  socket.emit('joinChat', <?= $viaggio_id ?>);
</script>

</body>
</html>