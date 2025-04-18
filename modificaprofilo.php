<?php
session_start();
require 'connessione.php';

header("Content-Type: application/json");

if (!isset($_SESSION['id_utente'])) {
    error_log("Sessione non inizializzata o id_utente mancante");
    echo json_encode(["success" => false, "error" => "Utente non loggato"]);
    exit;
  }

$id_utente = $_SESSION['id_utente'];
$utente= $_SESSION['user'];
$data = json_decode(file_get_contents("php://input"), true);

$nome = $data['nome'] ?? '';
$bio = $data['bio'] ?? '';
$colore = $data['colore_sfondo'] ?? '';

if (!$nome || !$bio || !$colore) {
  echo json_encode(["success" => false, "error" => "Campi mancanti"]);
  exit;
}

$conn = pg_connect("host=localhost port=5432 dbname=ConnessionePHP user=postgres password=html");
if (!$conn) {
  echo json_encode(["success" => false, "error" => "Connessione DB fallita"]);
  exit;
}

$sql = "UPDATE profili SET nome = $1, bio = $2, colore_sfondo = $3 WHERE email = $4";
$result = pg_query_params($dbconn, $sql, [$nome, $bio, $colore, $utente]);

if ($result) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "error" => "Errore durante update"]);
}

pg_close($conn);
?>
