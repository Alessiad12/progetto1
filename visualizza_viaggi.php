<?php
session_start();
require_once 'connessione.php'; // Connessione al DB

if (!isset($_SESSION['id_utente']) || !isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}

$id_utente = $_SESSION['id_utente'];

// Ottieni il colore di sfondo dell'utente
$sql = "SELECT colore_sfondo, immagine_profilo FROM profili WHERE id = $1";
$res = pg_query_params($dbconn, $sql, [$id_utente]);
if (!$res) {
    die("Errore nella query del profilo: " . pg_last_error($dbconn));
}

$row = pg_fetch_assoc($res);
$colore_sfondo = $row['colore_sfondo'] ?? '#fef6e4';
$immagine_profilo = $row['immagine_profilo'] ?? 'immagini/default.png';
// Recupera tutti i viaggi
// Recupera le preferenze dell’utente
$sqlPref = "SELECT * FROM preferenze_utente_viaggio WHERE utente_id = $1 ORDER BY data_partenza DESC LIMIT 1";
$resPref = pg_query_params($dbconn, $sqlPref, [$id_utente]);

if (!$resPref || pg_num_rows($resPref) === 0) {
    die("Nessuna preferenza trovata per l’utente.");
}

$pref = pg_fetch_assoc($resPref);


if (preg_match('/^\s*(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)\s*€?\s*$/', $pref['budget'], $m)) {
    // budget nel formato "100-500€"
    $budget_min = floatval($m[1]);
    $budget_max = floatval($m[2]);
} else {
    // budget singolo, es. "300€" → margine ±20%
    $budget_num = floatval(preg_replace('/[^\d.]/', '', $pref['budget']));
    $budget_min = $budget_num * 0.8;
    $budget_max = $budget_num * 1.2;
}



// Calcoli di flessibilità
$data_partenza_da = date('Y-m-d', strtotime($pref['data_partenza'] . ' -15 days'));
$data_partenza_a = date('Y-m-d', strtotime($pref['data_partenza'] . ' +15 days'));
$data_ritorno_da = date('Y-m-d', strtotime($pref['data_ritorno'] . ' -15 days'));
$data_ritorno_a = date('Y-m-d', strtotime($pref['data_ritorno'] . ' +15 days'));

// Gestione del budget come numero

// Query dei viaggi compatibili
$query = "
SELECT *
FROM viaggi v
WHERE
  destinazione ILIKE '%' || $1 || '%' AND
  tipo_viaggio ILIKE '%' || $2 || '%' AND
  data_partenza BETWEEN $3 AND $4
  AND data_ritorno BETWEEN $5 AND $6
  AND budget BETWEEN $7 AND $8
  -- altri filtri…
  AND NOT EXISTS (
      SELECT 1
      FROM viaggi_utenti vu
      WHERE vu.viaggio_id = v.id
        AND vu.user_id    = \$9
    )

ORDER BY data_partenza ASC
LIMIT 20
";

$user_id = $_SESSION['id_utente'];

$params = [
    $pref['destinazione'],
    $pref['tipo_viaggio'],
    $data_partenza_da,
    $data_partenza_a,    
    $data_ritorno_da,
    $data_ritorno_a,
    $budget_min,
    $budget_max,
    $user_id
];


$result = pg_query_params($dbconn, $query, $params);
if (!$result) {
    die("Errore nella query dei viaggi compatibili: " . pg_last_error($dbconn));
}

$viaggi = [];
while ($row = pg_fetch_assoc($result)) {
    $viaggi[] = $row;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Menu Profilo</title>
  <link rel="stylesheet" href="css/visualizza_viaggi.css">
</head>
<body style="background-color: <?= htmlspecialchars($colore_sfondo) ?>;">
<div class="card-container" id="cardContainer">
<?php foreach ($viaggi as $viaggio): 
    $immagine = $viaggio['foto'] ?? null;

    if (!$immagine) {
      $tipo = strtolower(trim($viaggio['tipo_viaggio'])); // Normalizza

      switch ($tipo) {
        case 'spiaggia':
          $immagine = 'https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg';
          break;
        case 'musei':
          $immagine = 'https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg';
          break;
        case 'ristoranti':
          $immagine = 'https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg';
          break;
        case 'natura':
          $immagine = 'https://i.pinimg.com/736x/89/08/9c/89089cd5fbe7662e5a35beb13eb18edf.jpg';
          break;
        default:
          $immagine = 'https://i.pinimg.com/736x/5d/e7/25/5de7255c7ef36023e25ac38fe9fec211.jpg';
          break;

          
      }
      $sqlUp = "UPDATE viaggi SET foto = $1 WHERE id = $2";
      $resUp = pg_query_params($dbconn, $sqlUp, [$immagine, $viaggio['id']]);
      if (!$resUp) {
          error_log("Errore aggiornamento foto viaggio {$viaggio['id']}: " . pg_last_error($dbconn));
}

      
    }

?>
<div class="card" id="card-<?php echo $viaggio['id']; ?>" style="background-image: url('<?= htmlspecialchars($immagine) ?>');"
    data-viaggio-id="<?php echo $viaggio['id']; ?>">
      <div class="card-content">
        <h2><?php echo $viaggio['destinazione']; ?></h2>
        <p class="destination">Destinazione: <?php echo $viaggio['destinazione']; ?></p>
        <p><?php echo $viaggio['descrizione']; ?></p>
        <ul class="info-list">
          <li><strong>Data partenza:</strong> <?php echo $viaggio['data_partenza']; ?></li>
          <li><strong>Data ritorno:</strong> <?php echo $viaggio['data_ritorno']; ?></li>
          <li><strong>Budget:</strong> <?php echo $viaggio['budget']; ?></li>
          <li><strong>Tipo di viaggio:</strong> <?php echo $viaggio['tipo_viaggio']; ?></li>
          <li><strong>Lingua:</strong> <?php echo $viaggio['lingua']; ?></li>
          <li><strong>Compagnia ideale:</strong> <?php echo $viaggio['compagnia']; ?></li>
          <li><strong>Descrizione:</strong> <?php echo $viaggio['descrizione']; ?></li>
        </ul>

        <div class="card-footer">
          <span class="budget">Budget: <?php echo $viaggio['budget']; ?></span>
          <span class="date">Partenza: <?php echo $viaggio['data_partenza']; ?></span>
        </div>
        <div class="componenti-wrapper"></div>
        </div>
      </div>
<?php endforeach; ?>
</div>
<div class="profile-menu-wrapper">
  <img src="<?= htmlspecialchars($immagine_profilo) ?>" alt="Foto Profilo" class="profile-icon"  />
  <div class="dropdown-menu" id="dropdownMenu" >
          <a href="pagina_profilo.php">Profilo</a>
          <a href="login.html">Logout</a>
  </div>
</div>

<script src="js/visualizza_viaggi.js"></script>
<script> document.querySelectorAll('.card').forEach(card => {
  enableMouseSwipe(card);
  enableSwipe(card);
});
</script>
</body>
</html>
