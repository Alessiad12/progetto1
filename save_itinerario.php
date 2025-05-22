<?php
require_once 'connessione.php';
session_start();
if (!isset($_SESSION['id_utente']) || !isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}
$utente_id = $_SESSION['id_utente'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo "Errore: dati JSON non ricevuti";
    exit;
}

$nome = pg_escape_string($dbconn, $data['nome'] ?? '');
$luoghi = json_encode($data['luoghi'] ?? []);
$viaggio_id = intval($data['viaggio_id'] ?? 0);

if (!$nome || !$utente_id || !$viaggio_id) {
    http_response_code(400);
    echo "Dati mancanti: nome=$nome, utente_id=$utente_id, viaggio_id=$viaggio_id";
    exit;
}

$query = "INSERT INTO itinerari (nome_itinerario, luoghi, utente_id, viaggio_id) VALUES ($1, $2, $3, $4)";
$result = pg_query_params($dbconn, $query, [$nome, $luoghi, $utente_id, $viaggio_id]);

if ($result) {
    echo "OK";
} else {
    http_response_code(500);
    echo "Errore query: " . pg_last_error($dbconn);
}
?>