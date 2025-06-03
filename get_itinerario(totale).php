<?php
require 'connessione.php';
$viaggio_id = $_GET['id'] ?? null;

// Recupero anche data_partenza e data_ritorno
$sql = "
  SELECT i.luoghi,
         v.destinazione,
         v.foto AS sfondo,
         v.latitudine,
         v.longitudine,
         v.data_partenza,
         v.data_ritorno
  FROM itinerari i
  JOIN viaggi v ON v.id = i.viaggio_id
  WHERE v.id = \$1
";
$res = pg_query_params($dbconn, $sql, [$viaggio_id]);
$row = pg_fetch_assoc($res) ?: [];

$luoghi = json_decode($row['luoghi'] ?? '[]', true);

// Formatto le date
$data_partenza = $row['data_partenza']
                 ? date("d/m/Y", strtotime($row['data_partenza']))
                 : '';
$data_ritorno  = $row['data_ritorno']
                 ? date("d/m/Y", strtotime($row['data_ritorno']))
                 : '';
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($row['destinazione'] ?? 'Itinerario') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Bootstrap CSS (per grid e componenti di base) -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <!-- Leaflet CSS -->
  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet/dist/leaflet.css"
  />

  <style>
    /* =========================
       1) STILI GENERALI + PALETTE
       ========================= */
    :root {
      --col-cream: #FDF7E3;
      --col-navy: #0A2342;
      --col-navy-light: #12315C;
      --col-bg-light: #f5f1de;
      --col-card-bg: rgba(255,255,255,0.85);
      --col-border-light: #ddd;
    }

    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      background: var(--col-bg-light);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--col-navy);
    }

    /* =========================
       2) HERO (titolo + background)
       ========================= */
    .hero {
      position: relative;
      height: 240px;
      background: url('<?= htmlspecialchars($row['sfondo'] ?: 'immagini/default-bg.jpg') ?>')
                  center/cover no-repeat;
      display: flex;
      align-items: flex-end;
      justify-content: center;
    }
    .hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.3);
    }
    .hero h1 {
      position: relative;
      margin-bottom: 1rem;
      background: var(--col-card-bg);
      padding: 0.75rem 1.5rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      font-size: 2.25rem;
      font-weight: 700;
      font-family:'secondo_font', serif;
      color: var(--col-navy);
      white-space: nowrap;
    }

    /* Freccia “Torna indietro” */
    .back-btn {
      position: absolute;
      top: 12px;
      left: 12px;
      background: var(--col-card-bg);
      padding: 6px 10px;
      border-radius: 8px;
      text-decoration: none;
      color: var(--col-navy);
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 6px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: background-color 0.2s ease, transform 0.2s ease;
      z-index: 10;
    }
    .back-btn:hover {
      background: rgba(255,255,255,1);
      transform: translateX(-2px);
    }
    .back-btn svg {
      width: 16px;
      height: 16px;
      fill: var(--col-navy);
      flex-shrink: 0;
    }

    /* =========================
       3) LAYOUT PRINCIPALE
       ========================= */
    .main {
      display: grid;
      grid-template-columns: 320px 1fr;
      gap: 1rem;
      height: calc(100vh - 240px);
      margin: 1rem;
    }
    /* Per schermi piccoli (< 992px), trasformo in colonna */
    @media (max-width: 991px) {
      .main {
        display: flex;
        flex-direction: column;
        height: auto;
      }
    }

    /* =========================
       4) SIDEBAR “Luoghi visitati”
       ========================= */
    .sidebar {
      background-color: var(--col-card-bg);
      border: 1px solid var(--col-border-light);
      border-radius: 12px;
      padding: 1.5rem;
      overflow-y: auto;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      max-height: calc(100vh - 260px); /* spaziatura hero + margin */
    }
    .sidebar h5 {
      margin-bottom: 1rem;
      font-weight: 700;
      font-size: 1.25rem;
      color: var(--col-navy);
    }
    .sidebar h6 {
      margin-top: 1rem;
      margin-bottom: 0.5rem;
      color: var(--col-navy-light);
      font-weight: 600;
      font-size: 1rem;
    }
    .list-group-item {
      border: none;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      margin-bottom: 0.5rem;
      cursor: pointer;
      background-color: white;
      transition: background 0.2s, transform 0.1s;
      font-weight: 500;
      color: var(--col-navy);
      display: flex;
      align-items: center;
      gap: 0.75rem;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    .list-group-item:hover {
      background-color: #EAE1C1;
      transform: translateX(2px);
    }
    .list-group-item .badge {
      background-color: var(--col-navy);
      font-size: 0.9rem;
    }

    /* =========================
       5) MAPPA + BOX DATE
       ========================= */
    #map-container {
      position: relative;
      border: 1px solid var(--col-border-light);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    #map {
      width: 100%;
      height: 100%;
      min-height: 300px;
    }
    .number-marker {
      background-color: var(--col-navy);
      color: white;
      font-weight: bold;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      text-align: center;
      line-height: 30px;
      box-shadow: 0 0 5px rgba(0,0,0,0.3);
    }
    .date-box {
      position: absolute;
      top: 16px;
      right: 16px;
      background: var(--col-card-bg);
      border-radius: 8px;
      padding: 8px 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      font-size: 0.9rem;
      color: var(--col-navy);
      line-height: 1.4;
      text-align: right;
      z-index: 1000;
    }

    /* =========================
       6) MEDIA QUERIES AGGIUNTIVE
       ========================= */
    @media (max-width: 992px) {
      .hero {
        height: 200px;
      }
      .hero h1 {
        font-size: 2rem;
        padding: 0.6rem 1.2rem;
      }
      .sidebar {
        max-height: none;
        margin-bottom: 1rem;
      }
      #map-container {
        height: 400px;
      }
      .date-box {
        top: 12px;
        right: 12px;
      }
    }
    @media (max-width: 576px) {
      .hero {
        height: 160px;
      }
      .hero h1 {
        font-size: 1.6rem;
        padding: 0.4rem 0.8rem;
        bottom: 8px;
      }
      .back-btn {
        top: 8px;
        left: 8px;
        font-size: 0.9rem;
        padding: 5px 8px;
      }
      .hero h1 {
        white-space: normal;
      }
      #map-container {
        height: 300px;
      }
      .date-box {
        top: 8px;
        right: 8px;
        font-size: 0.8rem;
        padding: 6px 10px;
      }
      .sidebar {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>

  <!-- =========================
       HERO + FRECCIA “INDIETRO”
       ========================= -->
  <div class="hero">
    <a href="prova.php?id=<?= urlencode($viaggio_id) ?>"
       class="back-btn"
       aria-label="Torna a pagina precedente">
      <!-- Icona a forma di freccia a sinistra -->
      <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
        <path d="M8.707 12.707a1 1 0 0 1-1.414 0L2.586 7.999l4.707-4.707a1 1 0 0 1 1.414 1.414L5.414 7.999l3.293 3.293a1 1 0 0 1 0 1.415z"/>
        <path d="M14 7.5H3v1h11v-1z"/>
      </svg>
      
    </a>

    <h1><?= htmlspecialchars($row['destinazione'] ?? '') ?></h1>
  </div>

  <!-- =========================
       MAIN WRAPPER: SIDEBAR + MAPPA
       ========================= -->
  <div class="main">
    <!-- Sidebar “Luoghi visitati” -->
    <aside class="sidebar">
      <h5>Luoghi visitati</h5>
      <?php
        $luoghiPerGiorno = 2;
        $giorno = 1;
        foreach($luoghi as $i => $l):
          if ($i % $luoghiPerGiorno === 0):
            if ($i > 0) echo '</ul>';
            echo "<h6>Giorno $giorno</h6><ul class='list-group'>";
            $giorno++;
          endif;
      ?>
          <li class="list-group-item" data-idx="<?= $i ?>">
            <span class="badge rounded-pill text-white"><?= $i+1 ?></span>
            <?= htmlspecialchars($l) ?>
          </li>
      <?php endforeach; ?>
      </ul>
    </aside>

    <!-- Contenitore mappa + date -->
    <div id="map-container">
      <div id="map"></div>
      <div class="date-box">
        <strong>Partenza:</strong> <?= $data_partenza ?><br>
        <strong>Ritorno:</strong>  <?= $data_ritorno ?>
      </div>
    </div>
  </div>

  <!-- Script di Bootstrap e Leaflet -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    // Inizializzo la mappa
    const lat = <?= $row['latitudine'] ?: 0 ?>,
          lon = <?= $row['longitudine'] ?: 0 ?>;
    const map = L.map('map').setView([lat, lon], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const luoghi = <?= json_encode($luoghi) ?>;
    const markers = [];
    const points = [];

    luoghi.forEach((nome, i) => {
      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(nome)}`)
        .then(r => r.json()).then(data => {
          if (!data[0]) return;
          const latlng = [parseFloat(data[0].lat), parseFloat(data[0].lon)];

          // Marker numerato
          const icon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="number-marker">${i + 1}</div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 30]
          });

          const m = L.marker(latlng, { icon })
                      .addTo(map)
                      .bindPopup(nome);
          markers[i] = m;
          points[i] = latlng;

          // Quando ho tutti i punti, traccio la linea tratteggiata animata
          if (points.filter(Boolean).length === luoghi.length) {
            const polyline = L.polyline(points, {
              color: '#0A2342',
              weight: 4,
              opacity: 0.9,
              dashArray: '8,10'
            }).addTo(map);

            let offset = 0;
            setInterval(() => {
              offset = (offset + 1) % 18;
              polyline.setStyle({ dashOffset: offset });
            }, 100);

            map.fitBounds(polyline.getBounds(), { padding: [50, 50] });
          }
        });
    });

    // Click sulla lista per centrare il marker
    document.querySelectorAll('.list-group-item').forEach(item => {
      item.addEventListener('click', () => {
        const idx = +item.dataset.idx;
        const m = markers[idx];
        if (!m) return;
        map.setView(m.getLatLng(), 14, { animate: true });
        m.openPopup();

        // Piccola animazione di “salto” sul marker
        let jump = 0;
        const interval = setInterval(() => {
          const iconEl = m._icon;
          if (iconEl) iconEl.style.transform = `translateY(${jump % 2 === 0 ? '-10px' : '0'})`;
          jump++;
          if (jump > 5) {
            clearInterval(interval);
            if (iconEl) iconEl.style.transform = '';
          }
        }, 100);
      });
    });
  </script>
</body>
</html>
