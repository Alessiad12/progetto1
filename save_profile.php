<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['id_utente'])) {
  header('Location: register.php');
  exit;
}

// include la connessione (definisce $dbconn)
require_once __DIR__ . '/connessione.php';

$id             = $_SESSION['id_utente'];
$nome           = $_POST['name']           ?? '';
$bio            = $_POST['bio']            ?? '';
$email          = $_POST['email']          ?? '';
$compleanno     = $_POST['compleanno']     ?? '';
$mi_interessano = $_POST['mi_interessano'] ?? '';
$dream_vacation = $_POST['dream_vacation'] ?? '';

// calcolo età
$birthdate = new DateTime($compleanno);
$eta       = (new DateTime())->diff($birthdate)->y;

// immagine (bytea)
$img_data = null;
if (!empty($_FILES['immagine_profilo']['tmp_name'])) {
  $raw = file_get_contents($_FILES['immagine_profilo']['tmp_name']);
  // PG si aspetta bytea: escapalo
  $img_data = pg_escape_bytea($raw);
}

// ---- INSERT su profili ----
$sql_profili = <<<SQL
INSERT INTO profili
  (id, email, nome, eta, bio, data_di_nascita, immagine_profilo, posizione_immagine)
VALUES
  ($1, $2, $3, $4, $5, $6, DECODE($7, 'escape'), $8)
SQL;

$params_profili = [
  $id,
  $email,
  $nome,
  $eta,
  $bio,
  $compleanno,
  $img_data,
  null  // o un valore per posizione_immagine
];

$res = pg_query_params($dbconn, $sql_profili, $params_profili);
if (!$res) {
  die('Profili INSERT failed: ' . pg_last_error($dbconn));
}

// ---- INSERT su preferenze_utente_viaggio ----
$sql_pref = <<<SQL
INSERT INTO preferenze_utente_viaggio
  (utente_id, email, destinazione, data_partenza, data_ritorno, budget, tipo_viaggio, compagnia)
VALUES
  ($1, $2, $3, $4, $5, $6, $7, $8)
SQL;

$params_pref = [
  $id,
  $email,
  null,   // destinazione
  null,   // data_partenza
  null,   // data_ritorno
  null,   // budget
  $dream_vacation,
  $mi_interessano
];

$res2 = pg_query_params($dbconn, $sql_pref, $params_pref);
if (!$res2) {
  die('Preferenze INSERT failed: ' . pg_last_error($dbconn));
}

// tutto ok
echo json_encode([
  'status' => 'success',
  'message'=> 'Profilo e preferenze salvate.'
]);
