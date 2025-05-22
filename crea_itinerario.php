<?php
require_once 'connessione.php'; // Connessione al DB
session_start();
if (!isset($_SESSION['id_utente']) || !isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}
$id_utente = $_SESSION['id_utente'];
$viaggio_id = $_GET['viaggio_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Itinerario Interattivo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
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
<?php
  $lat = isset($_GET['lat']) ? floatval($_GET['lat']) : 41.9028;
  $lon = isset($_GET['lon']) ? floatval($_GET['lon']) : 12.4964;
?>

<body>
  <div id="sidebar">
    <h2>Inserisci un luogo</h2>
    <input type="text" id="placeInput" placeholder="Es. Colosseo, Roma">
    <button onclick="addPlace()">Aggiungi</button>
    <button onclick="salvaItinerario()">Salva Itinerario</button>
    
    <h3 style= margin-top:20px;>Luoghi inseriti:</h3>
    <ul id="placesList"></ul>
  </div>

  <div id="map"></div>

  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
  const id_utente = <?= json_encode($id_utente) ?>;
  const viaggio_id = <?= json_encode($viaggio_id) ?>;
</script>
  <script>
    const initialLat = <?= json_encode($lat) ?>;
    const initialLon = <?= json_encode($lon) ?>;
    const map = L.map('map').setView([initialLat, initialLon], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const markers = [];

    function addPlace() {
      const input = document.getElementById('placeInput');
      const place = input.value.trim();
      if (!place) return;

      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(place)}`)
        .then(res => res.json())
        .then(data => {
          if (data && data[0]) {
            const lat = parseFloat(data[0].lat);
            const lon = parseFloat(data[0].lon);
            const marker = L.marker([lat, lon]).addTo(map).bindPopup(place).openPopup();
            map.setView([lat, lon], 14);

            const li = document.createElement('li');
            li.textContent = place;

            const btn = document.createElement('button');
            btn.textContent = 'Rimuovi';
            btn.className = 'remove-btn';
            btn.onclick = () => {
              map.removeLayer(marker);
              li.remove();
            };

            li.appendChild(btn);
            document.getElementById('placesList').appendChild(li);
            markers.push({ name: place, marker: marker });
            input.value = '';
          } else {
            alert("Luogo non trovato.");
          }
        });
    }
    function salvaItinerario() {
  const nome = prompt("Inserisci un nome per l'itinerario:");
  if (!nome) return;

  const luoghi = markers.map(m => m.name);

  fetch('save_itinerario.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      nome: nome,
      luoghi: luoghi,
      utente_id: id_utente,
      viaggio_id: viaggio_id
    })
  })
  .then(res => res.text())
  .then(response => {
    alert("Itinerario salvato con successo!");
  })
  .catch(error => {
    console.error("Errore nel salvataggio:", error);
    alert("Errore nel salvataggio dell'itinerario.");
  });
}

  </script>
  
</body>
</html>
