<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])) {
  header('Location: login.php');
  exit;
}
// Ricezione POST
$uid   = intval($_POST['utente_id']);
$vid   = intval($_POST['viaggio_id']);
$desc  = $_POST['descrizione'];
$rate  = intval($_POST['valutazione']);

// gestisci fino a 5 file
$fotos = [];
for ($i = 0; $i < 5; $i++) {
  if (!empty($_FILES['foto']['tmp_name'][$i])) {
    $tmp = $_FILES['foto']['tmp_name'][$i];
    $name = uniqid().'_'.basename($_FILES['foto']['name'][$i]);
    move_uploaded_file($tmp, __DIR__.'/uploads/'.$name);
    $fotos[] = '/uploads/'.$name;
  } else {
    $fotos[] = null;
  }
}

// INSERT in esperienze
$sql = "INSERT INTO viaggi_terminati
  (utente_id, viaggio_id, descrizione, valutazione, foto1, foto2, foto3, foto4, foto5)
 VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9)";
$params = [
  $uid, $vid, $desc, $rate,
  $fotos[0], $fotos[1], $fotos[2], $fotos[3], $fotos[4]
];
$res = pg_query_params($dbconn, $sql, $params);
if (!$res) {
  die('Errore salvataggio esperienza: '.pg_last_error());
}
// redirect al profilo o a una pagina riepilogo
header('Location: pagina_profilo.php');
exit;
