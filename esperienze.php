<?php
session_start();
require 'connessione.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
$uid = intval($_SESSION['user_id']);

// prendi tutte le esperienze dell’utente
$sql = "SELECT e.*, v.destinazione
        FROM esperienze e
        JOIN viaggi v ON e.viaggio_id = v.id
       WHERE e.utente_id = $1
       ORDER BY e.data_creazione DESC";
$res = pg_query_params($dbconn, $sql, [$uid]);
$exp = pg_fetch_all($res) ?: [];
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Le mie esperienze – Wanderlust</title>
  <link rel="stylesheet" href="css/style_index.css">
  <style>
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 2rem; }
    .card { background:#fff; border-radius:8px; padding:1rem; box-shadow:0 2px 5px rgba(0,0,0,0.1); }
    .stars { color: gold; }
    .photos { display:flex; gap:0.5rem; margin-top:0.5rem; }
    .photos img { width:60px; height:60px; object-fit:cover; border-radius:4px; }
  </style>
</head>
<body>
  <h1 style="padding:2rem; color:#0A2342;">Le mie esperienze</h1>
  <div class="grid">
    <?php if (empty($exp)): ?>
      <p style="grid-column:1/3; text-align:center;">Ancora nessuna esperienza terminata.</p>
    <?php else: ?>
      <?php foreach ($exp as $e): ?>
        <div class="card">
          <h2><?= htmlspecialchars($e['destinazione']) ?></h2>
          <p><?= nl2br(htmlspecialchars($e['descrizione'])) ?></p>
          <div class="stars">
            <?= str_repeat('★', $e['valutazione']) . str_repeat('☆', 5 - $e['valutazione']) ?>
          </div>
          <div class="photos">
            <?php for ($i = 1; $i <= 5; $i++): 
              $f = $e["foto{$i}"];
              if ($f): ?>
                <img src="<?= htmlspecialchars($f) ?>" alt="">
            <?php endif; endfor; ?>
          </div>
          <small style="color:#666;"><?= date('d/m/Y H:i', strtotime($e['data_creazione'])) ?></small>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>
