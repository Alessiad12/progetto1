<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])) {
    header('Location: login.php');
    exit;
}

$utente = $_SESSION['id_utente'];
$viaggio_id = $_GET['viaggio_id'];

$query_nome = "SELECT destinazione FROM viaggi WHERE id = $1";
$res_nome = pg_query_params($dbconn, $query_nome, [$viaggio_id]);
$viaggio_nome = 'Viaggio';
if ($row_nome = pg_fetch_assoc($res_nome)) {
    $viaggio_nome = $row_nome['destinazione'];
}

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
      font-family: "Arimo", sans-serif;
      background-color: #f5f1de;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .chat-container {
      width: 90%;
      max-width: 600px;
      margin: 2rem auto;
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 6px 24px rgba(0,0,0,0.08);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      position: relative;
    }

    .chat-header {
      padding: 1rem;
      background: #fff;
      border-bottom: 1px solid #eee;
      font-weight: 600;
      font-size: 1.1rem;
      color: #355c7d;
      text-align: center;
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
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
      margin-bottom: 10px;
    }

    .messaggio img.avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      flex-shrink: 0;
    }

    .proprio {
      flex-direction: row-reverse;
    }

    .testo-messaggio {
      background-color: #f1f1f1;
      color: #2e2e2e;
      padding: 12px 16px;
      border-radius: 20px;
      border-top-left-radius: 6px;
      font-size: 0.95rem;
      max-width: 70%;
      line-height: 1.5;
      word-wrap: break-word;
      position: relative;
    }

    .proprio .testo-messaggio {
      background-color: #0d6efd;
      color: white;
      border-radius: 20px;
      border-top-left-radius: 16px;
      border-top-right-radius: 6px;
    }

    .chat-input {
      display: flex;
      padding: 0.75rem;
      background: #ffffff;
      border-top: 1px solid #e0e0e0;
    }

    .chat-input input {
      flex: 1;
      padding: 12px 16px;
      background-color: #fdfdfd;
      border: 1px solid #ccc;
      box-shadow: 0 2px 4px rgba(0,0,0,0.04);
      border-radius: 999px;
      outline: none;
      transition: border-color .2s;
    }

    .chat-input input:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
    }

    .chat-input button {
      font-weight: 500;
      margin-left: 0.5rem;
      padding: 12px 18px;
      background-color: #0d6efd;
      color: #fff;
      border: none;
      border-radius: 999px;
      cursor: pointer;
      transition: background .2s;
    }

    .chat-input button:hover {
      background-color: #084298;
    }

    .data-separatore {
      text-align: center;
      margin: 1rem 0 0.5rem;
      font-size: 0.85rem;
      color: #888;
    }

    .ora-messaggio {
      text-align: right;
      font-size: 0.75rem;
      color: #ddd;
      margin-top: 6px;
    }

    .nome-utente {
      font-weight: 600;
      font-size: 0.85rem;
      margin-bottom: 4px;
    }

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
  <div class="chat-header">
    Benvenuto nella chat di <?= htmlspecialchars($viaggio_nome) ?>
  </div>
  <div id="chat-container" class="chat-messages">
    <?php
    if (empty($messaggi)) {
        echo "<p>Inizia la chat.</p>";
    } else {
        $ultima_data = null;
        $mittente_precedente = null;

        foreach ($messaggi as $messaggio):
            $timestamp = strtotime($messaggio['data_creazione']);
            $data_messaggio = date('Y-m-d', $timestamp);
            $ora_messaggio = date('H:i', $timestamp);

            $oggi = date('Y-m-d');
            $ieri = date('Y-m-d', strtotime('-1 day'));
            if ($data_messaggio === $oggi) {
                $testo_data = 'Oggi';
            } elseif ($data_messaggio === $ieri) {
                $testo_data = 'Ieri';
            } else {
                setlocale(LC_TIME, 'it_IT.UTF-8');
                $testo_data = strftime('%d %B %Y', $timestamp);
            }

            if ($data_messaggio !== $ultima_data) {
                echo "<div class='data-separatore'>{$testo_data}</div>";
                $ultima_data = $data_messaggio;
            }

            $is_own = $messaggio['utente_id'] == $utente;
            $classe = $is_own ? 'proprio messaggio' : 'messaggio';
            $img = !empty($messaggio['immagine_mittente']) ? htmlspecialchars($messaggio['immagine_mittente']) : 'immagini/default.png';
            $nome = htmlspecialchars($messaggio['mittente_nome']);
            $testo = htmlspecialchars($messaggio['messaggio']);
            $mostra_nome = $mittente_precedente !== $messaggio['utente_id'];
            $mittente_precedente = $messaggio['utente_id'];
    ?>
        <div class="<?= $classe ?>">
          <img src="<?= $img ?>" alt="Profilo" class="avatar">
          <div class="testo-messaggio">
            <?php if ($mostra_nome): ?>
              <div class="nome-utente"><?= $nome ?></div>
            <?php endif; ?>
            <?= $testo ?>
            <div class="ora-messaggio"><?= $ora_messaggio ?></div>
          </div>
        </div>
    <?php endforeach;
    } ?>
  </div>

  <div class="chat-input">
    <input type="text" id="messageInput" placeholder="Scrivi un messaggio...">
    <button onclick="sendMessage(<?= $viaggio_id ?>, <?= $utente ?>)">Invia</button>
  </div>
</div>

<script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
<script>
  const socket = io('http://localhost:4000');

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
    // Non gestiamo nome/immagine in JS perché non abbiamo i dati completi
    location.reload(); // Ricarica per mostrare tutto correttamente
  });

  socket.emit('joinChat', <?= $viaggio_id ?>);
</script>

</body>
</html>
