<?php
session_start();

$dbconn = pg_connect("host=localhost port=5432 dbname=ConnessionePHP user=postgres password=html");

if (!$dbconn) {
    echo json_encode([
        "status" => "error",
        "message" => "Errore di connessione al database."
    ]);
    exit;
}

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
$query = "SELECT id, password FROM utenti WHERE email = $1"; // aggiungi anche l'id alla SELECT
$result = pg_query_params($dbconn, $query, [$email]);

if (pg_num_rows($result) === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Utente non registrato."
    ]);
    pg_close($dbconn);
    exit;
}

$row = pg_fetch_assoc($result);
$hashedPassword = $row['password'];
$idUtente = $row['id']; // prendi anche l'id

if (password_verify($password, $hashedPassword)) {
    $_SESSION['user'] = $email;
    $_SESSION['id_utente'] = $idUtente; // salva anche l'id nella sessione!

    echo json_encode([
        "status" => "success",
        "message" => "Login effettuato con successo!",
        "redirect" => "index.html" 
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "message" => "Password errata."
    ]);
}

pg_close($dbconn);
?>
