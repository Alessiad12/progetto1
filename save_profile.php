<?php
session_start();
require 'connessione.php';  // connessione al DB

if (!isset($_SESSION['id_utente'])) {
    header('Location: /login.php');
    exit;
}

$u = $_SESSION['id_utente'];  // ID dell'utente dalla sessione

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera i dati dal form
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $giorno = $_POST['giorno'];
    $mese = $_POST['mese'];
    $anno = $_POST['anno'];
    $dream_vacation = $_POST['dream_vacation'];

    // Gestire gli interessi (sono passati come array)
    $interessi = isset($_POST['interessi']) ? implode(", ", $_POST['interessi']) : null;

    // Foto profilo (se presente)
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatarTmp = $_FILES['avatar']['tmp_name'];
        $avatarName = $_FILES['avatar']['name'];
        $avatarPath = "uploads/profiles/" . basename($avatarName);

        if (move_uploaded_file($avatarTmp, $avatarPath)) {
            // Salva la foto nel database (se l'upload è riuscito)
            $foto_profilo = $avatarPath;
        } else {
            $foto_profilo = null;
        }
    } else {
        $foto_profilo = null; // Se non è stata caricata una foto
    }

    // Salva i dati nel database
    $sql = "UPDATE utenti SET nome = ?, email = ?, data_nascita = ?, interessi = ?, foto_profilo = ?, dream_vacation = ? WHERE id_utente = ?";
    $stmt = $conn->prepare($sql);
    $data_nascita = "$anno-$mese-$giorno";  // Formatta la data nel formato YYYY-MM-DD
    $stmt->bind_param("ssssssi", $nome, $email, $data_nascita, $interessi, $foto_profilo, $dream_vacation, $u);

    if ($stmt->execute()) {
        echo "<p>Profilo aggiornato con successo!</p>";
        // Puoi reindirizzare o mostrare un messaggio di successo
        header("Location: profilo.php");
        exit;
    } else {
        echo "<p>Errore nell'aggiornamento del profilo.</p>";
    }
}
?>
