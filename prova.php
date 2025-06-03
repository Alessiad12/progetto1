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
    <link href="/css/style_pagina_iniziale.css" rel="stylesheet">
  <style>

    .hero { position:relative; width:100%; height:50vh; min-height:300px; overflow:hidden; }
      .back-button {
        position: absolute;
        top: 1rem;   /* 16px di distanza dal bordo superiore della hero */
        left: 1rem;  /* 16px di distanza dal bordo sinistro della hero */
        z-index: 10; /* in primo piano su immagine + testo */
        
        background-color: rgba(255, 255, 255, 0.4); /* box bianco semitrasparente */
        padding: 0.5rem;      /* 8px di padding su tutti i lati */
        border-radius: 0.375rem; /* 6px di arrotondamento */
        
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none; /* rimuove la sottolineatura del link */
        color: #1D3B5B;        /* colore della freccia (blu scuro) */
        transition: background-color 0.2s ease;
      }

      .back-button:hover {
        background-color: rgba(255, 255, 255, 0.6);
      }

      /* 3) Ridimensiona l’SVG all’interno se vuoi renderlo più piccolo o più grande */
      .back-button svg {
        width: 1.25rem;  /* 20px */
        height: 1.25rem; /* 20px */
      }

      /* 4) Media‐query per schermi molto stretti (<576px) */
      @media (max-width: 576px) {
        .back-button {
          top: 0.5rem;   /* 8px dal bordo superiore */
          left: 0.5rem;  /* 8px dal bordo sinistro */
          padding: 0.375rem; /* 6px padding */
        }
        .back-button svg {
          width: 1rem;   /* 16px */
          height: 1rem;  /* 16px */
        }
      }
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
    .container {
      display: grid;
      grid-template-columns: 1fr 1fr; 
      gap: 20px; 
    }
    .destra{
        flex: 1;
        align-self: end;
        place-self: end;
    }
    .container_1{
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 20px;
    }
    /* Sezione immagini*/
.next-section {
    min-height: 20vh;
    overflow: hidden;
    position: relative;
    white-space: nowrap;
    margin-bottom: 120px;
}


.contenitore_immagini{
    display: flex;
    width: max-content; /* adatta la dimensione alla quantità di immagini */
    animation: scroll 70s linear infinite; /* Controlla la velocità qui */
}
.immagine{
    width: 100%;
    height: 250px; /* Imposta un'altezza fissa per uniformare */
    object-fit: cover; /* Riempe il contenitore tagliando il minimo necessario */
    border-radius: 10px;
    margin-right: 10px;
 
}
/* Animazione */
@keyframes scroll {
    0%{
        transform: translateX(0%);
    }
    100% {
        transform: translateX(-50%);
    }
}

  </style>
</head>
<body>

  <!-- HERO -->
  <section class="hero mb-4">
    <a href="pagina_profilo.php" class="back-button">
    <!-- SVG minimal di freccia a sinistra -->
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
         class="feather feather-arrow-left">
      <line x1="19" y1="12" x2="5" y2="12"></line>
      <polyline points="12 19 5 12 12 5"></polyline>
    </svg>
  </a>
    <img src="<?= htmlspecialchars($trip['foto1']) ?>" alt="Hero">
    <div class="hero-overlay">
      <h1 style="color:white"><?= htmlspecialchars($trip['destinazione']) ?></h1>
      <p><?= htmlspecialchars($trip['descrizione_viaggio']) ?></p>
    </div>
  </section>

  <div class="container">
    <div class="sinistra">
    <!-- COMMENTI -->
      <?php while($c = pg_fetch_assoc($resComm)): ?>
        <div class="commento d-flex align-items-start">
        <a href="get_profiloo.php?id=<?= urlencode($c['utente_id']) ?>">
            <img src="<?= htmlspecialchars($c['immagine_profilo']) ?>"
                alt="Avatar <?= htmlspecialchars($c['nome']) ?>" class="avatar">
          </a>
          <div  style="font-family: italic; margin-bottom:20px; "class="testimonial" >
            <?= nl2br(htmlspecialchars($c['descrizione'])) ?>
            <strong><?= htmlspecialchars($c['nome']) ?></strong>
          </div>
        </div>
      <?php endwhile; ?>
    </section>
  </div>
      <div class="destra">
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
        <a href="get_itinerario(totale).php?id=<?= urlencode($viaggio_id) ?>"><button class="btn-login">Itinerario</button></a>
      
  </div>
    </div>
<div class ="container_1">
        <section class="mb-5">
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
  </div>


    <!-- GALLERIA FOTO -->

<div class="next-section">
  <div class="contenitore_immagini">
    <?php
      // Primo ciclo
      for ($i=1; $i<=5; $i++) {
        $f = $trip["foto{$i}"];
        if (empty($f)) continue; // Salta foto vuote
          echo '<img src="'.htmlspecialchars($f). '" alt="Foto '.$i.'"class="immagine" style="width: 300px; height: 200px;">';
      }
            for ($i=1; $i<=5; $i++) {
        $f = $trip["foto{$i}"];
        if (empty($f)) continue; // Salta foto vuote
          echo '<img src="'.htmlspecialchars($f). '" alt="Foto '.$i.'"class="immagine" style="width: 300px; height: 200px;">';
      }
            for ($i=1; $i<=5; $i++) {
        $f = $trip["foto{$i}"];
        if (empty($f)) continue; // Salta foto vuote
          echo '<img src="'.htmlspecialchars($f). '" alt="Foto '.$i.'"class="immagine" style="width: 300px; height: 200px;">';
      }
                  for ($i=1; $i<=5; $i++) {
        $f = $trip["foto{$i}"];
        if (empty($f)) continue; // Salta foto vuote
          echo '<img src="'.htmlspecialchars($f). '" alt="Foto '.$i.'"class="immagine" style="width: 300px; height: 200px;">';
      }
            for ($i=1; $i<=5; $i++) {
        $f = $trip["foto{$i}"];
        if (empty($f)) continue; // Salta foto vuote
          echo '<img src="'.htmlspecialchars($f). '" alt="Foto '.$i.'"class="immagine" style="width: 300px; height: 200px;">';
      }

    ?>
  </div>
</div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
  const testimonials = document.querySelectorAll(".testimonial");

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("visible");
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.2
  });

  testimonials.forEach((testimonial, index) => {
    testimonial.style.transitionDelay = `${index * 0.3}s`;
    observer.observe(testimonial);
  });
});

    </script>
</body>
</html>
