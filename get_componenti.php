<?php
require 'connessione.php';

if (!isset($_GET['id_viaggio'])) {
  http_response_code(400);
  echo json_encode(['errore' => 'ID viaggio mancante']);
  exit;
}

$id_viaggio = $_GET['id_viaggio'];

$sql = "
select viaggio_id,profili.nome, profili.id as id_utente, profili.immagine_profilo
from viaggi_utenti join viaggi on viaggio_id=id
join profili on viaggi_utenti.user_id= profili.id
where viaggio_id= $1
";

$res = pg_query_params($dbconn, $sql, [$id_viaggio]);

$componenti = [];
if ($res) {
  while ($row = pg_fetch_assoc($res)) {
    $componenti[] = $row;
  }
}

header('Content-Type: application/json');
echo json_encode($componenti);
?>
