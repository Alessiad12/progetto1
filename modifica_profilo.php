<?php
ini_set('display_errors',   0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

session_start();
require 'connessione.php';

if (!isset($_SESSION['id_utente']) || !isset($_SESSION['user'])) { 
  header('Location:/login.php'); 
  exit; 
}

$id_utente = $_SESSION['id_utente'];
$utente_email = $_SESSION['user']; 

$sql = "SELECT u.email,puv.tipo_viaggio as tipo_vacanza, p.immagine_profilo, p.nome, p.bio, p.colore_sfondo
        FROM utenti u
        JOIN profili p ON u.id = p.id
        JOIN preferenze_utente_viaggio puv ON u.id = puv.utente_id
        WHERE u.id = $1";

$result = pg_query_params($dbconn, $sql, [$id_utente]);

if ($row = pg_fetch_assoc($result)) {
    // Dati dal DB senza htmlspecialchars ( per JS)
    $nome = $row['nome'];
    $bio = $row['bio'];
    $email = $row['email'];
    $colore = $row['colore_sfondo'];
    $viaggio = $row['tipo_vacanza'];
    $immagine = $row['immagine_profilo'];
} else {
    // Nessun dato trovato
    $nome = $bio = $email = $colore = $viaggio = $immagine = '';
}

// Array dati profilo 
$profilo = [
    'nome' => $nome,
    'bio' => $bio,
    'email' => $email,
    'colore_sfondo' => $colore,
    'tipo_vacanza' => $viaggio,
    'immagine_profilo' => $immagine
];

// Gestione invio form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $colore = $_POST['colore_preferito'] ?? '#faf3bfc4';
    $viaggio = $_POST['viaggio_scelto'] ?? '';

    $target_dir = "uploads/";
    $foto_nome = "";
    $path_db = null;

    if (isset($_FILES['immagine_profilo']) && $_FILES['immagine_profilo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES["immagine_profilo"]["name"], PATHINFO_EXTENSION);
        $foto_nome = uniqid("profilo_") . "." . $ext;
        $target_file = $target_dir . $foto_nome;

        if (!move_uploaded_file($_FILES["immagine_profilo"]["tmp_name"], $target_file)) {
          error_log("Errore durante il salvataggio dell'immagine. Percorso tmp: " . $_FILES["immagine_profilo"]["tmp_name"]);
          echo json_encode(["success" => false, "error" => "Upload immagine fallito"]);
          exit;
      }
      

        $path_db = $target_file;
    } else {
      // Se non viene caricata una nuova immagine, usa quella esistente
      $path_db = $immagine; // $immagine contiene il valore dal database
  }
    // Gestione posizione immagine
    $currentX = $_POST['currentX'] ?? 50; // 50% come valore predefinito

// Salva nel database
$sql_profilo = "UPDATE profili SET nome = $1, bio = $2, colore_sfondo = $3, immagine_profilo = $4, posizione_immagine = $5 WHERE id = $6";
pg_query_params($dbconn, $sql_profilo, [$nome, $bio, $colore, $path_db, $currentX, $id_utente]);


  
        $sql_utente = "UPDATE preferenze_utente_viaggio SET tipo_viaggio = $1 WHERE id = $2";
        pg_query_params($dbconn, $sql_utente, [$viaggio, $id_utente]);
 

    // Inserisce viaggio (opzionale)
    if (!empty($foto_nome) && !empty($viaggio)) {
        $sql_viaggio = "INSERT INTO viaggi (utente, foto, vacanza, scopo) VALUES ($1, $2, $3, $4)";
        pg_query_params($dbconn, $sql_viaggio, [$id_utente, $foto_nome, $viaggio, 'profilo']);
    }

    pg_close($dbconn);

    // Risposta AJAX
    echo json_encode(["success" => true, "message" => "Profilo aggiornato con successo"]);
    exit;
}
?>



<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Modifica Profilo</title>
  <link rel="stylesheet" href="/css/style_modificaprofilo.css" />
  <script>
  window.profileData = <?= json_encode($profilo) ?>;
</script>

</head>
<body>
  <header>
    <h1>Modifica Profilo</h1>
  </header>

  <div class="container" id="container">
      <div class="profile-header">
        <div class="profile-picture-container" id="imageContainer">
          <img id="profileImage" src="" alt="Immagine Profilo" />
        </div>
        <input type="file" id="fileInput" name="immagine_profilo" accept="image/*">
      </div>

      <div class="bio">
        <label for="bio">Bio:</label>
        <textarea id="bio" name="bio" rows="4" placeholder="Scrivi la tua bio"></textarea>
      </div>

      <label for="nome">Nome:</label>
      <input type="text" id="nome" name="nome" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" readonly>

      <label class="preferito">Viaggio :</label>
      <div class="preferito-options">
        <button type="button" data-value="spiaggia">🏖️ Rilassarsi in spiaggia</button>
        <button type="button" data-value="musei">🏛️ Imparare nei musei</button>
        <button type="button" data-value="ristoranti">🍴 Provare nuovi ristoranti</button>
        <button type="button" data-value="avventura">🧗‍♂️ Avventure all'aria aperta</button>
      </div>

      <div class="colore_preferito">
        <label for="colore_preferito">Colore preferito:</label>
        <div class="color-options">
        <label><input type="radio" name="colore_preferito_radio" value="#f4cedc"><span class="color-swatch" style="background-color: #f4cedc;"></span></label>
        <label><input type="radio" name="colore_preferito_radio" value="#fbe0ce"><span class="color-swatch" style="background-color: #fbe0ce;"></span></label>
        <label><input type="radio" name="colore_preferito_radio" value="#fbfbce"><span class="color-swatch" style="background-color: #fbfbce;"></span></label>
        <label><input type="radio" name="colore_preferito_radio" value="#cef4e3"><span class="color-swatch" style="background-color: #cef4e3;"></span></label>
        <label><input type="radio" name="colore_preferito_radio" value="#cee3f4"><span class="color-swatch" style="background-color: #cee3f4;"></span></label>
          </div>
      </div>

      <input type="hidden" name="viaggio_scelto" id="viaggio_scelto">
      <input type="hidden" name="colore_preferito" id="colore_preferito">

      <button type="submit" class="save-btn">Salva Modifiche</button>
    </form>
  </div>

  <script>
    // Mostra anteprima immagine
    function previewImage(event) {
      const reader = new FileReader();
      reader.onload = function () {
        document.getElementById('previewImg').src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    }

    // Cambia colore di sfondo in diretta
    const colorOptions = document.querySelectorAll('input[name="colore_preferito_radio"]');
    const container = document.getElementById('container');
    const colorePreferitoHidden = document.getElementById('colore_preferito');

    colorOptions.forEach(option => {
      option.addEventListener('change', (event) => {
        container.style.backgroundColor = event.target.value;
        colorePreferitoHidden.value = event.target.value; // aggiorna hidden
      });
    });

    const viaggioButtons = document.querySelectorAll('.preferito-options button');
    const viaggioHidden = document.getElementById('viaggio_scelto');

    // Cambia viaggio preferito
    viaggioButtons.forEach(button => {
      button.addEventListener('click', () => {
        viaggioButtons.forEach(btn => btn.classList.remove('selected'));
        button.classList.add('selected');
        viaggioHidden.value = button.getAttribute('data-value'); // aggiorna hidden
      });
    });

  </script>
   <script>
    // Funzionalità di trascinamento dell'immagine
    const image = document.getElementById("profileImage");
    const imageContainer = document.getElementById("imageContainer");

    let isDragging = false;
    //let startX = 0;
    //let currentX = 50; // percentuale per object-position x

    // Caricamento immagine
    document.getElementById("fileInput").addEventListener("change", function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
          image.src = event.target.result;
          currentX = 50;
          image.style.objectPosition = `${currentX}% center`;
        };
        reader.readAsDataURL(file);
      }
    });
    currentX = 50;
    imageContainer.addEventListener("mousedown", (e) => {
      isDragging = true;
      startX = e.clientX;
      imageContainer.style.cursor = "grabbing";
    });

    window.addEventListener("mousemove", (e) => {
      if (!isDragging) return;
      const deltaX = e.clientX - startX;
      startX = e.clientX;

      currentX = Math.max(0, Math.min(100, currentX + deltaX / 3));
      image.style.objectPosition = `${currentX}% center`;
    });

    window.addEventListener("mouseup", () => {
      isDragging = false;
      imageContainer.style.cursor = "grab";
    });
    document.addEventListener("DOMContentLoaded", function () {
  const data = window.profileData;


  document.getElementById('nome').value = data.nome || '';
  document.getElementById('bio').value = data.bio || '';
  document.getElementById('email').value = data.email || '';
  document.getElementById('colore_preferito').value = data.colore_sfondo || '';
  document.getElementById('viaggio_scelto').value = data.tipo_vacanza || '';

  // Mostra immagine
  const img = document.getElementById('profileImage');
  img.src = data.immagine_profilo ? `${data.immagine_profilo}` : '/immagini/default.png';
  if (data.posizione_immagine) {
    img.style.objectPosition = `${data.posizione_immagine}% center`;
  }
  // Preseleziona colore
  document.querySelectorAll('input[name="colore_preferito_radio"]').forEach(radio => {
    if (radio.value === data.colore_sfondo) {
      radio.checked = true;
      document.getElementById('container').style.backgroundColor = data.colore_sfondo;
    }
  });

  // Preseleziona viaggio
  document.querySelectorAll('.preferito-options button').forEach(button => {
    if (button.dataset.value === data.tipo_vacanza) {
      button.classList.add('selected');
    }
  });
});

  </script>
  <script>

document.querySelector('.save-btn').addEventListener('click', function () {
  const formData = new FormData();
  formData.append('nome', document.getElementById('nome').value);
  formData.append('bio', document.getElementById('bio').value);
  formData.append('colore_preferito', document.getElementById('colore_preferito').value);
  formData.append('viaggio_scelto', document.getElementById('viaggio_scelto').value);
  formData.append('currentX', currentX);


  const fileInput = document.getElementById('fileInput');
  if (fileInput.files[0]) {
    formData.append('immagine_profilo', fileInput.files[0]);
  }

  fetch(window.location.href, {
  method: 'POST',
  body: formData
})
.then(response => response.json())
  .then(data => {
    if (data.success) {
      window.location.href = 'pagina_profilo.php';
    } else {
      alert(data.error || "Errore durante il salvataggio.");
    }
  })
  .catch(error => {
    console.error("Errore AJAX:", error);
    alert("Errore durante la richiesta.");
  });
});
</script>


</body>
</html>