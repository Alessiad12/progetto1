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

echo "Dati del form ricevuti: <br>";
echo "ID: $id <br>";
echo "Nome: $nome <br>";
echo "Bio: $bio <br>";
echo "Email: $email <br>";
echo "Compleanno: $compleanno <br>";
echo "Mi interessano: $mi_interessano <br>";
echo "Dream vacation: $dream_vacation <br>";

// Calcolo dell'età
$birthdate = new DateTime($compleanno);
$today = new DateTime();
$eta = $today->diff($birthdate)->y; // Ottieni l'età in anni
echo "Età calcolata: $eta <br>";

// Caricamento dell'immagine (se presente)
$immagine_profilo = null;
if (isset($_FILES['immagine_profilo']) && $_FILES['immagine_profilo']['error'] === UPLOAD_ERR_OK) {
    echo "Immagine profilo ricevuta: " . $_FILES['immagine_profilo']['name'] . "<br>";
    $immagine_profilo = file_get_contents($_FILES['immagine_profilo']['tmp_name']);
} else {
    echo "Nessuna immagine caricata o errore nel caricamento immagine <br>";
}

// Prepara e esegue la query di inserimento nella tabella profili
$sql_profili = "INSERT INTO profili (id, email, nome, eta, bio, colore_sfondo, data_di_nascita, immagine_profilo, posizione_immagine) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

echo "Esecuzione query profili...<br>";

$stmt_profili = $conn->prepare($sql_profili);
if (!$stmt_profili) {
    echo "Errore preparazione query profili: " . $conn->error . "<br>";
} else {
    echo "Query profili preparata correttamente.<br>";
}

$stmt_profili->bind_param("ississbss", $id, $email, $nome, $eta, $bio, $posizione, $compleanno, $immagine_profilo, $posizione);

// Prepara e esegue la query di inserimento nella tabella preferenze_utente_viaggio
$sql_preferenze = "INSERT INTO preferenze_utente_viaggio (utente_id, email, destinazione, data_partenza, data_ritorno, budget, tipo_viaggio, compagnia) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

echo "Esecuzione query preferenze...<br>";

$stmt_preferenze = $conn->prepare($sql_preferenze);
if (!$stmt_preferenze) {
    echo "Errore preparazione query preferenze: " . $conn->error . "<br>";
} else {
    echo "Query preferenze preparata correttamente.<br>";
}

$stmt_preferenze->bind_param("ssssssss", $id, $email, NULL, NULL, NULL, NULL, $dream_vacation, $mi_interessano);

// Esegui entrambe le query
if ($stmt_profili->execute()) {
    echo "Query profili eseguita con successo!<br>";
} else {
    echo "Errore esecuzione query profili: " . $stmt_profili->error . "<br>";
}

if ($stmt_preferenze->execute()) {
    echo "Query preferenze eseguita con successo!<br>";
} else {
    echo "Errore esecuzione query preferenze: " . $stmt_preferenze->error . "<br>";
}

// Chiude le connessioni
$stmt_profili->close();
$stmt_preferenze->close();
$conn->close();

?>
