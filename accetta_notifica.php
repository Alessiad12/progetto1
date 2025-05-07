<?php
session_start();
require_once 'connessione.php';
header('Content-Type: application/json');

// File di log per il debug
$log_file = __DIR__ . '/accetta_notifica.txt';
function log_debug($msg) {
    global $log_file;
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] " . $msg . "\n", FILE_APPEND);
}

log_debug("▶ Richiesta ricevuta");

$input = json_decode(file_get_contents('php://input'), true);
$notifica_id = $input['notifica_id'] ?? null;
$utente_loggato = $_SESSION['id_utente'] ?? null;

if (!$notifica_id || !$utente_loggato) {
    log_debug("❌ Dati mancanti: notifica_id=$notifica_id, utente_loggato=$utente_loggato");
    echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
    exit;
}

// Recupera la notifica
$sql = "SELECT * FROM notifiche WHERE id = $1 AND utente_id = $2";
$res = pg_query_params($dbconn, $sql, [$notifica_id, $utente_loggato]);

if (!$res || pg_num_rows($res) === 0) {
    log_debug("❌ Notifica non trovata. ID: $notifica_id, utente: $utente_loggato");
    echo json_encode(['success' => false, 'message' => 'Notifica non trovata']);
    exit;
}

$notifica = pg_fetch_assoc($res);
$mittente_id = $notifica['mittente_id'];
$titolo_viaggio = $notifica['titolo_viaggio'] ?? 'il tuo viaggio';
$tripId=$notifica["viaggio_id"];
$tipo = 'match_accepted';

log_debug("✅ Generata notifica di accettazione per utente $mittente_id");

// Inserisci nuova notifica nel DB
$insert = "INSERT INTO notifiche (utente_id, mittente_id,viaggio_id, titolo_viaggio, data_creazione, tipo)
           VALUES ($1, $2, $3, $4, NOW(), $5)";
$insert_res = pg_query_params($dbconn, $insert, [ $utente_loggato,$mittente_id, $tripId, $titolo_viaggio, $tipo]);

if (!$insert_res) {
    log_debug("❌ Errore nell'inserimento della nuova notifica: " . pg_last_error($dbconn));
    echo json_encode(['success' => false, 'message' => 'Errore nel salvataggio']);
    exit;
}

$insert = "INSERT INTO viaggi_utenti (viaggio_id, user_id)
           VALUES ($1, $2)";
$insert_res = pg_query_params($dbconn, $insert, [ $tripId,$mittente_id]);

if (!$insert_res) {
    log_debug("❌ Errore nell'inserimento della nuova notifica: " . pg_last_error($dbconn));
    echo json_encode(['success' => false, 'message' => 'Errore nel salvataggio']);
    exit;
}

log_debug("✅ Notifica salvata nel DB");

// Invia notifica real-time al server Node
$postData = json_encode([
    'userId' => $mittente_id,
    'fromUser' => $_SESSION['id_utente'],
    'tripId' => $notifica['viaggio_id'],
    'tripTitle' => $titolo_viaggio,
    'tipo' => $tipo
]);

$ch = curl_init('http://localhost:4000/notify-swipe');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postData)
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    log_debug("❌ Errore CURL: $curlErr");
    echo json_encode(['success' => false, 'message' => 'Errore nella comunicazione con il server']);
    exit;
}

log_debug("✅ Notifica match_accepted inviata con HTTP $httpCode: $postData");

echo json_encode(['success' => true]);
