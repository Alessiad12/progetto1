<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['id_utente'])) {
    echo json_encode(["error" => "Utente non loggato"]);
    exit;
}
require 'connessione.php'; // contiene $dbconn


$user_id = $_SESSION['id_utente'];

$destinazione = $_POST['destinazione'];
$data_partenza = $_POST['data_partenza'];
$data_ritorno = $_POST['data_ritorno'];
$budget = $_POST['budget'];
$tipo_viaggio = $_POST['tipo_viaggio'];
$lingua = $_POST['lingua'];
$compagnia = $_POST['compagnia'];
$descrizione = $_POST['descrizione'];

// Query parametrica per evitare SQL injection
$sql = "INSERT INTO viaggi (user_id, destinazione, data_partenza, data_ritorno, budget, tipo_viaggio, lingua, compagnia, descrizione)
        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";

$params = array(
    $user_id,
    $destinazione,
    $data_partenza,
    $data_ritorno,
    $budget,
    $tipo_viaggio,
    $lingua,
    $compagnia,
    $descrizione
);

$result = pg_query_params($dbconn, $sql, $params);

if ($result) {
    echo "Viaggio creato con successo!";
} else {
    echo "Errore nella creazione del viaggio: " . pg_last_error($dbconn);
}
?>