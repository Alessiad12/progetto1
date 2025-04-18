<?php
$host = 'localhost';
$port = '5432';
$dbname = 'ConnessionePHP';
$user = 'postgres';
$password = 'html';


$dbconn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$dbconn) {
    echo json_encode([
        "status" => "error",
        "message" => "Errore di connessione al database: " . pg_last_error()
    ]);
    exit;
}
?>