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

  <!-- Bootstrap CSS -->
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
       STILI GENERALI
    ========================= */
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f1de;
    }

    /* Hero con immagine di sfondo */
    .hero {
      height: 220px;
      background: url('<?= htmlspecialchars($row['sfondo'] ?: 'immagini/default-bg.jpg') ?>')
                  center/cover no-repeat;
      position: relative;
    }
    .hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.4);
      z-index: 1;
    }
    /* Titolo grande centrato */
    @font-face {
      font-family: 'secondo_font';
      src: url('/font/8e78142e2f114c02b6e1daaaf3419b2e.woff2') format('woff2');
      font-display: swap;
    }

    .hero h1 {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(255, 255, 255, 0.8);
      padding: 0.75rem 2rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      color: #0A2342;
      font-size: 2.5rem;
      font-weight: 700;
      z-index: 2;
      font-family:'secondo_font', serif;
      white-space: nowrap;
    }

    /* Freccia “Torna indietro” */
    .back-btn {
      position: absolute;
      top: 12px;
      left: 12px;
      background: rgba(255,255,255,0.8);
      padding: 6px 10px;
      border-radius: 6px;
      text-decoration: none;
      color: #0A2342;
      font-size: 1rem;
      z-index: 3;              /* Più alto dell’overlay */
      display: flex;
      align-items: center;
      gap: 6px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: background-color 0.2s ease;
    }
    .back-btn:hover {
      background: rgba(255,255,255,1);
    }
    /* Forzo dimensione visibile al SVG */
    .back-btn svg {
      width: 16px;
      height: 16px;
      display: block;
      fill: #0A2342;
    }

    /* =========================
       LAYOUT PRINCIPALE
    ========================= */
    .main {
      display: grid;
      grid-template-columns: 300px 1fr;
      height: calc(100vh - 220px);
    }
    .sidebar {
      background-color: #f8f9fa;
      padding: 1.5rem;
      overflow-y: auto;
      border-right: 1px solid #ddd;
    }
    .sidebar h5 {
      margin-bottom: 1.2rem;
      font-weight: 700;
      color: #0A2342;
    }
    .sidebar h6 {
      margin-top: 1rem;
      margin-bottom: 0.5rem;
      color: #0A34D1;
      font-weight: 600;
    }
    .list-group-item {
      border: none;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      margin-bottom: 0.5rem;
      cursor: pointer;
      background-color: #fff;
      transition: background 0.2s;
      font-weight: 500;
      color: #0A2342;
      display: flex;
      align-items: center;
    }
    .list-group-item:hover {
      background-color: #EAE1C1;
    }
    .badge {
      background-color: #0A2342;
    }

    /* Contenitore mappa + box date */
    #map-container {
      position: relative;
      width: 100%;
      height: 100%;
    }
    #map {
      width: 100%;
      height: 100%;
    }
    .number-marker {
      background-color: #0A2342;
      color: white;
      font-weight: bold;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      text-align: center;
      line-height: 30px;
      box-shadow: 0 0 5px rgba(0,0,0,0.3);
    }
    /* Box date (in alto a destra sulla mappa) */
    .date-box {
      position: absolute;
      top: 16px;
      right: 16px;
      background: rgba(255,255,255,0.85);
      border-radius: 8px;
      padding: 8px 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      font-size: 0.9rem;
      color: #0A2342;
      line-height: 1.4;
      text-align: right;
      z-index: 1000;
    }

    /* =========================
       MEDIA QUERIES
    ========================= */
    @media (max-width: 992px) {
      .hero {
        height: 180px;
      }
      .hero h1 {
        font-size: 2rem;
        padding: 0.5rem 1.5rem;
      }
    }
    @media (max-width: 768px) {
      .hero {
        height: 150px;
      }
      .hero h1 {
        font-size: 1.6rem;
        padding: 0.4rem 1rem;
        bottom: 12px;
      }
      .main {
        display: flex;
        flex-direction: column;
        height: auto;
      }
      .sidebar {
        width: 100%;
        height: auto;
        border-right: none;
        border-bottom: 1px solid #ddd;
      }
      .sidebar h5 {
        font-size: 1.1rem;
        margin-bottom: 0.8rem;
      }
      .sidebar h6 {
        font-size: 1rem;
        margin-top: 0.8rem;
        margin-bottom: 0.4rem;
      }
      .list-group-item {
        font-size: 0.95rem;
        padding: 0.5rem 0.8rem;
      }
      #map-container {
        width: 100%;
        height: 300px;
      }
      #map {
        height: 100%;
      }
      .date-box {
        top: 12px;
        right: 12px;
        font-size: 0.8rem;
        padding: 6px 10px;
      }
    }
    @media (max-width: 576px) {
      .hero {
        height: 120px;
      }
      .hero h1 {
        font-size: 1.3rem;
        padding: 0.3rem 0.8rem;
        bottom: 8px;
      }
      .sidebar {
        padding: 1rem;
      }
      .sidebar h5 {
        font-size: 1rem;
        margin-bottom: 0.6rem;
      }
      .sidebar h6 {
        font-size: 0.95rem;
        margin-top: 0.6rem;
        margin-bottom: 0.3rem;
      }
      .list-group-item {
        font-size: 0.9rem;
        padding: 0.4rem 0.6rem;
      }
      .date-box {
        top: 8px;
        right: 8px;
        font-size: 0.75rem;
        padding: 4px 8px;
      }
    }
  </style>
</head>
<body>
  <!-- Hero + freccia di “Torna indietro” -->
  <div class="hero">
    <a href="prova.php?id=<?= urlencode($viaggio_id) ?>"
       class="back-btn"
       aria-label="Torna a pagina precedente">
      <!-- SVG che rappresenta la freccia indietro -->
      <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
        <path d="M8.707 12.707a1 1 0 0 1-1.414 0L2.586 7.999l4.707-4.707a1 1 0 0 1 1.414 1.414L5.414 7.999l3.293 3.293a1 1 0 0 1 0 1.415z"/>
        <path d="M14 7.5H3v1h11v-1z"/>
      </svg>
      
    </a>

    <h1><?= htmlspecialchars($row['destinazione'] ?? '') ?></h1>
  </div>

  <div class="main">
    <aside class="sidebar">
      <h5>Luoghi visitati</h5>
      <?php
        $luoghiPerGiorno = 2;
        $giorno = 1;
        foreach($luoghi as $i => $l):
          if ($i % $luoghiPerGiorno === 0):
            if ($i > 0) echo '</ul>';
            echo "<h6 class='mt-3'>Giorno $giorno</h6><ul class='list-group'>";
            $giorno++;
          endif;
      ?>
          <li class="list-group-item d-flex align-items-center" data-idx="<?= $i ?>">
            <span class="badge rounded-pill text-white me-2 px-3 py-2"><?= $i+1 ?></span>
            <?= htmlspecialchars($l) ?>
          </li>
      <?php endforeach; ?>
      </ul>
    </aside>

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
