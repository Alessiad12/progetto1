<?php
session_start();

/* 1. Connessione */
$dbconn = pg_connect(
    "host=localhost port=5432 dbname=ConnessionePHP user=postgres password=html"
);
if (!$dbconn) {
    echo "Errore di connessione al database.";
    exit;
}

/* 2. Verifica login */
if (!isset($_SESSION['user_id'])) {
    echo "Non sei loggato.";
    exit;
}
$userId = intval($_SESSION['user_id']);

/* 3. Selezione delle foto */
$sql = "SELECT photo_path FROM viaggi WHERE user_id = $1 ORDER BY created_at DESC";
$res = pg_query_params($dbconn, $sql, [$userId]);
if (!$res) {
    echo "Errore query: " . pg_last_error($dbconn);
    exit;
}

/* 4. Costruisci array */
$photos = [];
while ($row = pg_fetch_assoc($res)) {
    $photos[] = '/uploads/' . $row['photo_path'];
}

/* 5. Output JSON pulito */
header('Content-Type: application/json; charset=utf-8');
echo json_encode($photos);

pg_close($dbconn);
/* Nessun tag ?> di chiusura */
