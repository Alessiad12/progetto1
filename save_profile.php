<?php
require 'connessione.php'; // Connessione al database

// Recupero i dati dal form
$id = $_SESSION['id_utente'];
$nome = $_POST['name'];
$bio = $_POST['bio'];
$email = $_POST['email'];
$compleanno = $_POST['compleanno']; // Formato della data: YYYY-MM-DD
$mi_interessano = $_POST['mi_interessano']; // Può essere un valore come 'viaggi in solitaria', 'coppia', ecc.
$dream_vacation = $_POST['dream_vacation'];
$posizione = null; // Può essere un valore come 'montagna', 'mare', ecc.

$birthdate = new DateTime($compleanno);
$today = new DateTime();
$eta = $today->diff($birthdate)->y; // Ottieni l'età in anni

// Caricamento dell'immagine (se presente)
$immagine_profilo = null;
if (isset($_FILES['immagine_profilo']) && $_FILES['immagine_profilo']['error'] === UPLOAD_ERR_OK) {
    $immagine_profilo = file_get_contents($_FILES['immagine_profilo']['tmp_name']);
}

// Prepara e esegue la query di inserimento nella tabella profili
$sql_profili = "INSERT INTO profili (id, email, nome, eta, bio, colore_sfondo, data_di_nascita, immagine_profilo, posizione_immagine) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt_profili = $conn->prepare($sql_profili);
$stmt_profili->bind_param("ississbss", $id, $email, $nome, $eta, $bio, $posizione, $compleanno, $immagine_profilo, $posizione);

// Prepara e esegue la query di inserimento nella tabella preferenze_utente_viaggio
$sql_preferenze = "INSERT INTO preferenze_utente_viaggio (utente_id, email, destinazione, data_partenza, data_ritorno, budget, tipo_viaggio, compagnia) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt_preferenze = $conn->prepare($sql_preferenze);
$stmt_preferenze->bind_param("ssssssss", $id, $email, $dream_vacation, NULL, NULL, NULL, $mi_interessano, NULL);

// Esegui entrambe le query
if ($stmt_profili->execute() && $stmt_preferenze->execute()) {
    echo "Registrazione completata con successo!";
} else {
    echo "Errore: " . $stmt_profili->error . " | " . $stmt_preferenze->error;
}

// Chiude le connessioni
$stmt_profili->close();
$stmt_preferenze->close();
$conn->close();
?>
