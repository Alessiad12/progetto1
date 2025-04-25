<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])) {
  http_response_code(401);
  echo json_encode(['error'=>'Non autenticato']);
  exit;
}

$planId = intval($_GET['planId'] ?? 0);
if (!$planId) {
  http_response_code(400);
  echo json_encode(['error'=>'planId mancante']);
  exit;
}

// 1) Recupera i dati base del piano
$res1 = pg_query_params($dbconn,
  "SELECT id, created_at FROM piani WHERE id = $1 AND user_id = $2",
  [$planId, $_SESSION['id_utente']]
);
$plan = pg_fetch_assoc($res1);
if (!$plan) {
  http_response_code(404);
  echo json_encode(['error'=>'Piano non trovato']);
  exit;
}

// 2) Recupera eventuali tappe (tabella esempio: tappe)
$res2 = pg_query_params($dbconn,
  "SELECT nome, data_inizio, data_fine
   FROM tappe
   WHERE plan_id = $1
   ORDER BY data_inizio",
  [$planId]
);
$tappe = pg_fetch_all($res2) ?: [];

// 3) Rispondi con JSON
echo json_encode([
  'plan'  => $plan,
  'tappe' => $tappe
]);
