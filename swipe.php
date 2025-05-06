<?php
session_start();
require_once 'connessione.php'; // qui imposti $dbconn come connessione pgsql

$userId = $_SESSION['id_utente'] ?? null;
$tripId = filter_input(INPUT_POST, 'tripId', FILTER_VALIDATE_INT);
// Leggi il valore raw e converti esattamente “1”→true, tutto il resto→false
$raw   = $_POST['like'] ?? '';
$isLike = ($raw === '1');

if (!$userId || !$tripId || $isLike === null) {
  echo json_encode(['userId'=>$userId,'tripId'=>$tripId,'isLike'=>$isLike]);
  http_response_code(400);
  echo json_encode(['error'=>'Parametri mancanti']);
  exit;
}

// 1) Inserisci o aggiorna lo swipe
$sql = "INSERT INTO swipes(user_id, trip_id, is_like, created_at)
        VALUES($1,$2,$3,now())
        ON CONFLICT(user_id,trip_id) DO UPDATE
          SET is_like = EXCLUDED.is_like, created_at = now()";
// Dopo: passa 1 o 0 espliciti
pg_query_params($dbconn, $sql, [
    $userId,
    $tripId,
    $isLike ? 1 : 0
  ]);
if ($isLike) {
  // 2) prendi dati viaggio+organizzatore
  $sql = "SELECT v.user_id AS org_id, v.destinazione, v.descrizione, u.email, u.nome
          FROM viaggi v JOIN utenti u ON u.id=v.user_id
          WHERE v.id=$1";
  $res = pg_query_params($dbconn, $sql, [$tripId]);
  $trip = pg_fetch_assoc($res);

  // 3) crea / recupera conversation
  $sql = "INSERT INTO conversations(trip_id,user_a,user_b,created_at)
          VALUES($1,$2,$3,now())
          ON CONFLICT(trip_id,user_a,user_b) DO NOTHING
          RETURNING id";
  $res = pg_query_params($dbconn, $sql, [$tripId, $userId, $trip['org_id']]);
  if (pg_num_rows($res)) {
    $convId = pg_fetch_result($res,0,'id');
  } else {
    $sql = "SELECT id FROM conversations WHERE trip_id=$1 AND user_a=$2 AND user_b=$3";
    $res = pg_query_params($dbconn, $sql, [$tripId, $userId, $trip['org_id']]);
    $convId = pg_fetch_result($res,0,'id');
  }
  // 4  notifica realtime con Node.js
  $notifyData = [
    'userId'    => $trip['org_id'],
    'fromUser'  => $userId,
    'tripId'    => $tripId,
    'tripTitle' => $trip['destinazione']
  ];

  $options = [
    'http' => [
      'header'  => "Content-type: application/json",
      'method'  => 'POST',
      'content' => json_encode($notifyData),
    ]
  ];

  $context = stream_context_create($options);
  file_get_contents('http://127.0.0.1:3000/notify-swipe', false, $context);

  // 5) rispondi al client
  echo json_encode([
    'success'=>true,
    'isMatch'=>true,
    'conversationId'=>(int)$convId,
    'tripTitle'=>$trip['destinazione']
  ]);
  exit;
}

// se è dislike
echo json_encode(['success'=>true,'isMatch'=>false]);
