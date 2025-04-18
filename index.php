<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location:/login.php'); exit; }
$u = $_SESSION['user_id'];

// leggi preferenze
$r = pg_query_params($dbconn,
  "SELECT dream_vacation, trip_purpose FROM utenti WHERE id=$1", [$u]
);
$pref = pg_fetch_assoc($r);

// recupera i viaggi matching
$r2 = pg_query_params($dbconn,
  "SELECT t.photo_path, u.nome
   FROM trips t JOIN utenti u ON u.id=t.user_id
   WHERE t.user_id<>$1
     AND t.vacation_type=$2
     AND t.purpose=$3",
  [$u, $pref['dream_vacation'], $pref['trip_purpose']]
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
<body>
<html>
