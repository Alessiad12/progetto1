<?php 
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])) {
    header('Location: login.php');
    exit;
}

$utente = $_SESSION['id_utente'];
$viaggio_id = $_GET['viaggio_id'];

// Prendiamo destinazione e foto di sfondo dal viaggio
$query_nome = "SELECT destinazione, foto FROM viaggi WHERE id = $1";
$res_nome = pg_query_params($dbconn, $query_nome, [$viaggio_id]);
$viaggio_nome = 'Viaggio';
$viaggio_img = 'immagini/default-bg.jpg';
if ($row_nome = pg_fetch_assoc($res_nome)) {
    $viaggio_nome = $row_nome['destinazione'];
    if (!empty($row_nome['foto'])) {
        $viaggio_img = htmlspecialchars($row_nome['foto']);
    }
}

// Lista dei viaggi (chat) a cui l'utente ha partecipato
$query_chatlist = "
  SELECT v.id, v.destinazione, v.foto
  FROM viaggi v
  JOIN chat_viaggio c ON c.viaggio_id = v.id
  WHERE c.utente_id = $1
  GROUP BY v.id, v.destinazione, v.foto
  ORDER BY MAX(c.data_creazione) DESC
";
//order by data_creazione DESC mi serve per avere ordine cronologico dopo

$res_chatlist = pg_query_params($dbconn, $query_chatlist, [$utente]);
$chatlist = pg_fetch_all($res_chatlist) ?: [];

// Query dei messaggi
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
    /* reset e box-sizing */
    *, *::before, *::after { box-sizing: border-box; margin:0; padding:0; }
    html, body { width:100%; height:100%; overflow:hidden; font-family:"Arimo",sans-serif; background:#f5f1de; }

    /* layout a due colonne */
    .app-container { display:flex; width:100%; height:100%; }

    /* colonna sinistra: back button, titolo, lista */
    .sidebar-col { flex: 0 0 300px; display:flex; flex-direction:column; height:100%; }
    .sidebar-brand {
      flex: 0 0 56px;
      display: flex;
      align-items: center;
      padding: 0 16px;
      background: #202c33;
    }
    .back-btn img { width:24px; height:24px; cursor:pointer; }
    .sidebar-title {
      flex: 0 0 56px;
      display: flex;
      align-items: center;
      padding: 0 16px;
      background: #fff;
      border-bottom: 1px solid #ddd;
      font-weight: 600;
      font-size: 1.25rem;
    }
    .chat-list {
      flex: 1 1 auto;
      background:#fff;
      overflow-y:auto;
      border-right:1px solid #ddd;
    }
    .chat-list-item {
      display:flex; align-items:center;
      padding:.75rem; border-bottom:1px solid #eee;
      text-decoration:none; color:inherit; transition:background .2s;
    }
    .chat-list-item:hover { background:#f0f0f0; }
    .chat-list-item img {
      width:40px; height:40px; border-radius:50%; object-fit:cover; margin-right:.75rem;
    }
    .destinazione { font-weight:600; }

    /* pannello chat */
    .chat-panel {
      flex:1; display:flex; flex-direction:column; position:relative;
      background: url('<?= $viaggio_img ?>') center/cover;
    }
    .chat-panel::before { content:""; position:absolute; inset:0; background:rgba(255,255,255,0.5); }
    .chat-panel>* { position:relative; z-index:1; }

    /* header chat */
    .chat-header {
      flex:0 0 56px;
      display:flex; align-items:center;
      padding:0 16px; background:#fff;
      border-bottom:1px solid #eee;
      font-weight:600; color:#355c7d;
      font-size:1.25rem;
    }

    /* messaggi */
    .chat-messages {
      flex:1; overflow-y:auto; padding:1rem;
      display:flex; flex-direction:column; gap:.5rem;
    }
    .chat-messages::-webkit-scrollbar { width:6px; }
    .chat-messages::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.2); border-radius:3px; }
    .data-separatore {
      align-self:center; background:#fff;
      padding:.25rem .75rem; border-radius:12px;
      font-size:.85rem; color:#888;
    }
    .messaggio, .proprio { display:flex; align-items:flex-start; }
    .messaggio img.avatar, .proprio img.avatar {
      width:40px; height:40px; border-radius:50%; object-fit:cover;
    }
    .messaggio .testo-messaggio, .proprio .testo-messaggio {
      max-width:60%;
      padding:12px 16px 20px 16px; /* ← Aggiunto spazio in basso per l'orario */
      border-radius:20px;
      line-height:1.5;
      position:relative;
      min-height:40px;
    }
    .messaggio .testo-messaggio {
      background:#f1f1f1; color:#2e2e2e; margin-left:.5rem;
    }
    .proprio { flex-direction:row-reverse; }
    .proprio .testo-messaggio {
      background:#0d6efd; color:#fff; margin-right:.5rem;
    }
    .nome-utente { font-size:.85rem; font-weight:600; margin-bottom:4px; }
    .ora-messaggio {
      position:absolute; bottom:4px; right:6px;
      font-size:.75rem; color:#aaa;
    }

    /* input */
    .chat-input {
      flex:0 0 56px; display:flex; align-items:center;
      padding:0 16px; background:#fff; border-top:1px solid #e0e0e0;
    }
    .chat-input input {
      flex:1; padding:8px 12px; border-radius:20px;
      border:1px solid #ccc; outline:none; transition:border-color .2s;
    }
    .chat-input input:focus { border-color:#0d6efd; }
    .chat-input button {
      margin-left:8px; background:#0d6efd; border:none;
      color:#fff; padding:8px 12px; border-radius:50%; cursor:pointer;
    }
    .chat-input button:hover { background:#084298; }
  </style>
</head>
<body>
  <div class="app-container">
    <div class="sidebar-col">
      <div class="sidebar-brand">
        <a href="notifiche.php" class="back-btn"><img src="immagini/back.svg" alt="Indietro"></a>
      </div>
      <div class="sidebar-title">Le mie chat</div>
      <div class="chat-list">
        <?php foreach($chatlist as $c): ?>
          <a href="?viaggio_id=<?php echo $c['id']; ?>" class="chat-list-item">
            <img src="<?php echo htmlspecialchars($c['foto'] ?: 'immagini/default-bg.jpg'); ?>" alt="">
            <div class="destinazione"><?php echo htmlspecialchars($c['destinazione']); ?></div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
<!--in ultima data scorro ò'array e controllo la data di ogni messaggio,cosi quando cambia giorno inserisco un nuovo separatore-->
    <div class="chat-panel">
      <div class="chat-header">Benvenuto nella chat di <?php echo htmlspecialchars($viaggio_nome); ?></div>
      <div id="chat-container" class="chat-messages">
        <?php if (empty($messaggi)): ?>
          <p>Seleziona una chat o inizia a scrivere per creare un nuovo messaggio.</p>
        <?php else:
          $ultima_data = null;
          $mittente_precedente = null;
          foreach ($messaggi as $m):
            //converto la data in timestap (secondi dall'epoca)
            $ts = strtotime($m['data_creazione']);
            //formatto il timestamp come data per avere la data solo come giorno,senza orario
            $d  = date('Y-m-d',$ts);
            if ($d !== $ultima_data) {
              if ($d === date('Y-m-d')) $label='Oggi';
              elseif ($d === date('Y-m-d',strtotime('-1 day'))) $label='Ieri';
              else { setlocale(LC_TIME,'it_IT.UTF-8'); $label=strftime('%d %B %Y',$ts); }
              echo "<div class='data-separatore'>{$label}</div>";
              //aggiorno l'ultima data per il prossimo confronto
              //in questo modo se la data è uguale non viene visualizzato il separatore
              $ultima_data=$d;
            }
            $own = $m['utente_id']==$utente;
            $cls = $own?'proprio':'messaggio';
            $img = !empty($m['immagine_mittente'])?htmlspecialchars($m['immagine_mittente']):'immagini/default.png';
        ?>
        <div class="<?php echo $cls; ?>">
          <img src="<?php echo $img; ?>" class="avatar" alt="Profilo">
          <div class="testo-messaggio">
            <!-- Se il mittente è diverso dal precedente, mostro il nome utente-->
            <?php if ($mittente_precedente!==$m['utente_id']): ?><div class="nome-utente"><?php echo htmlspecialchars($m['mittente_nome']); ?></div><?php endif; ?>
            <?php echo htmlspecialchars($m['messaggio']); ?>
            <div class="ora-messaggio"><?php echo date('H:i',$ts); ?></div>
          </div>
        </div>
        <?php $mittente_precedente=$m['utente_id']; endforeach; endif; ?>
      </div>
      <?php if ($viaggio_id): ?>
      <div class="chat-input">
        <input type="text" id="messageInput" placeholder="Scrivi un messaggio...">
        <button onclick="sendMessage(<?php echo $viaggio_id;?>,<?php echo $utente;?>)">Invia</button>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Includo Socket.IO per la comunicazione in tempo reale -->
  <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
  <script>
    const input = document.getElementById('messageInput');

    input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault(); // Impedisce di andare a capo
      sendMessage(<?= $viaggio_id ?>, <?= $utente ?>);
    }
  });
    const socket = io('http://localhost:4000');
    function sendMessage(vId,uId){const input=document.getElementById('messageInput');const msg=input.value.trim();if(!msg)return;socket.emit('sendMessage',{viaggio_id:vId,mittente_id:uId,messaggio:msg});input.value='';}
    socket.on('newMessage',()=>location.reload());
    <?php if($viaggio_id):?>socket.emit('joinChat',<?php echo $viaggio_id;?>);<?php endif;?>
  </script>
</body>
</html>

