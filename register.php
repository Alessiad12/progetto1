<?php
$dbconn = pg_connect("host=localhost port=5432 dbname=ConnessionePHP user=postgres password=Francesca2");

if (!$dbconn) {
    echo "❌ Errore di connessione al database.";
    exit;
}

$name = $_POST['name'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$name || !$username || !$email || !$password) {
    echo "❌ Tutti i campi sono obbligatori.";
    exit;
}

// Controllo se l'email o username esistono già
$check = pg_query_params($dbconn, "SELECT 1 FROM utenti WHERE email=$1 OR username=$2", [$email, $username]);

if (pg_num_rows($check) > 0) {
    echo "⚠️ Email o nome utente già registrati.";
    exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);
$query = "INSERT INTO utenti (nome, username, email, password) VALUES ($1, $2, $3, $4)";
$res = pg_query_params($dbconn, $query, [$name, $username, $email, $hashed]);

if ($res) {
    echo "✅ Registrazione avvenuta con successo!";
} else {
    echo "❌ Errore nella registrazione: " . pg_last_error($dbconn);
}

pg_close($dbconn);
?>
