<?php
session_start();
if (!isset($_SESSION['id_utente'])) {
  header('Location: login.php');
  exit;
}

$utente_id = $_SESSION['id_utente'];
$viaggio_id = $_GET['viaggio_id'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Registra la tua esperienza</title>
</head>
<body>
  <h2>Termina il tuo viaggio</h2>
  <form action="salva_viaggio_terminato.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="utente_id" value="<?= $utente_id ?>">
    <input type="hidden" name="viaggio_id" value="<?= $viaggio_id ?>">

    <label>Descrizione:</label><br>
    <textarea name="descrizione" rows="6" cols="50" required></textarea><br><br>

    <label>Valutazione (1-5):</label>
    <input type="number" name="valutazione" min="1" max="5" required><br><br>

    <label>Foto (max 5):</label>
    <input type="file" name="foto[]" accept="image/*" multiple><br><br>

    <button type="submit">Salva</button>
  </form>
</body>
</html>
