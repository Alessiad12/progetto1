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

// immagine (hex string)
$img_hex = null;
if (!empty($_FILES['immagine_profilo']['tmp_name']) 
    && is_uploaded_file($_FILES['immagine_profilo']['tmp_name'])
) {
    $raw = file_get_contents($_FILES['immagine_profilo']['tmp_name']);
    // converto in hex (ASCII-safe)  
    $img_hex = bin2hex($raw);
}

// ---- INSERT su profili ----
// La colonna immagine_profilo è di tipo BYTEA
// usiamo DECODE($7,'hex') per ottenere di nuovo i byte
$sql_profili = <<<SQL
INSERT INTO profili
  (id, email, nome, eta, bio, data_di_nascita, immagine_profilo, posizione_immagine)
VALUES
  ($1, $2, $3, $4, $5, $6, decode($7,'hex'), $8)
SQL;

$params_profili = [
  $id,
  $email,
  $nome,
  $eta,
  $bio,
  $compleanno,
  $img_hex,   // hex string o null
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
header('Location: /index.html');
exit;
/* header('Content-Type: application/json');
echo json_encode([
  'status'  => 'success',
  'message' => 'Profilo e preferenze salvate.'
]);*/
