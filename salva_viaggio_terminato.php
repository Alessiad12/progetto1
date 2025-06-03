<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])) {
  header('Location: login.php');
  exit;
}

$utente_id   = intval($_SESSION['id_utente']);
$viaggio_id  = intval($_POST['viaggio_id'] ?? 0);
$descrizione = trim($_POST['descrizione'] ?? '');
$valutazione = intval($_POST['valutazione'] ?? 0);

// --- Foto upload ---
$relativeDir = 'uploads';
$uploadDir   = __DIR__ . DIRECTORY_SEPARATOR . $relativeDir . DIRECTORY_SEPARATOR;
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    die('Errore: impossibile creare la cartella di upload.');
}

$fotos = [];
for ($i = 0; $i < 5; $i++) {
    if (
        isset($_FILES['foto']['error'][$i]) &&
        $_FILES['foto']['error'][$i] === UPLOAD_ERR_OK
    ) {
        $tmpName = $_FILES['foto']['tmp_name'][$i];
        $orig    = basename($_FILES['foto']['name'][$i]);
        $safe    = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $orig);
        $dest    = $uploadDir . $safe;

        if (move_uploaded_file($tmpName, $dest)) {
            $fotos[] = '/' . $relativeDir . '/' . $safe;
        } else {
            $fotos[] = null;
        }
    } else {
        $fotos[] = null;
    }
}

// --- Percentuali ---
$natura    = intval($_POST['natura'] ?? 0);
$relax     = intval($_POST['relax'] ?? 0);
$monumenti = intval($_POST['monumenti'] ?? 0);
$cultura   = intval($_POST['cultura'] ?? 0);
$nightlife = intval($_POST['nightlife'] ?? 0);

// --- INSERT DB ---
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

header('Location: pagina_profilo.php');
exit;
