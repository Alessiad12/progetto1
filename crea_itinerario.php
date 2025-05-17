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
      height: 100vh;
      font-family: 'CustomFont', sans-serif;
      background-color: #f5f1de;
      color: rgb(8, 7, 91);
      overflow: hidden;
    }

    #sidebar {
      width: 30%;
      min-width: 280px;
      background-color: #f5f1de;
      padding: 24px;
      border-right: 2px solid #ddd;
      z-index: 1000;
      overflow-y: auto;
    }

    #sidebar h2, #sidebar h3 {
      margin-bottom: 16px;
      font-size: 1.4rem;
      color: rgb(8, 7, 91);
    }

    #sidebar input[type="text"] {
      width: 100%;
      padding: 10px 14px;
      border: 2px solid rgb(8, 7, 91);
      border-radius: 6px;
      font-size: 1rem;
      background-color: white;
      color: rgb(8, 7, 91);
      font-family: 'CustomFont', sans-serif;
    }

    #sidebar button {
      margin-top: 10px;
      width: 100%;
      padding: 10px;
      background-color: rgb(8, 7, 91);
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-family: 'CustomFont', sans-serif;
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
      padding: 8px 0;
      border-bottom: 1px solid #ccc;
      font-size: 1rem;
      color: rgb(8, 7, 91);
    }

    .remove-btn {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.9rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .remove-btn:hover {
      background-color: #b02a37;
    }

    #map {
      flex: 1;
    }
  </style>
</head>
<body>
  <div id="sidebar">
    <h2>Inserisci un luogo</h2>
    <input type="text" id="placeInput" placeholder="Es. Colosseo, Roma">
    <button onclick="addPlace()">Aggiungi</button>
    
    <h3>Luoghi inseriti</h3>
    <ul id="placesList"></ul>
  </div>
  <div id="map"></div>

  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const map = L.map('map').setView([41.9028, 12.4964], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const markers = []; // Array per tenere traccia dei marker

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

            // Crea elemento lista
            const li = document.createElement('li');
            li.textContent = place;

            // Bottone per rimuovere
            const btn = document.createElement('button');
            btn.textContent = 'Rimuovi';
            btn.className = 'remove-btn';
            btn.onclick = () => {
              map.removeLayer(marker);      
              li.remove();                 
            };

            li.appendChild(btn);
            document.getElementById('placesList').appendChild(li);

            // Salva marker
            markers.push({ name: place, marker: marker });

            input.value = '';
          } else {
            alert("Luogo non trovato.");
          }
        });
    }
  </script>
</body>
</html>
