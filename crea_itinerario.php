<?php 
require_once 'connessione.php';
session_start();
if (!isset($_SESSION['id_utente']) || !isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}
$id_utente = $_SESSION['id_utente'];
$viaggio_id = $_GET['viaggio_id'] ?? 0;

// Recupera destinazione
$query = "SELECT destinazione, latitudine, longitudine FROM viaggi WHERE id = $1";
$res = pg_query_params($dbconn, $query, [$viaggio_id]);
$row = pg_fetch_assoc($res);
$destinazione = $row['destinazione'] ?? 'la tua destinazione';
$lat = floatval($row['latitudine'] ?? 41.9028);
$lon = floatval($row['longitudine'] ?? 12.4964);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Itinerario per <?= htmlspecialchars($destinazione) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
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
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #f9f7ef, #e6e3d3);
      color: #0a2342;
    }

    @media (min-width: 768px) {
      body { flex-direction: row; }
    }

    #sidebar {
      width: 100%;
      max-width: 100%;
      background-color: #ffffffcc;
      backdrop-filter: blur(6px);
      padding: 2rem;
      border-right: 2px solid #ccc;
      box-shadow: 2px 0 10px rgba(0,0,0,0.05);
    }

    @media (min-width: 768px) {
      #sidebar {
        width: 30%;
        max-width: 400px;
        height: 100vh;
      }
    }

    #sidebar h2 {
      font-size: 1.5rem;
      font-weight: 600;
      color: #12366e;
      margin-bottom: 0.75rem;
    }

    #sidebar p {
      font-size: 0.95rem;
      margin-bottom: 1.5rem;
      color: #333;
    }

    #sidebar input[type="text"] {
      width: 100%;
      padding: 10px 14px;
      border: 2px solid #12366e;
      border-radius: 8px;
      font-size: 1rem;
      background-color: #fff;
      color: #12366e;
    }

    #sidebar button {
      margin-top: 12px;
      width: 100%;
      padding: 12px;
      background-color: #12366e;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s;
    }

    #sidebar button:hover {
      background-color: #0f2a55;
      transform: translateY(-2px);
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
      padding: 8px 0;
      border-bottom: 1px solid #ddd;
      font-size: 1rem;
      color: #0e204b;
      gap: 8px;
      animation: fadeIn 0.4s ease forwards;
    }

    li button {
      background-color: transparent;
      border: 1px solid #12366e;
      color: #12366e;
      border-radius: 4px;
      padding: 4px 8px;
      font-size: 0.85rem;
      cursor: pointer;
      transition: background-color 0.3s, color 0.3s;
    }

    li button:hover {
      background-color: #12366e;
      color: white;
    }

    #map {
      flex: 1;
      height: 300px;
    }

    @media (min-width: 768px) {
      #map { height: 100vh; }
    }
  </style>
</head>
<body>
  <div id="sidebar">
    <h2>Itinerario per <?= htmlspecialchars($destinazione) ?></h2>
    <p>Aggiungi i luoghi da visitare e crea il tuo percorso personalizzato.</p>

    <input type="text" id="placeInput" placeholder="Es. Torre Eiffel, Parigi">
    <button onclick="addPlace()">Aggiungi luogo</button>
    <button data-bs-toggle="modal" data-bs-target="#modalSalva">Salva Itinerario</button>

    <h4 class="mt-4">Luoghi inseriti</h4>
    <ul id="placesList"></ul>
  </div>

  <div id="map"></div>

  <!-- Modal per salvataggio -->
  <div class="modal fade" id="modalSalva" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nome Itinerario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control" id="nomeItinerario" placeholder="Inserisci un nome">
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button class="btn btn-primary" onclick="salvaItinerario()" data-bs-dismiss="modal">Salva</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const id_utente = <?= json_encode($id_utente) ?>;
    const viaggio_id = <?= json_encode($viaggio_id) ?>;
    const map = L.map('map').setView([<?= $lat ?>, <?= $lon ?>], 13);

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
            li.innerHTML = `<i class='fas fa-map-marker-alt me-2 text-primary'></i>${place}`;

            const btn = document.createElement('button');
            btn.textContent = 'Rimuovi';
            btn.onclick = () => {
              map.removeLayer(marker);
              li.remove();
              const idx = markers.findIndex(m => m.name === place && m.marker === marker);
              if (idx !== -1) markers.splice(idx, 1);
            };

            li.appendChild(btn);
            document.getElementById('placesList').appendChild(li);
            markers.push({ name: place, marker });
            input.value = '';
          } else {
            alert("Luogo non trovato.");
          }
        });
    }

    function salvaItinerario() {
      const nome = document.getElementById('nomeItinerario').value.trim();
      if (!nome) return alert("Inserisci un nome valido");

      const luoghi = markers.map(m => m.name);

      fetch('save_itinerario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nome, luoghi, utente_id: id_utente, viaggio_id: viaggio_id })
      })
      .then(res => res.text())
      .then(() => alert("Itinerario salvato con successo!"))
      .catch(err => {
        console.error("Errore nel salvataggio:", err);
        alert("Errore nel salvataggio.");
      });
    }
  </script>
</body>
</html>
