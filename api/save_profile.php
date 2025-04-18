<?php
session_start();
require '../config.php';          // connessione DB + session_start()

if (!isset($_SESSION['user_id'])) {
  header('Location: /login.php'); exit;
}
$u = $_SESSION['user_id'];
$d = $_POST['dream_vacation']  ?? '';
$p = $_POST['trip_purpose']    ?? '';

// 1) salva preferenze
pg_query_params($dbconn,
  "UPDATE utenti SET dream_vacation=$1, trip_purpose=$2 WHERE id=$3",
  [$d, $p, $u]
);

// 2) salva le foto
if (!empty($_FILES['photos'])) {
  foreach ($_FILES['photos']['tmp_name'] as $i => $tmp) {
    $name = uniqid() . '_' . basename($_FILES['photos']['name'][$i]);
    move_uploaded_file($tmp, __DIR__.'/../uploads/'.$name);
    pg_query_params($dbconn,
      "INSERT INTO trips (user_id, photo_path, vacation_type, purpose)
       VALUES ($1,$2,$3,$4)",
      [$u, $name, $d, $p]
    );
  }
}

// 3) redirect alla home
header('Location: /index.php');
exit;
