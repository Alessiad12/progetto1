<?php 
require_once 'connessione.php';
session_start();
if (!isset($_SESSION['id_utente']) || !isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}
$id_utente = $_SESSION['id_utente'];
$viaggio_id = isset($_GET['viaggio_id']) ? intval($_GET['viaggio_id']) : 0;

// Recupera destinazione, latitudine e longitudine
$query = "SELECT destinazione, latitudine, longitudine ,data_partenza, data_ritorno
          FROM viaggi 
          WHERE id = $1";
$res = pg_query_params($dbconn, $query, [$viaggio_id]);
$row = ($res ? pg_fetch_assoc($res) : false);

$destinazione = $row['destinazione'] ?? 'la tua destinazione';
$lat = floatval($row['latitudine'] ?? 41.9028);
$lon = floatval($row['longitudine'] ?? 12.4964);

$data_partenza   = $row['data_partenza']  ?? '';
$data_ritorno    = $row['data_ritorno']   ?? '';

?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Itinerario per <?= htmlspecialchars($destinazione) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <!-- Bootstrap (solo per grid, non per il tema di default) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- FontAwesome (icona rimuovi) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* =========================
       1) PALETTE & RESET
       ========================= */
    :root {
      --col-cream: #FDF7E3;
      --col-navy: #0A2342;
      --col-navy-light: #12315C;
      --col-bg: #f9f7ef;
      --glass-bg: rgba(255, 255, 255, 0.8);
      --glass-border: rgba(255, 255, 255, 0.4);
      --accent-shadow: rgba(0, 0, 0, 0.1);
    }
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    html, body {
      height: 100%;
      background: var(--col-bg);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--col-navy);
    }
    a {
      text-decoration: none;
    }

    /* =========================
       2) LAYOUT PRINCIPALE
       ========================= */
    .wrapper {
      display: flex;
      flex-direction: column;
      height: 100vh;
    }
    @media (min-width: 768px) {
      .wrapper {
        flex-direction: row;
      }
    }

    /* =========================
       3) SIDEBAR “GLASS CARD”
       ========================= */
    #sidebar {
      position: relative;
      width: 100%;
      max-width: 100vw;
      background: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-right: 1px solid var(--glass-border);
      box-shadow: 2px 0 10px var(--accent-shadow);
      padding: 2rem 1.5rem;
      overflow-y: auto;
      animation: slideInLeft 0.6s ease-out forwards;
    }
    @media (min-width: 768px) {
      #sidebar {
        width: 300px;
        max-width: 300px;
        height: 100vh;
        border-right: 1px solid var(--glass-border);
      }
    }
    #sidebar h2 {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 0.75rem;
      color: var(--col-navy-light);
    }
    #sidebar p {
      font-size: 0.95rem;
      margin-bottom: 1.5rem;
      color: #333;
    }
    #placeInput {
      width: 100%;
      padding: 10px 14px;
      border: 2px solid var(--col-navy);
      border-radius: 8px;
      font-size: 1rem;
      background-color: #fff;
      color: var(--col-navy);
      margin-bottom: 0.75rem;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    #placeInput:focus {
      outline: none;
      border-color: var(--col-navy-light);
      box-shadow: 0 0 5px rgba(18, 54, 110, 0.3);
    }
    #sidebar button {
      width: 100%;
      padding: 12px;
      margin-top: 0.5rem;
      background-color: var(--col-navy);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease,
                  box-shadow 0.3s ease;
    }
    #sidebar button:hover {
      background-color: var(--col-navy-light);
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    #placesList {
      list-style: none;
      padding: 0;
      margin-top: 1.5rem;
    }
    #placesList li {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 12px;
      border-radius: 8px;
      background: #fff;
      margin-bottom: 0.5rem;
      font-size: 1rem;
      color: var(--col-navy);
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
      animation: slideInRight 0.5s ease forwards;
      transition: background-color 0.2s ease, transform 0.1s ease;
    }
    #placesList li:hover {
      background-color: rgba(18, 54, 110, 0.05);
      transform: translateX(4px);
    }
    #placesList li i.fa-map-marker-alt {
      color: var(--col-navy);
      margin-right: 0.5rem;
      font-size: 1.1rem;
    }
    #placesList li button {
      background-color: transparent;
      border: 1px solid var(--col-navy);
      color: var(--col-navy);
      border-radius: 4px;
      padding: 4px 8px;
      font-size: 0.85rem;
      cursor: pointer;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    #placesList li button:hover {
      background-color: var(--col-navy);
      color: #fff;
    }

    /* =========================
       4) MAPPA + OVERLAY DATE
       ========================= */
    #map-wrapper {
      position: relative;
      flex: 1;
      height: 50vh; /* a mobile occupa 50vh */
      animation: fadeInMap 0.8s ease-in;
    }
    @media (min-width: 768px) {
      #map-wrapper {
        height: 100vh; /* da tablet in su, occupa 100vh */
      }
    }
    #map {
      width: 100%;
      height: 100%;
    }
    /* Glass-card per le date sopra la mappa */
    #date-container {
      position: absolute;
      top: 16px;
      left: 50%;
      transform: translateX(-50%);
      background: var(--glass-bg);
      padding: 8px 12px;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      z-index: 1000;
      display: flex;
      gap: 12px;
      align-items: center;
      font-size: 0.9rem;
      color: var(--col-navy);
      transition: box-shadow 0.3s ease;
    }
    #date-container:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    #date-container label {
      display: flex;
      flex-direction: column;
      font-weight: 500;
      transition: flex-basis 0.3s ease;
    }
    #date-container input[type="date"] {
      margin-top: 4px;
      font-size: 0.9rem;
      padding: 4px 6px;
      border: 1px solid #ccc;
      border-radius: 4px;
      color: var(--col-navy);
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    #date-container input[type="date"]:focus {
      outline: none;
      border-color: var(--col-navy-light);
      box-shadow: 0 0 4px rgba(18, 54, 110, 0.3);
    }

    /* =========================
       5) ANIMAZIONI
       ========================= */
    @keyframes slideInLeft {
      from {
        transform: translateX(-100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    @keyframes slideInRight {
      from {
        transform: translateX(50px);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    @keyframes fadeInMap {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    /* =========================
       6) RESPONSIVE FURTHER TWEAKS
       ========================= */
    @media (max-width: 576px) {
      #sidebar {
        padding: 1.2rem;
      }
      #sidebar h2 {
        font-size: 1.3rem;
      }
      #sidebar p {
        font-size: 0.9rem;
      }
      #placeInput {
        font-size: 0.9rem;
        padding: 8px 12px;
      }
      #sidebar button {
        font-size: 0.9rem;
        padding: 10px;
      }
      #placesList li {
        font-size: 0.95rem;
        padding: 8px 10px;
      }
      #date-container {
        top: auto;
        bottom: 10px;
        width: 90%;
        left: 50%;
        transform: translateX(-50%);
        flex-wrap: wrap;
      }
      #date-container label {
        width: 48%;
        margin-bottom: 6px;
      }
      #map-wrapper {
        height: 60vh;
      }
    }

  </style>
</head>

<body>
  <!-- =========================
       WRAPPER PRINCIPALE: SIDEBAR + MAPPA
       ========================= -->
  <div class="wrapper">

    <!-- =========
         SIDEBAR
         ========= -->
    <div id="sidebar">
      <h2>Itinerario per <?= htmlspecialchars($destinazione) ?></h2>
      <p>Aggiungi i luoghi da visitare e crea il tuo percorso personalizzato.</p>

      <input 
        type="text" 
        id="placeInput" 
        placeholder="Es. Torre Eiffel, Parigi"
      >
      <button onclick="addPlace()">Aggiungi luogo</button>
      <button 
        class="mt-2" 
        data-bs-toggle="modal" 
        data-bs-target="#modalSalva"
      >
        Salva Itinerario
      </button>

      <h4 class="mt-4">Luoghi inseriti</h4>
      <ul id="placesList"></ul>
    </div>

    <!-- =========
         MAPPA + DATE OVERLAY
         ========= -->
    <div id="map-wrapper">
      <div id="date-container">
        <label for="start-date">
          Partenza:
          <input 
            type="date" 
            id="start-date" 
            name="data_partenza" 
            value="<?= htmlspecialchars($data_partenza) ?>"
          >
        </label>
        <label for="end-date">
          Arrivo:
          <input 
            type="date" 
            id="end-date" 
            name="data_ritorno" 
            value="<?= htmlspecialchars($data_ritorno) ?>"
          >
        </label>
      </div>
      <div id="map"></div>
    </div>

  </div> <!-- /wrapper -->

  <!-- ========
       MODAL “SALVA ITINERARIO”
       ======== -->
  <div class="modal fade" id="modalSalva" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nome Itinerario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input 
            type="text" 
            class="form-control" 
            id="nomeItinerario" 
            placeholder="Inserisci un nome"
          >
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button 
            class="btn btn-primary" 
            onclick="salvaItinerario()" 
            data-bs-dismiss="modal"
          >Salva</button>
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
      const partenza = document.getElementById('start-date').value;
      const ritorno = document.getElementById('end-date').value;

      fetch('save_itinerario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
          nome, 
          luoghi, 
          utente_id: id_utente, 
          viaggio_id: viaggio_id,
          data_partenza: partenza,
          data_ritorno: ritorno
        })
      })
      .then(res => res.text())
      .then(response => {
        alert("Itinerario salvato con successo!");
        setTimeout(() => {
          window.location.href = 'termina_viaggio.php?viaggio_id=' + encodeURIComponent(viaggio_id);
      }, 1000);
        }) 
      .catch(err => {
        console.error("Errore nel salvataggio:", err);
        alert("Errore nel salvataggio.");
      });
    }
  </script>
</body>
</html>
