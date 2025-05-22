<?php
require_once 'connessione.php'; // Connessione al DB
session_start();
if (!isset($_SESSION['id_utente']) || !isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}
$utente_id = $_SESSION['id_utente'];
$viaggio_id = isset($_GET['viaggio_id']) ? intval($_GET['viaggio_id']) : 0;


$query = "SELECT * FROM itinerari i join viaggi on viaggi.id=i.viaggio_id WHERE utente_id = $1 AND viaggio_id = $2";
$result = pg_query_params($dbconn, $query, [$utente_id, $viaggio_id]);
$itinerario = pg_fetch_assoc($result);

$luoghi = json_decode($itinerario['luoghi'], true);
$lat = floatval($itinerario['latitudine']);
$lon = floatval($itinerario['longitudine']);
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($itinerario['nome_itinerario']) ?></title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>#map { height: 100vh; }</style>
</head>
<body>
  <h1><?= htmlspecialchars($itinerario['nome_itinerario']) ?></h1>
  <div id="map"></div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
  const luoghi = <?= json_encode($luoghi) ?>;
  const map = L.map('map').setView([<?= $lat ?>, <?= $lon ?>], 5);

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
