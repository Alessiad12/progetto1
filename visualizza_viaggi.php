<?php
// Connessione al database PostgreSQL
$host = 'localhost';
$port = '5432';
$dbname = 'ConnessionePHP';
$user = 'postgres';
$password = 'html';

$dbconn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$dbconn) {
    die("Errore connessione DB: " . pg_last_error());
}

// Recupera tutti i viaggi
$query = "SELECT * FROM viaggi ORDER BY id DESC";
$result = pg_query($dbconn, $query);

$viaggi = [];
while ($row = pg_fetch_assoc($result)) {
    $viaggi[] = $row;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Scopri Viaggi</title>
  <style>
    body {
      font-family: sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: #eef2f3;
      margin: 0;
    }
    .card {
      width: 350px;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      background: white;
      position: absolute;
      transition: all 0.3s ease;
      text-align: center;
    }
    .card.hidden {
      display: none;
    }
    .buttons {
      margin-top: 15px;
    }
    .buttons button {
      padding: 10px 20px;
      margin: 0 10px;
      font-size: 18px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
    }
    .accept { background: #2ecc71; color: white; }
    .reject { background: #e74c3c; color: white; }
  </style>
</head>
<body>

<?php foreach ($viaggi as $index => $v): ?>
  <div class="card <?= $index !== 0 ? 'hidden' : '' ?>">
    <h2><?= htmlspecialchars($v['destinazione']) ?></h2>
    <p><strong>Data:</strong> <?= htmlspecialchars($v['data_partenza']) ?> → <?= htmlspecialchars($v['data_ritorno']) ?></p>
    <p><strong>Tipo:</strong> <?= htmlspecialchars($v['tipo_viaggio']) ?></p>
    <p><strong>Lingua:</strong> <?= htmlspecialchars($v['lingua']) ?></p>
    <p><strong>Compagnia:</strong> <?= htmlspecialchars($v['compagnia']) ?></p>
    <p><strong>Budget:</strong> <?= htmlspecialchars($v['budget']) ?></p>
    <p><strong>Descrizione:</strong><br> <?= nl2br(htmlspecialchars($v['descrizione'])) ?></p>
    <div class="buttons">
      <button class="reject" onclick="nextCard()">❌</button>
      <button class="accept" onclick="nextCard()">🩵</button>
    </div>
  </div>
<?php endforeach; ?>

<script>
  let current = 0;
  const cards = document.querySelectorAll('.card');

  function nextCard() {
    if (current < cards.length) {
      cards[current].classList.add('hidden');
      current++;
      if (current < cards.length) {
        cards[current].classList.remove('hidden');
      } else {
        document.body.innerHTML = '<h2 style="text-align:center;">Nessun altro viaggio da mostrare.</h2>';
      }
    }
  }
</script>

</body>
</html>
