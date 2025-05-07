<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])) {
    header('Location: login.php');
    exit;
}
$utente_id = intval($_SESSION['id_utente']);

// --- 1) Recupero fino a 4 foto dalle esperienze terminate ---
$sql = "
  SELECT foto1, foto2, foto3, foto4, foto5
  FROM viaggi_terminati
  WHERE utente_id = $1
  ORDER BY data_creazione DESC
";
$res = pg_query_params($dbconn, $sql, [ $utente_id ]);

$photos = [];
if ($res) {
    while ($row = pg_fetch_assoc($res)) {
        // prendo ogni colonna fotoN, fino ad avere 4 immagini
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($row["foto{$i}"])) {
                $photos[] = $row["foto{$i}"];
                if (count($photos) >= 4) break 2;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style_index.css">
  <title>Profilo Viaggiatore</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body, html {
      background-color:rgb(247, 247, 247);
      height: 100%;
      font-family: Arial, sans-serif;
    }

    .page-wrapper {
      display: flex;
      height: 100vh;
      width: 100vw;
    }

    /* PROFILO A SINISTRA */

    .profile-sidebar {
  border-radius: 20px;
  transition: background-color 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
  width: 260px;
  padding: 20px;
  box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
  background-color: rgb(186, 222, 214);
}

      .profile-sidebar-container {
  width: 190px;
  height: 250px;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  position: relative;
  cursor: grab;
}


.profile-sidebar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center center;
  user-select: none;
  pointer-events: none;
}



    .profile-sidebar h2 {
      font-size: 20px;
      text-align: center;
      margin-bottom: 5px;
    }

    .profile-sidebar p {
      font-size: 14px;
      text-align: center;
      margin-bottom: 15px;
    }

    .profile-stats {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 20px;
    }

    .stat-card {
      background-color: #e3f2fd;
      border-radius: 10px;
      padding: 10px;
      text-align: center;
      font-size: 14px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .stat-card span {
      display: block;
      font-size: 18px;
      font-weight: bold;
      color: #1976d2;
    }

    .profile-sidebar button {
      display: block;
      width: 100%;
      margin-bottom: 10px;
      padding: 8px;
      border: none;
      background-color: #3498db;
      color: white;
      border-radius: 6px;
      cursor: pointer;
    }

    /* AREA CONTENUTO DESTRO */
    .content-area {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 20px;
      overflow: hidden;
    }

    .map-container {
      height: 350px;
      background-color: #ddd;
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 20px;
      margin-right: 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      position: relative;
    }

    .map-container iframe {
      width: 100%;
      height: 100%;
      border: none;
     /* pointer-events: none; /* evita il click accidentale sull’iframe */
    }

    .map-container::after {
      content: "Clicca per espandere";
      position: absolute;
      bottom: 10px;
      right: 10px;
      background: rgba(0, 0, 0, 0.4);
      color: white;
      padding: 4px 8px;
      font-size: 12px;
      border-radius: 4px;
    }

    .photos-container {
      flex: 1;
      overflow-y: auto;
      background-color: #fafafa;
      padding: 10px;
      border-radius: 10px;
      display: flex;
      flex-wrap: wrap;
      margin-right: 20px;
      gap: 10px;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    }

    .photos-container img {
      width: calc(33.3% - 10px);
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
    }

    /* OVERLAY MAPPA FULLSCREEN */
    .map-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0,0,0,0.8);
      z-index: 1000;
      display: none;
      align-items: center;
      justify-content: center;
    }

    .map-overlay iframe {
      width: 90%;
      height: 90%;
      border: none;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
    }

    .map-overlay .close-btn {
      position: absolute;
      top: 20px;
      right: 30px;
      font-size: 28px;
      color: white;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="page-wrapper">

    <!-- PROFILO -->
    <div class="profile-sidebar">
      <div class="profile-sidebar-container">
      
    <img class="profile-pic" src="immagini/new-york-city.jpg" alt="Profilo">
    </div>
    <h2 class="profile-name">Mario Rossi, 35</h2>
    <p class="profile-bio">Viaggiatore curioso.<br> Amo conoscere culture e paesaggi.</p>
    <div class="profile-stats">
      <div class="stat-card">
        Compagni<br><span class="profile-compagni">18</span>
      </div>
      <div class="stat-card">
        Viaggi<br><span class="profile-viaggi">12</span>
      </div>
    </div>
    <button onclick="window.location.href='modifica_profilo.php'">Modifica profilo</button>
    <button onclick="window.location.href='/crea_viaggio.php'">Crea viaggio</button>
    
  </div>

    <!-- CONTENUTO DESTRO -->
    <div class="content-area">

      <!-- MAPPA -->
      <div class="map-container" onclick="espandiMappa()">
        <iframe src="mappamondo.php"></iframe>
      </div>

      <!-- FOTO -->
      <div class="photos-container">
        <?php if (!empty($photos)): ?>
          <?php foreach ($photos as $idx => $src): ?>
            <img
              src="<?= htmlspecialchars($src) ?>"
              alt="Viaggio <?= $idx + 1 ?>">
          <?php endforeach; ?>
        <?php else: ?>
          <p>Non hai ancora caricato foto delle tue esperienze.</p>
        <?php endif; ?>
</div>

    </div>
  </div>

  <!-- OVERLAY MAPPA -->
  <div class="map-overlay" id="mapOverlay">
    <span class="close-btn" onclick="chiudiMappa()">✖</span>
    <iframe src="mappamondo.php"></iframe>
  </div>
    <!-- Menu Profilo -->
    <div class="profile-menu-wrapper">
    <img src="immagini/new-york-city.jpg" alt="Foto Profilo" class="profile-icon" onclick="toggleDropdown()" />
    <div class="dropdown-menu" id="dropdownMenu">
      <a href="pagina_profilo.php">Profilo</a>
      <a href="login.html">Logout</a>
      <a href=visualizza_viaggi.php>Home</a>
      <a href=notifiche.php>Notifiche</a>
    </div>
  </div>

  <script>
    function espandiMappa() {
      document.getElementById('mapOverlay').style.display = 'flex';
    }

    function chiudiMappa() {
      document.getElementById('mapOverlay').style.display = 'none';
    }
  </script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    fetch('/profilo.php')
      .then(res => res.json())
      .then(data => {
        if (!data.error) {
          document.querySelector('.profile-name').textContent = `${data.nome}, ${data.eta}`;
          document.querySelector('.profile-bio').innerHTML = data.bio.replace(/\n/g, '<br>');
          document.querySelector('.profile-pic').src =  data.immagine_profilo;
          document.querySelector('.profile-icon').src =   data.immagine_profilo;
          document.querySelector('.photos-container').style.backgroundColor = data.colore_sfondo;
          if (data.posizione_immagine !== undefined) {
            document.querySelector('.profile-pic').style.objectPosition = `${data.posizione_immagine}% center`;
            document.querySelector('.profile-icon').style.objectPosition = `${data.posizione_immagine}% center`;
          }
        } else {
          console.error(data.error);
        }
      });
  });

  document.addEventListener('DOMContentLoaded', () => {
    fetch('/profilo.php')
      .then(res => res.json())
      .then(data => {
        if (!data.error) {
          const sidebar = document.querySelector('.profile-sidebar');
          sidebar.style.setProperty('background-color', data.colore_sfondo, 'important');
        } else {
          console.error(data.error);
        }
      });
    });

</script>
<script type="module" src="/js/index.js"></script>
</body>
</html>
