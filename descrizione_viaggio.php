<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])) {
    header('Location: login.php');
    exit;
}

$utente_id = intval($_SESSION['id_utente']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID viaggio non valido.";
    exit;
}

$viaggio_id = intval($_GET['id']);

$sql = "
  SELECT *
  FROM viaggi_terminati
  WHERE viaggio_id = $1 
  ORDER BY data_creazione DESC
";
$res = pg_query_params($dbconn, $sql, [ $viaggio_id ]);
$row = pg_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Descrizione Viaggio</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f7f9fa;
      font-family: Arial, sans-serif;
      padding: 40px;
    }
    .container {
      max-width: 800px;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .foto img {
      width: 100%;
      margin-bottom: 15px;
      border-radius: 8px;
    }
  </style>
</head>
<body>
<p><strong>ID Viaggio Terminato:</strong> <?= htmlspecialchars($row['id']) ?></p>
<div class="container">

    <h2 class="mb-4">Descrizione del viaggio</h2>
    <p><strong>Data:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($viaggio['data_creazione']))) ?></p>
    <p><strong>Valutazione:</strong> <?= intval($viaggio['valutazione']) ?>/5</p>

    <h4 class="mt-4">Testo esperienza</h4>
    <p><?= nl2br(htmlspecialchars($viaggio['descrizione'])) ?></p>

    <h4 class="mt-4">Foto</h4>


</body>
</html>