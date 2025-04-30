<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])||!isset($_SESSION['user']) ) { 
  header('Location:/register.php'); 
  exit; 
}

$id_utente = $_SESSION['id_utente'];
$email = $_SESSION['user'];

$query = "SELECT nickname FROM utenti WHERE email= $1 ";
$res = pg_query_params($dbconn, $query, [ $email]);

if ($res) {
  $row = pg_fetch_assoc($res);
  if ($row) {
      $nickname = $row['nickname'];  // Recupera il nickname dalla riga risultante
  } else {
      $nickname = null;  // Nessun risultato trovato
  }
} else {
  $nickname = null;  // Query fallita
}

echo json_encode([
  "status" => "success",
  "nickname" => $nickname,
  "email" => $_SESSION['user']
]);
?>