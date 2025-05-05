<?php
session_start();
require 'connessione.php'; // Assicurati che questo file definisca $dbconn

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode([
        "status" => "error",
        "message" => "Tutti i campi sono obbligatori."
    ]);
    exit;
}

// Recupera anche l'id_utente oltre alla password
$query = "SELECT id, password FROM utenti WHERE email = $1";
$result = pg_query_params($dbconn, $query, [$email]);

if (!$result || pg_num_rows($result) === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Utente non registrato."
    ]);
    pg_close($dbconn);
    exit;
}

$row = pg_fetch_assoc($result);
$hashedPassword = $row['password'];
$idUtente = $row['id'];

if (password_verify($password, $hashedPassword)) {
    $_SESSION['user'] = $email;
    $_SESSION['id_utente'] = $idUtente;

    echo json_encode([
        "status" => "success",
        "message" => "Login effettuato con successo!",
        "redirect" => "visualizza_viaggi.php" // Cambia con la tua pagina di destinazione
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Password errata."
    ]);
}

pg_close($dbconn);
?>