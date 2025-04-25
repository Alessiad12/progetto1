<?php
session_start();
require 'connessione.php';

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($input['action'])
    && $input['action'] === 'organize'
) {
    // Controllo autenticazione
    if (!isset($_SESSION['id_utente'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Non autenticato']);
        exit;
    }
    $u = $_SESSION['id_utente'];
    $viaggioId = intval($input['viaggioId']);

    // Crea il piano di viaggio
    $res = pg_query_params($dbconn,
      "INSERT INTO piani(user_id, viaggio_id) VALUES($1, $2) RETURNING id",
      [$u, $viaggioId]
    );
    if (!$res) {
        http_response_code(500);
        echo json_encode(['error'=>'Errore creazione piano']);
        exit;
    }
    $row = pg_fetch_assoc($res);
    $planId = $row['id'] ?? null;

    // Rispondi con JSON
    echo json_encode(['planId' => $planId]);
    exit;
}

if (!isset($_SESSION['id_utente'])) {
  echo json_encode(['error' => 'Non autenticato']);
  exit;
}

$u = $_SESSION['id_utente'];

$r = pg_query_params($dbconn,
  "SELECT vacanza, tipo_vacanza FROM utenti WHERE id = $1",
  [$u]
);
$pref = pg_fetch_assoc($r);

if (!$pref) {
  echo json_encode(['error' => 'Preferenze non trovate']);
  exit;
}

$r2 = pg_query_params($dbconn,
  "SELECT v.foto AS photo_path, u.nome
   FROM viaggi v
   JOIN utenti u ON u.id = v.utente
   WHERE v.vacanza = $1 AND v.scopo = $2 AND v.utente != $3",
  [$pref['vacanza'], $pref['tipo_vacanza'], $u] // esclude se stesso
);

$matches = pg_fetch_all($r2);
echo json_encode(['viaggi' => $matches ?: []]);
exit;
?>