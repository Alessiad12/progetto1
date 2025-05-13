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
try {
    $birthdate = new DateTime($compleanno);
    $eta       = (new DateTime())->diff($birthdate)->y;
} catch (Exception $e) {
    $eta = null;
}

$nome_file = uniqid() . '_' . basename($_FILES['immagine_profilo']['name']);
$destinazione = __DIR__ . '/uploads/' . $nome_file;
move_uploaded_file($_FILES['immagine_profilo']['tmp_name'], $destinazione);
$img_path = 'uploads/' . $nome_file;


// ---- INSERT su profili ----
$sql = "INSERT INTO viaggi_terminati
  (utente_id, viaggio_id, descrizione, valutazione, foto1, foto2, foto3, foto4, foto5)
 VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9)";
$sql_profili = "INSERT INTO profili(id, email, nome, eta, bio, data_di_nascita, immagine_profilo, posizione_immagine)
VALUES ($1, $2, $3, $4, $5, $6,$7, $8)";

$params_profili = [
  $id,
  $email,
  $nome,
  $eta,
  $bio,
  $compleanno,
  $img_path,
  null        // posizione_immagine
];

$res = pg_query_params($dbconn, $sql_profili, $params_profili);
if (!$res) {
  header('Content-Type: application/json');
  http_response_code(500);
  echo json_encode([
    'status'  => 'error',
    'message' => 'Profili INSERT failed: ' . pg_last_error($dbconn)
  ]);
  exit;
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
  header('Content-Type: application/json');
  http_response_code(500);
  echo json_encode([
    'status'  => 'error',
    'message' => 'Preferenze INSERT failed: ' . pg_last_error($dbconn)
  ]);
  exit;
}

// Tutto ok
header('Location: /visualizza_viaggi.php');
exit;
/* header('Content-Type: application/json');
echo json_encode([
  'status'  => 'success',
  'message' => 'Profilo e preferenze salvate.'
]);*/
