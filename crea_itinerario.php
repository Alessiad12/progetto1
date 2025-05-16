<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Itinerario Interattivo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    body { margin: 0; display: flex; height: 100vh; font-family: sans-serif; }
    #sidebar { width: 30%; padding: 20px; background: #f9f9f9; box-shadow: 2px 0 5px rgba(0,0,0,0.1); overflow-y: auto; }
    #map { flex: 1; }
    input, button { width: 100%; padding: 10px; margin-top: 10px; }
    ul { list-style: none; padding-left: 0; margin-top: 20px; }
    li { padding: 5px 0; border-bottom: 1px solid #ccc; display: flex; justify-content: space-between; align-items: center; }
    .remove-btn { background: red; color: white; border: none; padding: 5px 10px; cursor: pointer; }
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
