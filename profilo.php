<?php
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['id_utente'])) {
    echo json_encode(["error" => "Utente non loggato"]);
    exit;
}
require 'connessione.php'; // Assicurati che questo file definisca $conn

// Recupera l'ID utente dalla sessione
$id_utente = $_SESSION['id_utente'];
$email = $_SESSION['user'];


$sql = "SELECT nome, eta, bio, colore_sfondo, immagine_profilo FROM profili WHERE email = $1";
$result = pg_query_params($dbconn, $sql, [$email]);


if ($result) {
    $profilo = pg_fetch_assoc($result);
    echo json_encode($profilo);
} else {
    echo json_encode(["error" => "Profilo non trovato"]);
}
pg_close($dbconn);
?>
