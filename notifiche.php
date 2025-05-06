<?php
session_start();
require_once 'connessione.php'; // Connessione al DB

if (!isset($_SESSION['id_utente']) || !isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}

$id_utente = $_SESSION['id_utente'];

// Ottieni il colore di sfondo dell'utente
$sql = "SELECT colore_sfondo, immagine_profilo FROM profili WHERE id = $1";
$res = pg_query_params($dbconn, $sql, [$id_utente]);
if (!$res) {
    die("Errore nella query del profilo: " . pg_last_error($dbconn));
}
$row = pg_fetch_assoc($res);
$colore_sfondo = $row['colore_sfondo'] ?? '#fef6e4';
$immagine_profilo = $row['immagine_profilo'] ?? 'immagini/default.png';
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifiche</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      padding: 20px;
    }
    .profile-menu-wrapper {
    position: absolute;
    bottom: 20px;
    left: 20px;
  }
  
  .profile-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid #ddd;
  }
  
  .dropdown-menu {
    display: none;
    position: absolute;
    bottom: 60px;
    left: 0;
    background-color: white;
    min-width: 140px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    z-index: 1000;
  }
  
  .dropdown-menu a {
    display: block;
    padding: 12px 16px;
    color: #333;
    text-decoration: none;
    transition: background 0.2s;
  }
  
  .dropdown-menu a:hover {
    background-color: #f0f0f0;
  }
  
    .notifica {
      background-color: #fff;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }
    .notifica:hover {
      transform: scale(1.05);
    }
  </style>
</head>
<body>

<h1>Le tue notifiche</h1>

<div id="notifiche">
  <!-- Le notifiche verranno aggiunte qui -->
</div>
<div class="profile-menu-wrapper">
  <img src="<?= htmlspecialchars($immagine_profilo) ?>" alt="Foto Profilo" class="profile-icon"  />
  <div class="dropdown-menu" id="dropdownMenu" >
          <a href="pagina_profilo.php">Profilo</a>
          <a href="login.html">Logout</a>
  </div>
</div>


<!-- Socket.IO -->
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
  // Ottieni l'ID dell'utente dalla sessione PHP (assumiamo che tu abbia già un sistema di autenticazione)
  const userId = <?php echo $_SESSION['id_utente']; ?>; // Modifica secondo la tua logica

  // Crea una connessione Socket.IO al server
  const socket = io('http://localhost:4000');

  // Unisciti alla stanza con il tuo userId per ricevere notifiche personalizzate
  socket.on('connect', () => {
    socket.emit('join', userId);
    console.log(`Connesso come ${socket.id}`);
  });

  // Ascolta le notifiche
  socket.on('swipeNotification', (data) => {
    console.log('Notifica ricevuta:', data);

    // Crea un elemento per visualizzare la notifica
    const notificaElement = document.createElement('div');
    notificaElement.classList.add('notifica');
    notificaElement.innerHTML = `
      <strong>Nuovo Like:</strong> ${data.fromUser} ha messo un like al viaggio "${data.tripTitle}".
    `;

    // Aggiungi la notifica all'elenco
    document.getElementById('notifiche').appendChild(notificaElement);


  });
</script>

</body>
</html>
