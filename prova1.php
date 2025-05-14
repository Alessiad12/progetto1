<?php
$API_KEY = '5ae2e3f221c38a28845f05b69cb3128974c2267f0934fc3c5a60f70c';

// Esempio: coordinata da tabella viaggi
$lat = $_GET['lat'] ?? 51.48933350;
$lon = $_GET['lon'] ?? -0.14405510;

$radius = 2000; // Raggio in metri
$limit = 300; // Limita a 50 risultati
$kinds = "cultural"; // Luoghi culturali e cibo

$url = "https://api.opentripmap.com/0.1/en/places/radius?radius=$radius&lon=$lon&lat=$lat&format=json&limit=$limit&kinds=$kinds&apikey=$API_KEY";
$response = file_get_contents($url);
$luoghi = json_decode($response, true);

// Ordinare per popolarità se il campo rate è presente
usort($luoghi, function($a, $b) {
    return $b['rate'] <=> $a['rate']; // Ordinamento decrescente
});

// Prendi solo i primi 10 più popolari
$top_luoghi = array_slice($luoghi, 0, 300);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Itinerario più Popolare</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { margin:0; display: flex; height: 100vh; font-family: Arial; }
        #lista { width: 30%; padding: 20px; overflow-y: scroll; background: #f0f0f0; }
        #mappa { flex: 1; }
        .luogo { margin-bottom: 15px; background: #fff; padding: 10px; border-radius: 5px; }
        .luogo h3 { margin: 0 0 5px; }
    </style>
</head>
<body>
<div id="lista">
    <h2>Itinerario Popolare</h2>
    <?php foreach ($top_luoghi as $luogo): 
        $name = $luogo['name'] ?? '';
        $kinds = $luogo['kinds'] ?? '';
        if (!$name) continue;
    ?>
        <div class="luogo">
            <h3><?= htmlspecialchars($name) ?></h3>
            <p><small><?= htmlspecialchars($kinds) ?></small></p>
        </div>
    <?php endforeach; ?>
</div>
<div id="mappa"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const mappa = L.map('mappa').setView([<?= $lat ?>, <?= $lon ?>], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mappa);

    const luoghi = <?= json_encode($top_luoghi) ?>;
    luoghi.forEach(l => {
        if (!l.point || !l.name) return;
        L.marker([l.point.lat, l.point.lon])
            .addTo(mappa)
            .bindPopup(`<b>${l.name}</b><br>${l.kinds}`);
    });
</script>
</body>
</html>
