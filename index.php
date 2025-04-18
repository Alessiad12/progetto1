<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])||!isset($_SESSION['user']) ) { 
  header('Location:/login.php'); 
  exit; 
}

$u = $_SESSION['id_utente'];

// Leggi preferenze dell'utente
$r = pg_query_params($dbconn,
  "SELECT vacanza, tipo_vacanza FROM utenti WHERE email = $1", [$u]
);
$pref = pg_fetch_assoc($r);

if (!$pref) {
    echo "Errore nel recupero delle preferenze.";
    exit;
}

// Recupera i viaggi che corrispondono alle preferenze
$r2 = pg_query_params($dbconn,
  "SELECT v.foto AS photo_path, u.nome
   FROM viaggi v JOIN utenti u ON u.id = v.utente
   WHERE v.utente = $1
     AND v.vacanza = $2
     AND v.scopo = $3",
  [$u, $pref['vacanza'], $pref['tipo_vacanza']]
);

$matches = pg_fetch_all($r2) ?: [];
?>
<!DOCTYPE html>
<html lang="en">        
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Travel Match</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/brands.min.css">
</head>
<body>
  <div class="grid grid-cols-2 gap-6 p-4">
  <?php if (empty($matches)): ?>
    <p>Nessun viaggio disponibile.</p>
  <?php else: foreach($matches as $m): ?>
    <div class="bg-white p-4 rounded-lg shadow">
      <img src="/uploads/<?=htmlspecialchars($m['photo_path'])?>"
           class="w-full h-40 object-cover rounded mb-2" />
      <h3 class="font-semibold"><?=htmlspecialchars($m['nome'])?></h3>
    </div>
  <?php endforeach; endif; ?>
  </div>
</body>
</html>