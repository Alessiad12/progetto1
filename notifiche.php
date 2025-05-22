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

$notifiche = [];
// Recupera le notifiche per l'utente
$sql = "
SELECT n.*, p.nome AS mittente_nome, p.immagine_profilo AS immagine_mittente
FROM notifiche n
JOIN profili p ON n.mittente_id = p.id
WHERE n.utente_id = $1
ORDER BY n.data_creazione DESC;
";

$res = pg_query_params($dbconn, $sql, [$id_utente]);
if (!$res) {
    die("Errore nella query delle notifiche: " . pg_last_error($dbconn));
}

$notifiche = [];
while ($row = pg_fetch_assoc($res)) {
    $notifiche[] = $row;
}
$mittente_nome = $row['mittente_nome'] ?? 'Utente sconosciuto';
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifiche</title>
  <link rel="stylesheet" href="css/style_notifiche.css">
</head>
<body style="background-color: <?= htmlspecialchars($colore_sfondo) ?>;">
 <?php
// Determina se tutte le notifiche sono di tipo 'like'
$solo_like = true;

foreach ($notifiche as $notifica) {
    if ($notifica['tipo'] !== 'like') {
        $solo_like = false;
        break;
    }
}
?>

<body style="background-color: <?= htmlspecialchars($colore_sfondo) ?>;">

  <div class="header">Notifiche</div>

  <div class="tabs">
    <div class="tab <?= $solo_like ? 'active' : '' ?>" id="notificheTab">Notifiche</div>
    <div class="tab <?= !$solo_like ? 'active' : '' ?>" id="chatTab">Chat</div>
  </div>

  <div class="content" id="notificheContent" style="<?= $solo_like ? '' : 'display: none;' ?>">
    <div id="notifiche">
      <?php
      if (empty($notifiche)) {
          echo "<p>Nessuna notifica al momento.</p>";
      } else {
          foreach ($notifiche as $notifica) {
              $tipo = $notifica['tipo'];
              $mittente_nome = htmlspecialchars($notifica['mittente_nome'] ?? '');
              $viaggio = htmlspecialchars($notifica['titolo_viaggio'] ?? '');
              $profilo_img = !empty($notifica['immagine_mittente']) ? htmlspecialchars($notifica['immagine_mittente']) : 'immagini/default.png';
              $mittente_id = htmlspecialchars($notifica['mittente_id'] ?? '');
              $id = htmlspecialchars($notifica['id']);
              $viaggio_id = htmlspecialchars($notifica['viaggio_id'] ?? '');

              if ($tipo === 'like') {
                  echo "<div class='notifica'>
                          <div class='notifica-header'>
                            <a href='get_profiloo.php?id=$mittente_id'>
                              <img src='$profilo_img' alt='Profilo' class='avatar'>
                            </a>
                            <div class='testo-notifica'>
                              <strong>$mittente_nome</strong> è interessato a partire con te per il viaggio <strong>\"$viaggio\"</strong>.
                            </div>
                          </div>
                          <button class='accetta-btn' data-id='$id'>Accetta</button>
                        </div>";
              } elseif ($tipo === 'registra_viaggio') {
                  echo "<div class='notifica'>
                          <div class='notifica-header'>
                            <div class='testo-notifica'>
                              Il viaggio <strong>\"$viaggio\"</strong> è terminato. Puoi ora <strong>registrare il resoconto</strong>.
                            </div>
                          </div>
                          <button class='registra-btn' data-id='$id' data-viaggio-id='$viaggio_id'>Registra viaggio</button>
                        </div>";
              }
          }
      }
      ?>
    </div>
  </div>

  <div class="content" id="chatContent" style="<?= !$solo_like ? '' : 'display: none;' ?>">
        <div id="notifiche">
      <?php
          foreach ($notifiche as $notifica) {
              $tipo = $notifica['tipo'];
              $mittente_nome = htmlspecialchars($notifica['mittente_nome'] ?? '');
              $viaggio = htmlspecialchars($notifica['titolo_viaggio'] ?? '');
              $profilo_img = !empty($notifica['immagine_mittente']) ? htmlspecialchars($notifica['immagine_mittente']) : 'immagini/default.png';
              $mittente_id = htmlspecialchars($notifica['mittente_id'] ?? '');
              $id = htmlspecialchars($notifica['id']);
              $viaggio_id = htmlspecialchars($notifica['viaggio_id'] ?? '');

              if ($tipo === 'match_accepted') {
                            echo "<div class='notifica'>
                                    <div class='notifica-header'>
                                      <a href='get_profiloo.php?id=$mittente_id'>
                                        <img src='$profilo_img' alt='Profilo' class='avatar'>
                                      </a>
                                      <div class='testo-notifica'>
                                        <strong>$mittente_nome</strong> ha accettato la tua proposta <strong>\"$viaggio\"</strong>.
                                      </div>
                                    </div>
                                    <button class='organizza-btn' data-id='$id' data-viaggio-id='$viaggio_id'>Organizza</button>
                                  </div>";
                        }
          } 

                 ?>
  </div>
  </div>
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

  // Ricarica la pagina forzando anche dal server (bypassa cache)
  window.location.reload(true);
});
socket.on('matchAcceptedNotification', (data) => {
  console.log('Notifica ricevuta:', data);

  // Ricarica la pagina forzando anche dal server (bypassa cache)
  window.location.reload(true);
});
socket.on('registraViaggioNotification', (data) => {
  console.log('Notifica ricevuta:', data);

  // Ricarica la pagina forzando anche dal server (bypassa cache)
  window.location.reload(true);
});
</script>
<script>
  // Mostra/nascondi il menu a discesa
  const profileIcon = document.querySelector('.profile-icon');
  const dropdownMenu = document.getElementById('dropdownMenu');

  profileIcon.addEventListener('click', () => {
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
  });

  // Chiudi il menu se si fa clic al di fuori
  window.addEventListener('click', (event) => {
    if (!profileIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
      dropdownMenu.style.display = 'none';
    }
  });
</script>
<script>
document.querySelectorAll('.accetta-btn').forEach(button => {
  button.addEventListener('click', async () => {
    const notificaId = button.dataset.id;

    button.disabled = true;
    button.innerText = 'In attesa...';

    try {
      const response = await fetch('accetta_notifica.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ notifica_id: notificaId })
      });

      const data = await response.json();
      if (data.success) {
        alert('Match accettato! Notifica inviata.');
        button.innerText = 'Accettato';
      } else {
        alert('Errore: ' + data.message);
        button.disabled = false;
        button.innerText = 'Accetta';
      }
    } catch (error) {
      console.error('Errore nella richiesta:', error);
      alert('Errore nella comunicazione con il server.');
      button.disabled = false;
      button.innerText = 'Accetta';
    }
  });
});
document.querySelectorAll('.organizza-btn').forEach(button => {
  button.addEventListener('click', async () => {
    const viaggioId = button.dataset.viaggioId;

    // Naviga direttamente alla pagina della chat del viaggio
    window.location.href = `chat.php?viaggio_id=${viaggioId}`;
  });
});


  // Funzione per gestire il click e fare il redirect
  function redirectToRegistraViaggio(button) {
    const viaggioId = button.getAttribute('data-viaggio-id');
    // cambia qui:
    const url = `termina_viaggio.php?viaggio_id=${viaggioId}`;
    window.location.href = url;
}

document.querySelectorAll('.registra-btn').forEach(button => {
  button.addEventListener('click', () =>
    redirectToRegistraViaggio(button)
  );
});


</script>
 
  <script>
    const chatTab = document.getElementById("chatTab");
    const notificheTab = document.getElementById("notificheTab");
    const chatContent = document.getElementById("chatContent");
    const notificheContent = document.getElementById("notificheContent");

    chatTab.addEventListener("click", () => {
      chatTab.classList.add("active");
      notificheTab.classList.remove("active");
      chatContent.style.display = "block";
      notificheContent.style.display = "none";
    });

    notificheTab.addEventListener("click", () => {
      notificheTab.classList.add("active");
      chatTab.classList.remove("active");
      notificheContent.style.display = "block";
      chatContent.style.display = "none";
    });
  </script>
</body>
</html>
