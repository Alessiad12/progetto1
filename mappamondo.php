<?php
$viaggi = [
  ["lat" => -13.1631, "lon" => -72.545, "nome" => "Machu Picchu"],
  ["lat" => 48.8566, "lon" => 2.3522, "nome" => "Parigi"],
  // Aggiungi altri viaggi
];
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Mappa Viaggi</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="/css/style_index.css" />
  
    <style>
            .profile-picture-container {
display: flex;
justify-content: center;
align-items: center;
width: 200px;
height: 260px;
border-radius: 10px;
overflow: hidden;
box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
background-color: #f0f0f0;
margin-bottom: 18px;
}
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
  }

  #map {
    height: 100%;
    width: 100%;
    border-radius: 0;
    box-shadow: none;
  }
</style>
</head>
<body>

  <div id="map"></div>
  <div class="profile-toggle" onclick="toggleProfileContainer()">
    <img src="/uploads/<?= htmlspecialchars($immagine_profilo) ?>" alt="Foto Profilo" class="profile-icon">

  </div>

<div class="profile-container">
    <div class="profile-header">
      <div class="profile-picture-container">
        <img src="/uploads/<?= htmlspecialchars($immagine_profilo) ?>" alt="Foto Profilo" class="profile-pic">

      </div>
      <div class="profile-info">
        <h1 class="profile-name">Mario Rossi, 35</h1>
        <p class="profile-bio">
          Appassionato di esperienze uniche e avventure.<br>
          Amo scoprire nuove culture, cibi e paesaggi: ogni viaggio è un’opportunità per crescere e condividere.
        </p>
        
        <button class="companions-toggle"> Compagni di viaggio</button>
        <button class="viaggi-toggle" onclick="caricaViaggi()">Modifica viaggi</button>
      </div>

        </div>
    </div>


  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    // Crea la mappa centrata sull’equatore
    const map = L.map('map').setView([0, 0], 2);

    // Aggiungi layer gratuito OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    // Array dei viaggi da PHP a JS
    const viaggi = <?= json_encode($viaggi) ?>;

    const viaggioIcon = L.icon({
  iconUrl: '/immagini/pin-viaggio.png', // cambia con il percorso corretto della tua icona
  iconSize: [32, 32],
  iconAnchor: [16, 32],
  popupAnchor: [0, -32]
});

// Aggiungi i marker con link al viaggio
viaggi.forEach((v, i) => {
  L.marker([v.lat, v.lon], { icon: viaggioIcon })
    .addTo(map)
    .on('click', () => {
      window.location.href = `viaggio.php?id=${i}`; // cambia 'i' con v.id se hai ID veri
    })
    .bindTooltip(v.nome, { permanent: false, direction: 'top' });
});

    // Mostra/nasconde profilo
function toggleProfileContainer() {
  document.querySelector('.profile-container').classList.toggle('active');
}

// Chiudi cliccando fuori
window.onclick = (e) => {
  if (!e.target.closest('.profile-toggle') && document.querySelector('.profile-container').classList.contains('active')) {
    document.querySelector('.profile-container').classList.remove('active');
  }
};
    // Carica dati profilo all'avvio
document.addEventListener('DOMContentLoaded', () => {
  fetch('/profilo.php')
    .then(res => res.json())
    .then(data => {
      if (!data.error) {
        document.querySelector('.profile-name').textContent = `${data.nome}, ${data.eta}`;
        document.querySelector('.profile-bio').innerHTML = data.bio.replace(/\n/g, '<br>');
        document.querySelector('.profile-pic').src =  data.immagine_profilo;
        document.querySelector('.profile-icon').src =   data.immagine_profilo;
        document.querySelector('.profile-container').style.backgroundColor = data.colore_sfondo;
        if (data.posizione_immagine !== undefined) {
          document.querySelector('.profile-pic').style.objectPosition = `${data.posizione_immagine}% center`;
          document.querySelector('.profile-icon').style.objectPosition = `${data.posizione_immagine}% center`;

        }
      } else {
        console.error(data.error);
      }
    });
});
  </script>

</body>
</html>
