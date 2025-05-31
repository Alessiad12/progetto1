<?php
require 'connessione.php';
$viaggio_id = $_GET['id'] ?? null;
$sql = "
  SELECT i.luoghi, v.destinazione, v.foto AS sfondo, v.latitudine, v.longitudine
  FROM itinerari i
  JOIN viaggi v ON v.id = i.viaggio_id
  WHERE v.id = $1
";
$res = pg_query_params($dbconn, $sql, [$viaggio_id]);
$row = pg_fetch_assoc($res) ?: [];
$luoghi = json_decode($row['luoghi'] ?? '[]', true);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($row['destinazione'] ?? 'Itinerario') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .hero {
      height: 220px;
      background: url('<?= htmlspecialchars($row['sfondo'] ?: 'immagini/default-bg.jpg') ?>') center/cover no-repeat;
      position: relative;
    }
    .hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.4);
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
    }
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
    }
    .list-group-item:hover {
      background-color: #EAE1C1; 
    }
    .badge {
      background-color: #0A2342;
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
  </style>
</head>
<body>
  <div class="hero">
    <h1><?= htmlspecialchars($row['destinazione'] ?? '') ?></h1>
  </div>
  <div class="main">
    <aside class="sidebar">
      <h5>Luoghi visitati</h5>
      <ul class="list-group">
        <?php
        $luoghiPerGiorno = 2;
        $giorno = 1;
        foreach($luoghi as $i => $l):
          if ($i % $luoghiPerGiorno === 0):
            if ($i > 0) echo '</ul>';
            echo "<h6 class='mt-3 text-primary fw-semibold'>Giorno $giorno</h6><ul class='list-group'>";
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
    <div id="map"></div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
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

          const icon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="number-marker">${i + 1}</div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 30]
          });

          const m = L.marker(latlng, { icon }).addTo(map).bindPopup(nome);
          markers[i] = m;
          points[i] = latlng;

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

    document.querySelectorAll('.list-group-item').forEach(item => {
      item.addEventListener('click', () => {
        const idx = +item.dataset.idx;
        const m = markers[idx];
        if (!m) return;
        map.setView(m.getLatLng(), 14, { animate: true });
        m.openPopup();
        let jump = 0;
        const interval = setInterval(() => {
          const icon = m._icon;
          if (icon) icon.style.transform = `translateY(${jump % 2 === 0 ? '-10px' : '0'})`;
          jump++;
          if (jump > 5) {
            clearInterval(interval);
            if (icon) icon.style.transform = '';
          }
        }, 100);
      });
    });
  </script>
</body>
</html>
