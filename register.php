<?php
session_start(); // Avvia la sessione
include 'connessione.php';

$name = $_POST['name'] ?? '';
$nickname = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Controllo campi vuoti
if (!$name || !$nickname || !$email || !$password) {
    echo json_encode([
        "status" => "error",
        "message" => "Tutti i campi sono obbligatori."
    ]);
    exit;
}

// Controlla se email o nickname sono già presenti
$check = pg_query_params($dbconn, "SELECT 1 FROM utenti WHERE email=$1 OR nickname=$2", [$email, $nickname]);

if (pg_num_rows($check) > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Email o nome utente già registrati."
    ]);
    exit;
}

// Hash della password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Inserimento nuovo utente
$query = "INSERT INTO utenti (nome, nickname, email, password, vacanza, tipo_vacanza) VALUES ($1, $2, $3, $4, $5, $6)";
$res = pg_query_params($dbconn, $query, [$name, $nickname, $email, $hashed, null, null]);

if ($res) {
    // Recupera ID utente appena creato
    $result = pg_query_params($dbconn, "SELECT id FROM utenti WHERE email=$1", [$email]);
    $row = pg_fetch_assoc($result);
    $id_utente = $row['id'] ?? null;

    // Salva dati in sessione
    $_SESSION['user'] = $email;
    $_SESSION['id_utente'] = $id_utente;

    echo json_encode([
        "status" => "success",
        "message" => "Registrazione effettuata con successo!",
        "redirect" => "creaprofilo.html"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Impossibile effettuare la registrazione."
    ]);
}

pg_close($dbconn);
?>
