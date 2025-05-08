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
    font-family: "Inter", sans-serif;
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
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .chat-messages {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 1rem;
    overflow-y: auto;
    background: #f7f9fb;
    border-bottom: 1px solid #e0e0e0;
    scroll-behavior: smooth;
  }

  .messaggio {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .messaggio img.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
  }

  .testo-messaggio {
    background-color: #e9ecef;
    color: #343a40;
    padding: 0.75rem 1rem;
    border-radius: 16px;
    border-top-left-radius: 4px;
    max-width: 75%;
    word-wrap: break-word;
    position: relative;
  }

  /* messaggi propri (allineati a destra e con colori invertiti) */
  .proprio {
    justify-content: flex-end;
  }

  .proprio .testo-messaggio {
    background-color: #0d6efd;
    color: #fff;
    border-top-left-radius: 16px;
    border-top-right-radius: 4px;
  }

  /* footer fisso con input e bottone */
  .chat-input {
    display: flex;
    padding: 0.75rem;
    background: #ffffff;
    border-top: 1px solid #e0e0e0;
  }

  .chat-input input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 1px solid #ced4da;
    border-radius: 20px;
    outline: none;
    transition: border-color .2s;
  }

  .chat-input input:focus {
    border-color: #0d6efd;
  }

  .chat-input button {
    margin-left: 0.5rem;
    padding: 0 1.2rem;
    background-color: #0d6efd;
    color: #fff;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    transition: background .2s;
  }

  .chat-input button:hover {
    background-color: #084298;
  }

  /* scrollbar minimalista */
  .chat-messages::-webkit-scrollbar {
    width: 6px;
  }
  .chat-messages::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 3px;
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