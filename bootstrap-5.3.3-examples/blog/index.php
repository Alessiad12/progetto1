<?php
session_start();
require '../../connessione.php';
if (!isset($_SESSION['id_utente'])) {
    header('Location: login.php');
    exit;
}

$utente_id = intval($_SESSION['id_utente']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID viaggio non valido.<br>";
    exit;
}

$viaggio_id =($_GET['id']);

$sql = "
  SELECT v.*,p.*, viaggi.*
  FROM viaggi_terminati v join profili p on 
  p.id=v.utente_id join viaggi on viaggi.id=v.viaggio_id
  WHERE viaggio_id=$1;
";
$res = pg_query_params($dbconn, $sql, [ $viaggio_id ]);
$row = pg_fetch_assoc($res);

$sql_commenti = "
  SELECT p.nome,p.immagine_profilo, v.descrizione
  FROM viaggi_terminati v
  JOIN profili p ON p.id = v.utente_id
  WHERE v.viaggio_id = $1;
";
$res_commenti = pg_query_params($dbconn, $sql_commenti, [ $viaggio_id ]);
$query_media=" SELECT AVG(valutazione) AS media_valutazione 
 FROM viaggi_terminati 
 WHERE viaggio_id= $1;";
 $res_media = pg_query_params($dbconn, $query_media, [$viaggio_id]);
 $media = 0;
 if ($res_media && pg_num_rows($res_media) > 0) {
     $row_media = pg_fetch_assoc($res_media);
     $media = round($row_media['media_valutazione'], 2); // Arrotonda a 2 decimali
 }
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head><script src="../assets/js/color-modes.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.122.0">
    <title>Viaggi Terminati</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/blog/">

    

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">


<link href="/bootstrap-5.3.3-examples/assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body{
        background-color: rgba(139, 214, 255, 0.34);
      }
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }
      .stars {
      display: flex;
      direction: rtl;
      gap: 0.25rem;
    }
    .stars input { display: none; }
    .stars label {
      font-size: 2rem;
      color: rgba(0,0,0,0.2);
      cursor: pointer;
      transition: color 0.2s;
    }
    .stars label:hover,
    .stars label:hover ~ label,
    .stars input:checked ~ label {
      color: gold;
    }
    .img-fluid {
max-width: 100%;
height: 44px;
}

      .btn-bd-primary {
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
      }

      .bd-mode-toggle {
        z-index: 1500;
      }

      .bd-mode-toggle .dropdown-menu .active .bi {
        display: block !important;
      }
      .display-4 {
        font-size: calc(2.48rem + 2.7vw);
        font-weight: 300;
        line-height: 1.2;
        }
        .photo-strip {
    display: flex;
    overflow-x: auto;
    gap: 10px;
    padding: 1rem 0;
    scroll-snap-type: x mandatory;
  }

  .photo-strip img {
    height: 200px;
    width: 300px;
    border-radius: 10px;
    flex-shrink: 0;
    scroll-snap-align: start;
  }

  .photo-strip::-webkit-scrollbar {
    display: none; /* rimuove scrollbar su Chrome/Safari */
  }

    </style>

    
    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Playfair&#43;Display:700,900&amp;display=swap" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="/bootstrap-5.3.3-examples/blog/blog.css" rel="stylesheet">
  </head>
  <body>
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
      <symbol id="check2" viewBox="0 0 16 16">
        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
      </symbol>
      <symbol id="circle-half" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
      </symbol>
      <symbol id="moon-stars-fill" viewBox="0 0 16 16">
        <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
        <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
      </symbol>
      <symbol id="sun-fill" viewBox="0 0 16 16">
        <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
      </symbol>
    </svg>

    
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="aperture" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="10"/>
    <path d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94M9.69 16L3.95 6.06M14.31 16H2.83m13.79-4l-5.74 9.94"/>
  </symbol>
  <symbol id="cart" viewBox="0 0 16 16">
    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
  <symbol id="chevron-right" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
  </symbol>
</svg>

<div class="container">
  <header class="border-bottom lh-1 py-3">
    <div class="row flex-nowrap justify-content-between align-items-center">
      </div>
    </div>
  </header>
  
<div> <img src="../../<?= htmlspecialchars($row['foto']) ?>" alt="foto viaggio">
</div>
<main class="container " >
  <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="background-color: rgba(167, 186, 213, 0.49)!important;size: 17rem;">
    <div class="col-lg-6 px-0">
      <h1 class="display-4 fst-italic"> <?= htmlspecialchars($row['destinazione']) ?></h1>
    
    </div>
  </div>
  <article class="blog-post">
  <h2 class="pb-4 link-body-emphasis mb-4"><?= htmlspecialchars($row['descrizione'])?> </h2>
  </article>
      <h3 class="pb-4 mb-4 fst-italic border-bottom">
        <div>
          <label>Valutazione</label>
              <?php
              $rounded = round($media); // oppure round($media, 1) se vuoi decimali
              for ($i = 1; $i <= 5; $i++) {
                  echo $i <= $rounded ? '★' : '☆';
              }
              ?>
              (<?= $media ?> / 5)

        </div>
      </h3>

      <article class="blog-post">
        <h2 class="display-5 link-body-emphasis mb-1">Commenti</h2>
        <p class="blog-post-meta">Condivisioni della propria esperienza </p>
          <?php while ($commento = pg_fetch_assoc($res_commenti)): ?>

              <strong><?= htmlspecialchars($commento['nome']) ?></strong><br>
              <img src="../../<?= htmlspecialchars($commento['immagine_profilo']) ?>" alt="Foto profilo di <?= htmlspecialchars($row['nome']) ?>" class="img-fluid rounded-circle" style="max-width: 44px;">
              <?= nl2br(htmlspecialchars($commento['descrizione'])) ?>
              <h3 class="pb-4 mb-4 fst-italic border-bottom"></h3>
          <?php endwhile; ?>
    
    
      </article>

      <article class="blog-post">
        <h2 class="display-5 link-body-emphasis mb-1">foto</h2>
        <div class="photo-strip">
  <?php while ($row = pg_fetch_assoc($res)): ?>
    <?php
    for ($i = 1; $i <= 5; $i++) {
      if (!empty($row["foto$i"])) {
        $foto = htmlspecialchars($row["foto$i"]);
        echo '<img src="../../' . $foto . '" alt="Foto" />';
      }
    }
    ?>
  <?php endwhile; ?>
</div>



<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>
