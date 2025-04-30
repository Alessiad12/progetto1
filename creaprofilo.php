<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente']) || !isset($_SESSION['user'])) {
  header('Location:/register.php'); 
  exit;
}

$id_utente = $_SESSION['id_utente'];
$email = $_SESSION['user'];

// Query per recuperare il nickname e la data di nascita
$query = "SELECT nickname, data_di_nascita FROM utenti WHERE email = $1";
$res = pg_query_params($dbconn, $query, [$email]);

if ($res) {
    $row = pg_fetch_assoc($res);
    if ($row) {
        $nickname = $row['nickname'];  // Recupera il nickname dalla riga risultante
        $data_di_nascita = $row['data_di_nascita'];  // Recupera la data di nascita
    } else {
        // Nessun risultato trovato
        $nickname = null;
        $data_di_nascita = null;
    }
} else {
    // La query ha fallito
    $nickname = null;
    $data_di_nascita = null;
}

echo json_encode([
  "status" => "success",
  "nickname" => $nickname,
  "email" => $_SESSION['user'],
  "data_di_nascita" => $data_di_nascita
]);
?>