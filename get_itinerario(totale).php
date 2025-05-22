<?php
require_once 'connessione.php'; // Connessione al DB
$viaggio_id = $_GET['id'] ?? null;

$query = "SELECT * FROM itinerari i join viaggi on viaggi.id=i.viaggio_id WHERE viaggio_id = $1";
$result = pg_query_params($dbconn, $query, [ $viaggio_id]);
$itinerario = pg_fetch_assoc($result);

$query_lat = "SELECT latitudine, longitudine from viaggi  WHERE id = $1";
$result_1 = pg_query_params($dbconn, $query, [ $viaggio_id]);
$latlon = pg_fetch_assoc($result_1); 

$luoghi = json_decode($itinerario['luoghi'], true);
$lat = floatval($latlon['latitudine']);
$lon = floatval($latlon['longitudine']);
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($itinerario['nome_itinerario']) ?></title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>#map { height: 100vh; }</style>
    <style>
    @font-face {
      font-family: 'CustomFont';
      src: url('../font/8e78142e2f114c02b6e1daaaf3419b2e.woff2') format('woff2');
      font-display: swap;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      display: flex;
      flex-direction: column;
      height: 100vh;
      font-family: 'CustomFont', sans-serif;
      background-color: #f5f1de;
      color: rgb(8, 7, 91);
    }

    @media (min-width: 768px) {
      body {
        flex-direction: row;
      }
    }

    #sidebar {
      width: 100%;
      max-width: 100%;
      background-color: #f5f1de;
      padding: 24px;
      border-right: 2px solid #ddd;
      z-index: 1000;
      overflow-y: auto;
    }

    @media (min-width: 768px) {
      #sidebar {
        width: 30%;
        max-width: 400px;
        height: 100vh;
      }
    }

    #sidebar h2,
    #sidebar h3 {
      margin-bottom: 16px;
      font-size: 1.4rem;
      color: rgb(13, 10, 143);
    }

    #sidebar input[type="text"] {
      width: 100%;
      padding: 10px 14px;
      border: 2px solid rgb(8, 7, 91);
      border-radius: 6px;
      font-size: 1rem;
      background-color: white;
      color: rgb(12, 11, 92);
      font-family: sans-serif
    }

    #sidebar button {
      margin-top: 10px;
      width: 100%;
      padding: 10px;
      background-color:  #2e5a80;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-family: Arial, Helvetica, sans-serif;
    }

    #sidebar button:hover {
      background-color: #2e5a80;
    }

    #placesList {
      list-style: none;
      padding: 0;
      margin-top: 20px;
    }

    li {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 4px 0;
      border-bottom: 1px solid #ccc;
      font-size: 1rem;
      color: rgb(8, 7, 91);
      gap: 8px;
    }


    #map {
      flex: 1;
      height: 300px;
    }

    @media (min-width: 768px) {
      #map {
        height: 100vh;
      }
    }
    </style>
</head>
<body>
    
    <div id="sidebar">
    <h1><?= htmlspecialchars($itinerario['nome_itinerario']) ?></h1>
    <h3 style= margin-top:20px;>Luoghi inseriti:</h3>
    <ul>
  <?php foreach ($luoghi as $luogo): ?>
    <li><?= htmlspecialchars($luogo) ?></li>
  <?php endforeach; ?>
</ul>
  </div>

  
  <div id="map"></div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const initialLat = <?= json_encode($lat) ?>;
    const initialLon = <?= json_encode($lon) ?>;
    console.log(initialLat, initialLon);
  const luoghi = <?= json_encode($luoghi) ?>;
  const map = L.map('map').setView([initialLat, initialLon], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  luoghi.forEach(nome => {
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(nome)}`)
      .then(res => res.json())
      .then(data => {
        if (data[0]) {
          const lat = parseFloat(data[0].lat);
          const lon = parseFloat(data[0].lon);
          L.marker([lat, lon]).addTo(map).bindPopup(nome);
        }
      })
      .catch(err => console.error("Errore geocoding:", err));
  });
</script>

</body>
</html>
