<?php
session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente'])) {
    header('Location: login.php');
    exit;
}

$utente_id = intval($_SESSION['id_utente']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID viaggio non valido.<br>";

    exit;
}
echo "Valore ricevuto: ";
echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : 'Nessun ID ricevuto';
$viaggio_id =($_GET['id']);

$sql = "
  SELECT v.*,p.*
  FROM viaggi_terminati v join profili p on 
  p.id=v.utente_id
  WHERE viaggio_id=$1;
";
$res = pg_query_params($dbconn, $sql, [ $viaggio_id ]);
$row = pg_fetch_assoc($res);

?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Descrizione Viaggio</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f7f9fa;
      font-family: Arial, sans-serif;
      padding: 40px;
    }
    .container {
      max-width: 800px;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .foto img {
      width: 100%;
      margin-bottom: 15px;
      border-radius: 8px;
    }
  </style>
</head>
<body>

</div>
</body>
</html>