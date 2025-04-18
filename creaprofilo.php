<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])||!isset($_SESSION['user']) ) { 
  header('Location:/register.php'); 
  exit; 
}

$id_utente = $_SESSION['id_utente'];
$email = $_SESSION['user'];

echo json_encode([
    "status" => "success",
    "email" => $_SESSION['user']
]);
?>