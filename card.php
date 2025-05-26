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

// Recupera le preferenze dell’utente
$sqlPref = "SELECT * FROM preferenze_utente_viaggio WHERE utente_id = $1 ORDER BY data_partenza DESC LIMIT 1";
$resPref = pg_query_params($dbconn, $sqlPref, [$id_utente]);

if (!$resPref || pg_num_rows($resPref) === 0) {
    die("Nessuna preferenza trovata per l’utente.");
}

$pref = pg_fetch_assoc($resPref);
$continente_utente = $pref['destinazione'];

//destinazioni:
 function getLimitiContinente($continente) {
    switch (strtolower($continente)) {
        case 'africa':
            return ['lat_min' => -35, 'lat_max' => 37, 'lon_min' => -17, 'lon_max' => 51];
        case 'europa':
            return ['lat_min' => 35, 'lat_max' => 71, 'lon_min' => -10, 'lon_max' => 40];
        case 'asia':
            return ['lat_min' => -10, 'lat_max' => 55, 'lon_min' => 60, 'lon_max' => 150];
        case 'america':
            return ['lat_min' => -55, 'lat_max' => 70, 'lon_min' => -30, 'lon_max' => 150];
        case 'antartide':
            return ['lat_min' => -90, 'lat_max' => -60, 'lon_min' => -180, 'lon_max' => 180];
        case 'oceania':
            return ['lat_min' => -47, 'lat_max' => -10, 'lon_min' => 110, 'lon_max' => 180];
        default:
            return ['lat_min' => -90, 'lat_max' => 90, 'lon_min' => -180, 'lon_max' => 180]; // Default for "unknown"
    }
}
$limitiContinente = getLimitiContinente($continente_utente);

if (preg_match('/^\s*(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)\s*$/', $pref['budget'], $m)) {
    // Budget nel formato "100-200" → intervallo esplicito
    $budget_min = floatval($m[1]);
    $budget_max = floatval($m[2]);
} else {
    // Budget singolo, es. "300" → margine ±20%
    $budget_num = floatval(preg_replace('/[^\d.]/', '', $pref['budget']));
    $budget_min = $budget_num * 0.8;
    $budget_max = $budget_num * 1.2;
}

// Calcoli di flessibilità
$data_partenza_da = date('Y-m-d', strtotime($pref['data_partenza'] . ' -15 days'));
$data_partenza_a = date('Y-m-d', strtotime($pref['data_partenza'] . ' +15 days'));
$data_ritorno_da = date('Y-m-d', strtotime($pref['data_ritorno'] . ' -15 days'));
$data_ritorno_a = date('Y-m-d', strtotime($pref['data_ritorno'] . ' +15 days'));


// Query dei viaggi compatibili
$query = "
SELECT *
FROM viaggi v
WHERE
  tipo_viaggio ILIKE '%' || $1 || '%' 
  AND data_partenza BETWEEN $2 AND $3
  AND data_ritorno BETWEEN $4 AND $5
  AND budget::numeric >= $6 and budget::numeric <= $7
  AND latitudine BETWEEN $8 AND $9
  AND longitudine BETWEEN $10 AND $11
    AND NOT EXISTS (
      SELECT 1
      FROM viaggi_utenti vu
      WHERE vu.viaggio_id = v.id
        AND vu.user_id    = $12
    )
  ORDER BY data_partenza ASC
  LIMIT 20";


$params = [
    $pref['tipo_viaggio'],
    $data_partenza_da,
    $data_partenza_a,    
    $data_ritorno_da,
    $data_ritorno_a,
    $budget_min,
    $budget_max,
    $limitiContinente['lat_min'],
    $limitiContinente['lat_max'],
    $limitiContinente['lon_min'],
    $limitiContinente['lon_max'],
        $id_utente
 

];



$result = pg_query_params($dbconn, $query, $params);
if (!$result) {
    die("Errore nella query dei viaggi compatibili: " . pg_last_error($dbconn));
}

$viaggi = [];
while ($row = pg_fetch_assoc($result)) {
    $viaggi[] = $row;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Menu Profilo</title>
  <link rel="stylesheet" href="css/style_pagina_iniziale.css">
  <link rel="stylesheet" href="css/card.css">
  <link rel="stylesheet" href="stili_card.css">
  <link rel="stylesheet" href="css/style_notifiche.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Great+Vibes&display=swap" rel="stylesheet">
</head>
<style>
    .hero{
    background-color: #f6f5f0;
    background-image: none;
    }
    .fade-section {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: #f5f1de;
  color: rgb(8, 7, 91);
  display: flex;
  align-items: center;
  font-size: 24px;
  justify-content: center;
  opacity: 1;
  transition: opacity 1s ease-in-out;
  z-index: 200000;
  pointer-events: none; /* opzionale: disattiva click */
}

.fade-out {
  opacity: 0;
}

</style>


<div class="hero">
<nav>
    <div class="logo">
        <img src="immagini/logo.png" alt="Logo" class="logo-img"> Wanderlust </div>
        <ul>            
            <li> <a href="pagina_profilo.php"> <img src="immagini/icona_profilo.png" alt=" profilo" class="logo-img" style="height: 35px;"> </a></li>
            <li> <a href="crea_preferenze_viaggi.php"> <img src="immagini/preferenze.png" alt="Notifiche" class="logo-img" style="height: 35px;"> </a></li>
            <li> <a href="notifiche.php"> <img src="immagini/notifiche.png" alt="Notifiche" class="logo-img" style="height: 35px;"> </a></li>
            <li> <a href="login.html"><button class="btn-login">Logout</button></a></li>

        </ul>
</nav>
<div class="card-container" id="cardContainer">
<?php foreach ($viaggi as $viaggio): 
    $stili_disponibili = ['stile1', 'stile2', 'stile3', 'stile4'];

    // Scelta random di uno stile
    $classe_stile = $stili_disponibili[array_rand($stili_disponibili)];
   
    $immagine = $viaggio['foto'] ?? null;

    if (!$immagine) {
      $tipo = strtolower(trim($viaggio['tipo_viaggio'])); // Normalizza

      switch ($tipo) {
        case 'spiaggia':
          $immagine = 'https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg';
          break;
        case 'musei':
          $immagine = 'https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg';
          break;
        case 'ristoranti':
          $immagine = 'https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg';
          break;
        case 'natura':
          $immagine = 'https://i.pinimg.com/736x/89/08/9c/89089cd5fbe7662e5a35beb13eb18edf.jpg';
          break;
        default:
          $immagine = 'https://i.pinimg.com/736x/5d/e7/25/5de7255c7ef36023e25ac38fe9fec211.jpg';
          break;

          
      }
      $sqlUp = "UPDATE viaggi SET foto = $1 WHERE id = $2";
      $resUp = pg_query_params($dbconn, $sqlUp, [$immagine, $viaggio['id']]);
      if (!$resUp) {
          error_log("Errore aggiornamento foto viaggio {$viaggio['id']}: " . pg_last_error($dbconn));
}

      
    }

?>
<div class="card <?= $classe_stile ?>" id="card-<?= $viaggio['id'] ?>" data-viaggio-id="<?= $viaggio['id'] ?>">
    <?php if ($classe_stile == 'stile1' || $classe_stile == 'stile3') { ?>
        <div class="left">
            <div class="image-background" style="background-image: url('<?= htmlspecialchars($immagine) ?>');"></div>
            <div class="image-overlay"></div>
        </div>
        <div class="right">
            <h1><?php echo $viaggio['destinazione']; ?></h1>
            <h2><?php echo $viaggio['descrizione']; ?></h2>
            <p>        
                <strong>Data partenza:</strong> <?php echo $viaggio['data_partenza']; ?>
                <br>
                <strong>Data ritorno:</strong> <?php echo $viaggio['data_ritorno']; ?>
                <br>
                <strong>Budget:</strong> <?php echo $viaggio['budget']; ?></li>
                <br>
                <strong>Tipo di viaggio:</strong> <?php echo $viaggio['tipo_viaggio']; ?>
                <br>
                <div class="componenti-wrapper"></div>
                <div class="script"><?php echo $viaggio['compagnia'];?></div>
            </p>
        </div>
        <?php } ?>
        <?php if ($classe_stile == 'stile2' || $classe_stile == 'stile4') { ?>
        <div class="left">
        <h1><?php echo $viaggio['destinazione']; ?></h1>
        <h2><?php echo $viaggio['descrizione']; ?></h2>
        <p>        
          <strong>Data partenza:</strong> <?php echo $viaggio['data_partenza']; ?>
          <br>
          <strong>Data ritorno:</strong> <?php echo $viaggio['data_ritorno']; ?>
           <br>
          <strong>Budget:</strong> <?php echo $viaggio['budget']; ?></li>
           <br>
          <strong>Tipo di viaggio:</strong> <?php echo $viaggio['tipo_viaggio']; ?>
           <br>
        <div class="componenti-wrapper"></div>
          <div class="script"><?php echo $viaggio['compagnia'];?></div>
         </p>
    </div>
    <div class="right" >
  <div class="image-background" style="background-image: url('<?= htmlspecialchars($immagine) ?>');"></div>
  <div class="image-overlay"></div>
    </div>
    <?php } ?>

        </div>
<?php endforeach; ?>
</div>
<div class="fade-section" id="intro">
  Inizia a fare swipe
</div>
<div id="matchModal" class="modal" style="display: none;">
  <div class="modal-content">
    <span class="close-btn" id="closeModal">&times;</span>
    <p id="matchText"></p>
    <button onclick="window.location.href='notifiche.php'">Vai alle notifiche</button>
  </div>
</div>

<div class="reaction-buttons">

  <button class="circle-button dislike-button"><img src="/immagini/dislike.png" alt="Dislike"></button>
    <button class="circle-button like-button"><img src="/immagini/like.png" alt="Like"></button>
</div>
<div class="profile-menu-wrapper">
  <img src="<?= htmlspecialchars($immagine_profilo) ?>" alt="Foto Profilo" class="profile-icon"  />
  <div class="dropdown-menu" id="dropdownMenu" >
  </div>
</div>
</div>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
  const userId = <?= json_encode($_SESSION['id_utente']); ?>;
  const socket = io('http://localhost:4000');

  socket.on('connect', () => {
    console.log('Socket connesso:', socket.id);
    socket.emit('join', userId);
    console.log(`Socket ${socket.id} entrato in stanza user_${userId}`);
  });

  socket.on('matchAcceptedNotification', (data) => {
    console.log('Match accettato ricevuto:', data);
    const modal = document.getElementById("matchModal");
    const text = document.getElementById("matchText");
    text.innerHTML = `<strong>Hai un nuovo match!</strong><br>Viaggio: <strong>"${data.tripTitle}"</strong>`;
    modal.style.display = "block";

    document.getElementById("closeModal").onclick = () => {
      modal.style.display = "none";
    };
  });
</script>
<script src="js/visualizza_viaggi.js"></script>
<script> 
document.querySelectorAll('.card').forEach(card => {
  enableMouseSwipe(card);
  enableSwipe(card);
});
window.addEventListener("load", function () {
  setTimeout(function () {
    const intro = document.getElementById("intro");
    intro.classList.add("fade-out");
    intro.addEventListener("transitionend", function () {
      intro.parentNode.removeChild(intro);
    }, { once: true });
  }, 200);
});

</script>
</body>
</html>
