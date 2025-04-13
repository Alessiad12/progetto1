<?php
$dbconn = pg_connect("host=localhost port=5432 dbname=ConnessionePHP user=postgres password=html");

if (!$dbconn) {
    echo "Errore di connessione al database.";
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo "Tutti i campi sono obbligatori.";
    exit;
}

$query = "SELECT password FROM utenti WHERE email = $1";
$result = pg_query_params($dbconn, $query, [$email]);

if (pg_num_rows($result) === 0) {
    echo "Utente non registrato.";
    pg_close($dbconn);
    exit;
}

$row = pg_fetch_assoc($result);
$hashedPassword = $row['password'];

if (password_verify($password, $hashedPassword)) {
    echo "Login effettuato con successo!";
} else {
    echo "Password errata.";
}

pg_close($dbconn);
?>
