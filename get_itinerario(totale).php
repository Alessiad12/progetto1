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
    body { overflow: hidden; }
    .hero {
      height: 200px;
      background: url('<?= htmlspecialchars($row['sfondo'] ?: 'immagini/default-bg.jpg') ?>') center/cover no-repeat;
      position: relative;
    }
    .hero::after {
      content:""; position:absolute; inset:0;
      background: rgba(0,0,0,0.3);
    }
    .hero .card {
      position: absolute; bottom: -24px; left: 50%;
      transform: translateX(-50%);
      width: 90%; max-width: 600px;
      z-index: 1;
    }
    #map { height: calc(100vh - 236px); }
    .sidebar { height: calc(100vh - 236px); overflow-y: auto; }
  </style>
</head>
<body>
  <div class="hero mb-5">
    <div class="card shadow">
      <div class="card-body text-center">
        <h3 class="card-title mb-0"><?= htmlspecialchars($row['destinazione'] ?? '') ?></h3>
      </div>
    </div>
  </div>

  <div class="container-fluid px-0">
    <div class="row gx-0">
      <nav class="col-12 col-md-4 col-lg-3 bg-white sidebar p-3">
        <h5 class="mb-3">Luoghi visitati</h5>
        <ul class="list-group">
          <?php foreach($luoghi as $i=>$l): ?>
            <li class="list-group-item d-flex align-items-center" data-idx="<?= $i ?>">
              <span class="badge bg-primary me-3"><?= $i+1 ?></span>
              <?= htmlspecialchars($l) ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
      <div class="col-12 col-md-8 col-lg-9 p-0 map">
        <div id="map"></div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    // Initialize map
    const lat = <?= $row['latitudine'] ?: 0 ?>,
          lon = <?= $row['longitudine'] ?: 0 ?>;
    const map = L.map('map').setView([lat, lon], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Add markers
    const luoghi = <?= json_encode($luoghi) ?>;
    const markers = [];
    luoghi.forEach((nome, i) => {
      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(nome)}`)
        .then(r=>r.json()).then(data=>{
          if(!data[0]) return;
          const m = L.marker([data[0].lat, data[0].lon])
            .addTo(map)
            .bindPopup(nome);
          markers.push(m);
        });
    });

    // List click
    document.querySelectorAll('.list-group-item').forEach(item=>{
      item.onclick = ()=>{
        const idx = +item.dataset.idx;
        const m = markers[idx];
        if(!m) return;
        map.setView(m.getLatLng(), 14, { animate:true });
        m.openPopup();
      };
    });
  </script>
</body>
</html>
