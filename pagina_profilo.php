<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])) {
    header('Location: login.php');
    exit;
}
$utente_id = intval($_SESSION['id_utente']);

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


// --- Conta compagni: utenti distinti che hanno condiviso almeno un tuo viaggio ---
$sql = <<<SQL
SELECT COUNT(DISTINCT vt2.utente_id) AS compagni
FROM viaggi_terminati vt2
WHERE vt2.viaggio_id IN (
    SELECT vt1.viaggio_id
    FROM viaggi_terminati vt1
    WHERE vt1.utente_id = $1
)
AND vt2.utente_id <> $1
SQL;
$res = pg_query_params($dbconn, $sql, [ $utente_id ]);
$row = pg_fetch_assoc($res);
$n_compagni = $row['compagni'] ?? 0;


?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profilo Viaggiatore</title>
  <link rel="stylesheet" href="css/style_index.css">
  <link rel="stylesheet" href="css/style_pagina_profilo.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

  </style>
</head>
<body>
<div class="page-wrapper">

  <!-- SIDEBAR PROFILO -->
  <div class="profile-sidebar">
    <div class="profile-sidebar-container">
      <img class="profile-pic" src="immagini/new-york-city.jpg" alt="Profilo">
    </div>
    <h2 class="profile-name">Mario Rossi, 35</h2>
    <p class="profile-bio">Viaggiatore curioso.<br>Amo conoscere culture e paesaggi.</p>
    <div class="profile-stats">
      <div class="stat-card">
        Compagni<br><span class="profile-compagni"><?= intval($n_compagni) ?></span>
      </div>
      <div class="stat-card">
        Viaggi<br><span class="profile-viaggi"><?= htmlspecialchars($n_viaggi) ?></span>
      </div>
    </div>
    <button onclick="window.location.href='modifica_profilo.php'">Modifica profilo</button>
    <button onclick="window.location.href='/crea_viaggio.php'">Crea viaggio</button>
  </div>

  <!-- AREA CONTENUTI -->
  <div class="content-area">

    <!-- MAPPA -->
    <div class="map-container" onclick="espandiMappa()">
      <iframe src="mappamondo.php"></iframe>
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
  document.querySelectorAll('.thumbnail').forEach(thumbnail => {
    thumbnail.addEventListener('click', function () {
      const groupId = this.getAttribute('data-group');
      const gallery = document.getElementById('group-' + groupId);
      gallery.style.display = gallery.style.display === 'none' ? 'block' : 'none';
    });
  });
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="/js/index.js"></script>
</body>
</html>
