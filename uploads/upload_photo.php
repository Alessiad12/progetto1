<?php
$targetDir = "uploads/";
$email= $_POST['email'];

// Check se esiste immagine
if (isset($_FILES["immagine"]) && $_FILES["immagine"]["error"] == 0) {
    $imageName = basename($_FILES["immagine"]["name"]);
    $imagePath = $targetDir . uniqid() . "_" . $imageName;

    // Sposta il file nella cartella uploads
    if (move_uploaded_file($_FILES["immagine"]["tmp_name"], $imagePath)) {

        require '../connessione.php';

        // Aggiorna il campo immagine_profilo
        $imagePathDb = $dbconn->real_escape_string($imagePath);
        $emailDB = $dbconn->real_escape_string($email);

        $sql = "UPDATE utenti SET immagine_profilo = '$imagePathDb' WHERE email = '$emailDb'";

        if ($dbconn->query($sql) === TRUE) {
            echo "Immagine profilo aggiornata!";
        } else {
            echo "Errore query: " . $dbconn->error;
        }

        $dbconn->close();

    } else {
        echo "Errore nel salvataggio dell'immagine.";
    }
} else {
    echo "Nessuna immagine caricata o errore durante l'upload.";
}
?>
