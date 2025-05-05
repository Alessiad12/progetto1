<?php
session_start();
require_once 'connessione.php';
if (!isset($_SESSION['id_utente']) || !isset($_SESSION['user'])) { 
    header('Location:/login.php'); 
    exit; 
  }
$id_utente = $_GET['id'] ?? null; // Recupera l'ID passato nella query string

if (!$id_utente) {
    die('ID utente non specificato.');
}

// Esegui la query per ottenere i dati dell'utente selezionato
$sql = "SELECT nome, eta, bio, colore_sfondo, immagine_profilo, posizione_immagine FROM profili WHERE id = $1";
$result = pg_query_params($dbconn, $sql, [$id_utente]);



if ($result) {
    $profilo = pg_fetch_assoc($result);
    echo json_encode($profilo);
} else {
    echo json_encode(["error" => "Profilo non trovato"]);
}

// Ora hai i dati dell'utente selezionato in $utente
?>