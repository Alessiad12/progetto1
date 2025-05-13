<?php
session_start();
require 'connessione.php';  // si assume che qui tu esporti $dbconn

if (!isset($_SESSION['id_utente'])) {
    header('Location: login.php');
    exit;
}

$utente_id = intval($_SESSION['id_utente']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- 1) Ricevo i dati dal form ---
    $viaggio_id  = intval($_POST['viaggio_id'] ?? 0);
    $descrizione = trim($_POST['descrizione'] ?? '');
    $valutazione = intval($_POST['valutazione'] ?? 0);

    // --- 2) Preparo la cartella uploads ---
    $relativeDir = 'uploads';
    $uploadDir   = __DIR__ . DIRECTORY_SEPARATOR . $relativeDir . DIRECTORY_SEPARATOR;
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        die('Errore: impossibile creare la cartella di upload.');
    }

    // --- 3) Gestisco fino a 5 file ---
    $fotos = [];
    for ($i = 0; $i < 5; $i++) {
        if (
            isset($_FILES['foto']['error'][$i]) &&
            $_FILES['foto']['error'][$i] === UPLOAD_ERR_OK
        ) {
            $tmpName = $_FILES['foto']['tmp_name'][$i];
            $orig    = basename($_FILES['foto']['name'][$i]);
            // Sanitizzo il nome e lo rendo unico
            $safe    = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $orig);
            $dest    = $uploadDir . $safe;

            if (move_uploaded_file($tmpName, $dest)) {
                // salvo percorso relativo (da usare in HTML: /uploads/…)
                $fotos[] = '/' . $relativeDir . '/' . $safe;
            } else {
                $fotos[] = null;
            }
        } else {
            $fotos[] = null;
        }
    }

    // --- 4) Inserisco su viaggi_terminati ---
    $natura    = intval($_POST['natura'] ?? 0);
$relax     = intval($_POST['relax'] ?? 0);
$monumenti = intval($_POST['monumenti'] ?? 0);
$cultura   = intval($_POST['cultura'] ?? 0);
$nightlife = intval($_POST['nightlife'] ?? 0);

// --- Inserisco su viaggi_terminati (incluso percentuali) ---
$sql = "INSERT INTO viaggi_terminati
    (utente_id, viaggio_id, descrizione, valutazione,
     foto1, foto2, foto3, foto4, foto5,
     natura, relax, monumenti, cultura, nightlife)
  VALUES
    ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14)";
$params = [
    $utente_id,
    $viaggio_id,
    $descrizione,
    $valutazione,
    $fotos[0],
    $fotos[1],
    $fotos[2],
    $fotos[3],
    $fotos[4],
    $natura,
    $relax,
    $monumenti,
    $cultura,
    $nightlife
];
$res = pg_query_params($dbconn, $sql, $params);
if (!$res) {
    die('Errore salvataggio esperienza: ' . pg_last_error($dbconn));
}

    // --- 5) Redirect di conferma ---
    header('Location: pagina_profilo.php');
    exit;
}

// Se arrivo qui, è GET: mostro il form
$viaggio_id = intval($_GET['viaggio_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Registra la tua esperienza – Wanderlust</title>
  <style>
    

    /* Palette */
    :root {
      --cream: #FDF7E3;
      --navy: #0A2342;
      --navy-light: #12315C;
    }
    /* Reset base */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: var(--cream);
      color: var(--navy);
      font-family: sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 2rem;
    }
    /* Card a sinistra, più grande */
    .card {
      background: #FFF;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      padding: 2rem;
      width: 600px;
    }
    .card h1 {
      font-size: 1.8rem;
      margin-bottom: 1.5rem;
      color: var(--navy);
      text-align: left;
    }
    form > div { margin-bottom: 1.5rem; }
    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
    }
    textarea {
      width: 100%;
      min-height: 120px;
      border: 1px solid rgba(10,35,66,0.3);
      border-radius: 6px;
      padding: 0.75rem;
      resize: vertical;
      font-size: 1rem;
    }
    /* Star rating */
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
    /* Photo slots */
    .photo-slots {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 0.5rem;
    }
    .photo-slot {
      position: relative;
      width: 100%;
      padding-top: 100%; /* square */
      border: 2px dashed rgba(10,35,66,0.3);
      border-radius: 8px;
      overflow: hidden;
    }
    .photo-slot input {
      position: absolute;
      width: 0;
      height: 0;
      overflow: hidden;
    }
    .photo-slot label {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      font-size: 2rem;
      color: rgba(10,35,66,0.5);
      cursor: pointer;
    }
    /* Submit button */
    .btn-submit {
      background: var(--navy);
      color: #fff;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.2s;
    }
    .btn-submit:hover {
      background: var(--navy-light);
    }
  
  </style>
</head>
<body>
  <div class="card">
    <h1>Il tuo viaggio</h1>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="viaggio_id" value="<?= htmlspecialchars($viaggio_id) ?>">

      <div>
        <label for="descrizione">Descrizione</label>
        <textarea id="descrizione" name="descrizione" required></textarea>
      </div>

      <div>
        <label>Valutazione</label>
        <div class="stars">
          <?php for ($s = 5; $s >= 1; $s--): ?>
            <input type="radio" name="valutazione" id="star<?= $s ?>" value="<?= $s ?>">
            <label for="star<?= $s ?>">★</label>
          <?php endfor; ?>
        </div>
      </div>

      <div>
        <label>Foto (max 5)</label>
        <div class="photo-slots">
          <?php for ($i = 0; $i < 5; $i++): ?>
            <div class="photo-slot">
              <input type="file" name="foto[]" accept="image/*" id="foto<?= $i ?>">
              <label for="foto<?= $i ?>">+</label>
            </div>
          <?php endfor; ?>
        </div>
      </div>
          <div>
      <label>Natura e avventura (%)</label>
      <input type="number" name="natura" min="0" max="100" required>
    </div>
    <div>
      <label>Relax (%)</label>
      <input type="number" name="relax" min="0" max="100" required>
    </div>
    <div>
      <label>Monumenti e storia (%)</label>
      <input type="number" name="monumenti" min="0" max="100" required>
    </div>
    <div>
      <label>Città e cultura (%)</label>
      <input type="number" name="cultura" min="0" max="100" required>
    </div>
    <div>
      <label>Party e nightlife (%)</label>
      <input type="number" name="nightlife" min="0" max="100" required>
    </div>


      <button type="submit" class="btn-submit">Salva Esperienza</button>
    </form>
  </div>
  <script>
  // Seleziono tutti gli input file dentro .photo-slot
  document.querySelectorAll('.photo-slot input[type="file"]').forEach(input => {
    input.addEventListener('change', function() {
      const file = this.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = e => {
        const slot = this.closest('.photo-slot');

        // Nascondi il "+"
        const label = slot.querySelector('label');
        if (label) label.style.display = 'none';

        // Verifico se esiste già un <img>, altrimenti lo creo
        let img = slot.querySelector('img');
        if (!img) {
          img = document.createElement('img');
          // Stili in linea per coprire tutto lo slot
          img.style.position    = 'absolute';
          img.style.top         = '0';
          img.style.left        = '0';
          img.style.width       = '100%';
          img.style.height      = '100%';
          img.style.objectFit   = 'cover';
          img.style.borderRadius= '8px';
          slot.appendChild(img);
        }

        // Imposto la sorgente al data URL
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    });
  });
</script>

</body>
</html>









