<?php
require 'connessione.php';

if (!isset($_SESSION['id_utente']) || !isset($_SESSION['user'])) { 
    header('Location: /login.php?error=not_logged_in'); 
    exit; 
}

// Prendi l'ID del viaggio dalla query string
$id = $_GET['id'] ?? null;

if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    echo "<h1>ID del viaggio non valido o non fornito.</h1>";
    exit;
}

// Query per ottenere il viaggio con l'utente associato
$query = "SELECT v.*, u.nome AS nome_utente 
          FROM viaggi v 
          JOIN utenti u ON v.utente = u.id 
          WHERE v.id = $1";

$result = pg_query_params($dbconn, $query, [$id]);

if (!$result || pg_num_rows($result) === 0) {
    echo "<h1>Viaggio non trovato.</h1>";
    exit;
}

$viaggio = pg_fetch_assoc($result);

// Debug dei dati recuperati
// var_dump($viaggio); exit;

// Controlla che tutti i campi obbligatori siano presenti
if (empty($viaggio['vacanza']) || empty($viaggio['scopo']) || empty($viaggio['nome_utente'])) {
    echo "<h1>Tutti i campi obbligatori devono essere compilati.</h1>";
    exit;
}

// Gestione del campo foto opzionale
$viaggio['foto'] = $viaggio['foto'] ?? '/img/default.png';
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($viaggio['vacanza']) ?></title>
  <style>
    body {
      font-family: sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 20px;
    }
    .viaggio-container {
      background: white;
      max-width: 800px;
      margin: auto;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .viaggio-img {
      width: 100%;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .viaggio-info h1 {
      margin: 0 0 10px;
    }
    .viaggio-info p {
      margin: 5px 0;
    }
  </style>
</head>
<body>

<div class="viaggio-container">
  <img src="<?= htmlspecialchars($viaggio['foto']) ?>" alt="Foto Viaggio" class="viaggio-img">
  <div class="viaggio-info">
    <h1><?= htmlspecialchars($viaggio['vacanza']) ?></h1>
    <p><strong>Scopo:</strong> <?= htmlspecialchars($viaggio['scopo']) ?></p>
    <p><strong>Creato da:</strong> <?= htmlspecialchars($viaggio['nome_utente']) ?></p>
    <p><strong>Data:</strong> <?= !empty($viaggio['creazione']) ? date("d/m/Y H:i", strtotime($viaggio['creazione'])) : 'Data non disponibile' ?></p>
  </div>
</div>

<a href="/lista_viaggi.php" style="display: block; margin-top: 20px; text-align: center; color: #007BFF;">Torna alla lista dei viaggi</a>

</body>
</html>