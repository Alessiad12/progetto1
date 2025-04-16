<?php
session_start();

// Verifica se la sessione è attiva e se l'utente è loggato
if (!isset($_SESSION['user']) || !isset($_SESSION['id_utente'])) {
    echo json_encode(["error" => "Utente non loggato"]);
    exit;
}

// Connessione al database PostgreSQL
$conn = pg_connect("host=localhost port=5432 dbname=ConnessionePHP user=postgres password=html");

if (!$conn) {
    die("Connessione fallita: " . pg_last_error());
}

// Recupera l'ID utente dalla sessione
$id_utente = $_SESSION['id_utente'];

$sql = "SELECT nome, eta, bio, colore_sfondo FROM profili WHERE id_utente = $1";
$result = pg_query_params($conn, $sql, array($id_utente));

if ($result) {
    $profilo = pg_fetch_assoc($result);
    echo json_encode($profilo);
} else {
    echo json_encode(["error" => "Profilo non trovato"]);
}

pg_close($conn);
?>
