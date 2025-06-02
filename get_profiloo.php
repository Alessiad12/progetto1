<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])) {
    header('Location: login.php');
    exit;
}
$utente_id = $_GET['id'] ?? null; 
// --- Recupera foto e ID viaggio ---
$sql = "
  SELECT viaggi_terminati.viaggio_id, foto1, foto2, foto3, foto4, foto5, viaggi.destinazione as destinazione
  FROM viaggi_terminati
  JOIN viaggi ON viaggi_terminati.viaggio_id = viaggi.id
  WHERE utente_id = $1
  ORDER BY data_creazione DESC;
";
$res = pg_query_params($dbconn, $sql, [$utente_id]);

$photoGroups = [];
if ($res) {
    while ($row = pg_fetch_assoc($res)) {
        $foto = [];
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($row["foto{$i}"])) {
                $foto[] = $row["foto{$i}"];
            }
        }

        if (!empty($foto)) {
            $photoGroups[$row['destinazione']] = [
                'viaggio_id' => $row['viaggio_id'],
                'foto' => $foto
            ];
        }
    }
}

// --- Conta viaggi terminati ---
$sql = "
  SELECT COUNT(DISTINCT viaggio_id) AS viaggi
  FROM viaggi_terminati
  WHERE utente_id = $1
  GROUP BY utente_id;
";
$result = pg_query_params($dbconn, $sql, [$utente_id]);
$ro = pg_fetch_assoc($result);
$n_viaggi = $ro ? $ro['viaggi'] : 0;
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style_index.css">
  <link rel="stylesheet" href="css/style_pagina_profilo.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/card.css">
    <link rel="stylesheet" href="css/style_pagina_iniziale.css">
  <title>Profilo Viaggiatore</title>
  <style>
      @font-face {
      font-family: 'secondo_font';
      src: url('/font/8e78142e2f114c02b6e1daaaf3419b2e.woff2') format('woff2');
      font-display: swap;
    }
    .w-100 {
      width: 100%!important;
      height: 100%;
    }
    /* ===== Disposizione mobile per pagina profilo ===== */
@media (max-width: 600px) {
  /* Permetti alla pagina di crescere in altezza invece che restare ferma a 100vh */
  .page-wrapper {
    flex-direction: column !important;
    height: auto !important;
    max-width: 100%;
    overflow-x: hidden;
  }

  /* Sidebar e content area full-width e senza margin-right */
  .profile-sidebar,
  .content-area {
    width: 100% !important;
    margin: 0 !important;
    padding: 1rem !important;
  }

  /* Rimuovi lo spazio a destra su mappa e foto */
  .map-container,
  .photos-container {
    width: 100% !important;
    margin-right: 0 !important;
  }

  /* Riduci l’altezza della mappa per mobile */
  .map-container {
    height: 200px;
    margin-bottom: 1rem;
  }

  /* Foto a due colonne più piccole */
  .photos-container {
    gap: 0.5rem;
  }
  .photos-container img {
    width: calc(50% - 0.5rem);
    height: 80px;
  }

  /* Se hai il menu fisso in basso, centrato */
  .profile-menu-wrapper {
    position: fixed;
    bottom: 20px;
    left: 20px;
    z-index: 1000;
    }
}
    .logo-img {
        height: 40px;
        width: auto;
        margin-right: 10px;
        vertical-align: middle;
        filter: brightness(0) invert(1); 
    }
  </style>
</head>
<body>
      <script src="https://cdn.tailwindcss.com"></script>
  <nav style='padding: 0;
background-color: #9cc4cc;'>
    <div class="logo">
        <img src="immagini/logo.png" alt="Logo" class="logo-img" > Wanderlust </div>
        <ul style="margin-right: 1rem; margin-top:0.5rem;">     
            <li> <a href="pagina_profilo.php"> <img src="immagini/icona_profilo.png" alt=" profilo" class="logo-img" style="height: 35px;   margin-top:2px; filter: sepia(1) hue-rotate(560deg) saturate(10) brightness(0.4) contrast(1.5);">  </a></li>       
            <li> <a href="notifiche.php"> <img src="immagini/notifiche.png" alt="Notifiche" class="logo-img" style="height: 35px; margin-top:2px;  filter: sepia(1) hue-rotate(180deg) saturate(4);"> </a></li>
            <li>  <a href="card.php" class="logo-img" >
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-15 h-10 fill-[#0A2342] hover:opacity-80 transition" viewBox="0 0 24 24">
                    <path d="M12 3l9 8h-3v9h-12v-9h-3l9-8z"/>
                  </svg>
                  </a></li>
            <li> <a href="login.html"><button class="btn-login" style="margin-top: 2px;">Logout</button></a></li>

        </ul>
</nav>
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
        Compagni<br><span class="profile-compagni"></span>
      </div>
      <div class="stat-card">
        Viaggi<br><span class="profile-viaggi"><?= htmlspecialchars($n_viaggi) ?></span>
      </div>
    </div>
  </div>

  
  <!-- AREA CONTENUTI -->
  <div class="content-area">

    <!-- MAPPA -->
    <div class="map-container" onclick="espandiMappa()">
      <iframe src="/mappamondo(get_profilo).php?id=<?= urlencode($utente_id)?>"></iframe>
    </div>

    <!-- FOTO ESPERIENZE -->
    <div class="photos-container">
      <?php if (!empty($photoGroups)): ?>
        <?php foreach ($photoGroups as $destinazione => $data): ?>
          <?php 
            $group = $data['foto'];
            $viaggio_id = $data['viaggio_id'];
            $modalId = 'modal-' . md5($destinazione); 
          ?>
          <div class="photo-group mb-4">

            <!-- COPERTINA -->
            <div class="cover-container" style="background-color:rgba(251, 253, 254, 0.76); font-family: secondo_font, sans-serif; color: rgb(4, 2, 38); padding: 50px 0; text-align: center; cursor: pointer;"
                 data-bs-toggle="modal"
                 data-bs-target="#<?= $modalId ?>">
              <h4><?= htmlspecialchars($destinazione) ?></h4>
            </div>

            <!-- MODAL FOTO -->
            <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                  <div class="modal-body p-0">
                    <div id="carousel-<?= $modalId ?>" class="carousel slide" data-bs-ride="carousel">
                      <div class="carousel-inner">
                        <?php foreach ($group as $index => $src): ?>
                          <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="<?= htmlspecialchars($src) ?>" class="d-block w-100" alt="Foto <?= $index + 1 ?>">
                          </div>
                        <?php endforeach; ?>
                      </div>

                      <!-- Controlli -->
                      <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?= $modalId ?>" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      </button>
                      <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?= $modalId ?>" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      </button>
                    </div>
                    <div class="text-center p-3">
                      <a href="prova.php?id=<?= urlencode($viaggio_id); ?>" class="btn btn-primary">Descrizione</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Non ha ancora caricato foto delle tue esperienze.</p>
      <?php endif; ?>
    </div>

  </div>
</div>
<div class="profile-menu-wrapper">
    <img src="immagini/new-york-city.jpg" alt="Foto Profilo" class="profile-icon" onclick="toggleDropdown()" />
    <div class="dropdown-menu" id="dropdownMenu">
      <a href="pagina_profilo.php">Profilo</a>
      <a href="login.html">Logout</a>
      <a href=visualizza_viaggi.php>Home</a>
      <a href=notifiche.php>Notifiche</a>
    </div>
  </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


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
    const params = new URLSearchParams(window.location.search);
    const idUtente = params.get('id');
    fetch(`get_profilo.php?id=${idUtente}`)
      .then(res => res.json())
      .then(data => {
        if (!data.error) {
          document.querySelector('.profile-name').textContent = `${data.nome}, ${data.eta}`;
          document.querySelector('.profile-bio').innerHTML = data.bio.replace(/\n/g, '<br>');
          document.querySelector('.profile-pic').src =  data.immagine_profilo;
          document.querySelector('.photos-container').style.backgroundColor = data.colore_sfondo;
          document.querySelector('.profile-sidebar').style.backgroundColor = data.colore_sfondo;
          if (data.posizione_immagine !== undefined) {
            document.querySelector('.profile-pic').style.objectPosition = `${data.posizione_immagine}% center`;
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
          document.querySelector('.profile-icon').src =  data.immagine_profilo;
        } else {
          console.error(data.error);
        }
      });
    });
  



</script>
<script type="module" src="/js/index.js"></script>
</body>
</html>
