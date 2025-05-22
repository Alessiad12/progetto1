<?php
session_start();
require 'connessione.php';
if (!isset($_SESSION['id_utente'])) {
    header('Location: login.php');
    exit;
}

$utente_id = intval($_SESSION['id_utente']);
$viaggio_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
if (!$viaggio_id) {
    die("ID viaggio non valido.");
}

// 1) Carica i dettagli del viaggio
$sql = "
  SELECT v.foto1, v.foto2, v.foto3, v.foto4, v.foto5,
         vi.destinazione, vi.descrizione AS descrizione_viaggio
  FROM viaggi_terminati v
  JOIN viaggi vi ON vi.id = v.viaggio_id
  WHERE v.viaggio_id = $1
  LIMIT 1
";
$res = pg_query_params($dbconn, $sql, [$viaggio_id]);
if (!$res || pg_num_rows($res) === 0) {
    die("Viaggio non trovato.");
}
$trip = pg_fetch_assoc($res);

// Recupera percentuali dinamiche dal DB
$sqlPerc = "
SELECT 
  ROUND(AVG(natura)) AS natura,
  ROUND(AVG(relax)) AS relax,
  ROUND(AVG(monumenti)) AS monumenti,
  ROUND(AVG(cultura)) AS cultura,
  ROUND(AVG(nightlife)) AS nightlife
FROM viaggi_terminati
WHERE viaggio_id = $1
";
$resPerc = pg_query_params($dbconn, $sqlPerc, [$viaggio_id]);
if (!$resPerc || pg_num_rows($resPerc) === 0) {
    $percentuali = ['natura'=>0,'relax'=>0,'monumenti'=>0,'cultura'=>0,'nightlife'=>0];
} else {
    $percentuali = pg_fetch_assoc($resPerc);
}



// 2) Media valutazione
$sqlMedia = "
  SELECT ROUND(AVG(valutazione)::numeric,2) AS media
  FROM viaggi_terminati
  WHERE viaggio_id = $1
";
$r = pg_query_params($dbconn, $sqlMedia, [$viaggio_id]);
$media = ($r && pg_num_rows($r)) ? pg_fetch_result($r,0,'media') : 0;

// 3) Commenti
$sqlComm = "
  SELECT p.nome, p.immagine_profilo, v.descrizione, v.utente_id
  FROM viaggi_terminati v
  JOIN profili p ON p.id = v.utente_id
  WHERE v.viaggio_id = $1
  ORDER BY v.data_creazione DESC
";
$resComm = pg_query_params($dbconn, $sqlComm, [$viaggio_id]);
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dettaglio Viaggio – <?= htmlspecialchars($trip['destinazione']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @font-face {
    font-family: 'CustomFont';
    src: url('../font/8e78142e2f114c02b6e1daaaf3419b2e.woff2') format('woff2');
        font-display: swap;
    }
    @font-face {
        font-family: 'secondo_font';
        src: url('../font/Arimo.7ac02a544211773d9636e056e9da6c35.7.f8f199f09526f79e87644ed227e0f651.woff2') format('woff2');
        font-display: swap;
    }
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: 'CustomFont', sans-serif;
        text-align:left;
        overflow-x: hidden;
        position: relative;
        background-color: #f5f1de;

    }

    body { background:#f4f6f8;  }
    .hero { position:relative; width:100%; height:50vh; min-height:300px; overflow:hidden; }
    .hero img { width:100%; height:100%; object-fit:cover; }
    .hero-overlay { position:absolute; bottom:1rem; left:1rem; color:#fff; text-shadow:0 2px 6px rgba(0,0,0,0.6); }
    .hero-overlay h1 { font-size:clamp(1.8rem,6vw,2.8rem); margin:0; }
    .hero-overlay p { margin:.5rem 0; font-size:clamp(1rem,4vw,1.2rem); }
    .rating { font-size:1.1rem; color:#444; }
    .rating .star { color:gold; }
    .commenti .commento { border-bottom:1px solid #e0e0e0; padding:.75rem 0; }
    .commenti .commento:last-child { border-bottom:none; }
    .commento .avatar { width:40px; height:40px; border-radius:50%; object-fit:cover; margin-right:.75rem; }
    .gallery img { object-fit:cover; width:100%; height:200px; border-radius:8px; }
        .circle-container {
      display: inline-flex;
      flex-direction: column;
      align-items: center;
      margin: 15px;
    }

    .circle {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: conic-gradient(var(--color) calc(var(--percent) * 1%), #e0e0e0 0%);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .circle::before {
      content: '';
      width: 80%;
      height: 80%;
      background: #fff;
      border-radius: 50%;
      position: absolute;
    }

    .circle img {
      position: absolute;
      width: 40px;
      height: 40px;
      z-index: 1;
      filter: brightness(0) saturate(100%) invert(40%) sepia(100%) saturate(600%) hue-rotate(var(--hue,0deg));
    }

    .circle-label {
      margin-top: 10px;
      font-weight: 600;
      font-size: 0.9rem;
      text-align: center;
    }

    .circle-percent {
      margin-top: 5px;
      font-size: 0.85rem;
      color: var(--color);
      font-weight: bold;
    }

    @media (max-width:576px) { .gallery img { height:120px; } }
  </style>
</head>
<body>

  <!-- HERO -->
  <section class="hero mb-4">
    <img src="<?= htmlspecialchars($trip['foto1']) ?>" alt="Hero">
    <div class="hero-overlay">
      <h1><?= htmlspecialchars($trip['destinazione']) ?></h1>
      <p><?= htmlspecialchars($trip['descrizione_viaggio']) ?></p>
    </div>
  </section>

  <div class="container">

    <!-- VALUTAZIONE -->
    <div class="rating mb-4">
      <?php
        $filled = floor($media);
        for ($i=1; $i<=5; $i++) {
          if ($i <= $filled) {
            echo '<span class="star">★</span>';
          } elseif ($i === $filled+1 && $media - $filled >= 0.5) {
            echo '<span class="star">★</span>'; // mezza stella semplificata
          } else {
            echo '<span class="text-muted">☆</span>';
          }
        }
      ?>
      <small class="text-muted">(<?= number_format($media,2) ?> / 5,00)</small>
    </div>

    <!-- COMMENTI -->
        <section class="commenti mb-5">
      <h4>Commenti</h4>
      <?php while($c = pg_fetch_assoc($resComm)): ?>
        <div class="commento d-flex align-items-start">
        <a href="get_profiloo.php?id=<?= urlencode($c['utente_id']) ?>">
            <img src="<?= htmlspecialchars($c['immagine_profilo']) ?>"
                alt="Avatar <?= htmlspecialchars($c['nome']) ?>" class="avatar">
          </a>
          <div>
            <strong><?= htmlspecialchars($c['nome']) ?></strong>
            <p class="mb-0"><?= nl2br(htmlspecialchars($c['descrizione'])) ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    </section>


    <!-- ITINERARIO -->
    <section class="mb-5">
      <a href="get_itinerario(totale).php?id=<?= urlencode($viaggio_id) ?>" class="text-decoration-none">
      <h4 >Itinerario</h4>
      </a>
      <p><?= nl2br(htmlspecialchars($trip['descrizione_viaggio'])) ?></p>
    </section>

        <section class="mb-5">
  <h4>Il viaggio in breve</h4>
  <div class="d-flex flex-wrap justify-content-center">

    <div class="circle-container">
      <div class="circle" style="--percent:<?= htmlspecialchars($percentuali['natura']) ?>; --color:#8BC34A; --hue:70deg;">
        <img src="immagini/tree-solid.svg" alt="Natura">
      </div>
      <span class="circle-label">Natura e avventura</span>
      <span class="circle-percent"><?= htmlspecialchars($percentuali['natura']) ?>%</span>
    </div>

    <div class="circle-container">
      <div class="circle" style="--percent:<?= htmlspecialchars($percentuali['relax']) ?>; --color:#29B6F6; --hue:180deg;">
        <img src="immagini/umbrella-beach-solid.svg" alt="Relax">
      </div>
      <span class="circle-label">Relax</span>
      <span class="circle-percent"><?= htmlspecialchars($percentuali['relax']) ?>%</span>
    </div>

    <div class="circle-container">
      <div class="circle" style="--percent:<?= htmlspecialchars($percentuali['monumenti']) ?>; --color:#FFCA28; --hue:40deg;">
        <img src="immagini/archway-solid.svg" alt="Monumenti">
      </div>
      <span class="circle-label">Monumenti e storia</span>
      <span class="circle-percent"><?= htmlspecialchars($percentuali['monumenti']) ?>%</span>
    </div>

    <div class="circle-container">
      <div class="circle" style="--percent:<?= htmlspecialchars($percentuali['cultura']) ?>; --color:#FB8C00; --hue:20deg;">
        <img src="immagini/city-solid.svg" alt="Cultura">
      </div>
      <span class="circle-label">Città e cultura</span>
      <span class="circle-percent"><?= htmlspecialchars($percentuali['cultura']) ?>%</span>
    </div>

    <div class="circle-container">
      <div class="circle" style="--percent:<?= htmlspecialchars($percentuali['nightlife']) ?>; --color:#7E57C2; --hue:250deg;">
        <img src="immagini/champagne-glasses-solid.svg" alt="Nightlife">
      </div>
      <span class="circle-label">Party e nightlife</span>
      <span class="circle-percent"><?= htmlspecialchars($percentuali['nightlife']) ?>%</span>
    </div>

  </div>
</section>



    <!-- GALLERIA FOTO -->
    <section class="gallery mb-5">
      <h4>Foto</h4>
      <div class="row g-3">
        <?php
          for ($i=1; $i<=5; $i++) {
            $f = $trip["foto{$i}"];
            if ($f) {
              echo '<div class="col-6 col-md-3">';
              echo '<img src="'.htmlspecialchars($f).'" alt="Foto '.$i.'">';
              echo '</div>';
            }
          }
        ?>
      </div>
    </section>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
