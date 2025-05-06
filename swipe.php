<?php
session_start();
require_once 'connessione.php';

// Impostiamo l'header per il tipo di risposta
header('Content-Type: application/json');

// 1. Leggi parametri
$userId = $_SESSION['id_utente'] ?? null;
$tripId = filter_input(INPUT_POST, 'tripId', FILTER_VALIDATE_INT);
$like   = $_POST['like'] ?? null;
$isLike = $like === '1';

// Log per debug
error_log("User ID: " . $userId);
error_log("Trip ID: " . $tripId);
error_log("Like: " . $like);
error_log("Is Like: " . $isLike);

// Verifica la validità dei parametri
if (!$userId || !$tripId || !in_array($like, ['0', '1'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Parametri mancanti o non validi']);
    exit;
}

// 2. Registra o aggiorna lo swipe
$sql = "INSERT INTO swipes(user_id, trip_id, is_like, created_at)
        VALUES ($1, $2, $3, now())
        ON CONFLICT(user_id, trip_id)
        DO UPDATE SET is_like = EXCLUDED.is_like, created_at = now()";

// Log della query SQL
error_log("SQL Query: " . $sql);

// Esegui la query
$result = pg_query_params($dbconn, $sql, [$userId, $tripId, $isLike ? 1 : 0]);

// Se la query fallisce, logga l'errore
if (!$result) {
    $error = pg_last_error($dbconn);
    error_log("Errore SQL: " . $error);
    http_response_code(500);
    echo json_encode(['error' => 'Errore durante l\'inserimento dello swipe']);
    exit;
}

// 3. Se è un like, manda la notifica all'organizzatore
if ($isLike) {
  // Recupera dati del viaggio e dell'organizzatore
  $sql = "SELECT v.user_id AS org_id, v.destinazione
          FROM viaggi v
          WHERE v.id = $1";

  error_log("SQL Query: " . $sql);
  $res = pg_query_params($dbconn, $sql, [$tripId]);

  if (!$res || pg_num_rows($res) === 0) {
      error_log("Errore SQL o viaggio non trovato: " . pg_last_error($dbconn));
      http_response_code(500);
      echo json_encode(['error' => 'Viaggio non trovato']);
      exit;
  }

  $trip = pg_fetch_assoc($res);
  $orgId = $trip['org_id'];
  $tripTitle = $trip['destinazione'];


  // Notifica realtime con Node.js (non blocca in caso di errore)
  $notifyData = [
      'userId'    => (int)$orgId,
      'fromUser'  => (int)$userId,
      'tripId'    => (int)$tripId,
      'tripTitle' => $tripTitle,
      'tipo'      => 'like'
  ];

  $options = [
      'http' => [
          'header'  => "Content-type: application/json",
          'method'  => 'POST',
          'content' => json_encode($notifyData),
          'timeout' => 1
      ]
  ];

  $context = stream_context_create($options);
  $response = @file_get_contents('http://127.0.0.1:4000/notify-swipe', false, $context);
  error_log("Notifica inviata. Risposta: " . ($response ?: 'nessuna risposta'));

  echo json_encode([
      'success' => true,
      'isMatch' => true
  ]);
  exit;
}
// 4. Se è dislike
echo json_encode(['success' => true, 'isMatch' => false]);
