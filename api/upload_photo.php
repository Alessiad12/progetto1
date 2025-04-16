<?php
session_start();

/* 1. Connessione --------------------------------------------------------- */
$dbconn = pg_connect(
    "host=localhost port=5432 dbname=ConnessionePHP user=postgres password=html"
);
if (!$dbconn) {
    echo "Errore di connessione al database.";
    exit;
}

/* 2. Verifica login ------------------------------------------------------- */
if (!isset($_SESSION['user_id'])) {
    echo "Non sei loggato.";
    exit;
}
$userId = intval($_SESSION['user_id']);

/* 3. Verifica file ricevuto ---------------------------------------------- */
if (!isset($_FILES['photo'])) {
    echo "Nessun file caricato.";
    exit;
}

/* 4. Controlli di tipo e dimensione -------------------------------------- */
$allowed = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($_FILES['photo']['type'], $allowed) ||
    $_FILES['photo']['size'] > 2 * 1024 * 1024) {   // 2 MB
    echo "Formato non supportato o file troppo grande.";
    exit;
}

/* 5. Salvataggio fisico del file ----------------------------------------- */
$uploadsDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0755, true)) {
    echo "Cartella uploads non creabile.";
    exit;
}

$filename = uniqid() . '_' . basename($_FILES['photo']['name']);
$target   = $uploadsDir . $filename;

if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
    echo "Errore nel salvataggio del file.";
    exit;
}

/* 6. Inserimento nel DB --------------------------------------------------- */
$sql = "INSERT INTO viaggi (user_id, photo_path) VALUES ($1, $2)";
$res = pg_query_params($dbconn, $sql, [$userId, $filename]);

if (!$res) {
    echo "Errore query: " . pg_last_error($dbconn);
    exit;
}

/* 7. Risposta JSON -------------------------------------------------------- */
header('Content-Type: application/json; charset=utf-8');
echo json_encode(["url" => "/uploads/" . $filename]);

pg_close($dbconn);
/* niente tag ?> */
