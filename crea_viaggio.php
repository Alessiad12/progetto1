<?php
session_start();
// Se non loggato, reindirizza al login
if (!isset($_SESSION['id_utente'])) {
  header('Location: login.php');
  exit;
}
require_once __DIR__ . '/connessione.php';
$error = '';
$userId = intval($_SESSION['id_utente']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destinazione  = trim($_POST['destinazione'] ?? '');
    $descrizione   = trim($_POST['descrizione'] ?? '');
    $dataPartenza  = $_POST['data_partenza'] ?? null;
    $dataRitorno   = $_POST['data_ritorno'] ?? null;
    $budget        = floatval($_POST['budget'] ?? 0);
    $compagnia     = $_POST['compagnia'] ?? '';
    $tipoViaggio   = $_POST['tipo_viaggio'] ?? '';
    $latitudine    = $_POST['latitudine'] ?? null;
    $longitudine   = $_POST['longitudine'] ?? null;

    $photoPath = null;
    if (!empty($_FILES['foto']['tmp_name']) && is_uploaded_file($_FILES['foto']['tmp_name'])) {
        $uploadsDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
        $filename = uniqid() . '_' . basename($_FILES['foto']['name']);
        $target = $uploadsDir . $filename;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $photoPath = '/uploads/' . $filename;
        }
    }

    // Inserimento viaggio
    $sql1 = "INSERT INTO viaggi (
        user_id, destinazione, data_partenza, data_ritorno, budget, tipo_viaggio,
        lingua, compagnia, descrizione, foto, latitudine, longitudine
    ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12) RETURNING id";

    $params1 = [
        $userId,
        $destinazione,
        $dataPartenza,
        $dataRitorno,
        $budget,
        $tipoViaggio,
        null, // oppure $lingua se previsto
        $compagnia,
        $descrizione,
        $photoPath,
        $latitudine,
        $longitudine
    ];

    $res1 = pg_query_params($dbconn, $sql1, $params1);
    if (!$res1) {
        $error = pg_last_error($dbconn);
    } else {
        $row = pg_fetch_assoc($res1);
        $viaggioId = $row['id'];

        // Inserimento nella tabella viaggi_utenti
        $sql2 = "INSERT INTO viaggi_utenti (viaggio_id, user_id, ruolo) VALUES ($1, $2, $3)";
        $params2 = [$viaggioId, $userId, 'ideatore'];
        $res2 = pg_query_params($dbconn, $sql2, $params2);

        if ($res2) {
            header('Location: /pagina_profilo.php');
            exit;
        } else {
            $error = pg_last_error($dbconn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Crea Nuovo Viaggio – Wanderlust</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            cream: '#FDF7E3',
            navy: '#0A2342',
            'navy-light': '#12315C'
          }
        }
      }
    };
  </script>
</head>
<body class="bg-cream text-navy min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-xl bg-white/60 backdrop-blur-sm rounded-2xl shadow-lg p-4 md:p-8">
    <h1 class="text-xl md:text-2xl font-bold mb-6 text-center">Crea Un Nuovo Viaggio</h1>
    <?php if ($error): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form id="form-viaggio"
          action="crea_viaggio.php"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-4 md:space-y-6"
    >
      <!-- Destinazione -->
      <div>
        <label for="destinazione" class="block mb-1 font-semibold">Destinazione</label>
        <input id="destinazione"
               name="destinazione"
               type="text"
               required
               placeholder="Roma, Parigi..."
               class="w-full bg-cream border border-navy/30 rounded-lg p-3 focus:ring-1 focus:ring-navy-light"
        />
      </div>

      <!-- Descrizione -->
      <div>
        <label for="descrizione" class="block mb-1 font-semibold">Descrizione</label>
        <textarea id="descrizione"
                  name="descrizione"
                  rows="3"
                  required
                  placeholder="Dettagli del viaggio..."
                  class="w-full bg-cream border border-navy/30 rounded-lg p-3 focus:ring-1 focus:ring-navy-light resize-none"
        ></textarea>
      </div>

      <!-- Date: 1 col su mobile, 2 col su md+ -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="data_partenza" class="block mb-1 font-semibold">Data Partenza</label>
          <input id="data_partenza"
                 name="data_partenza"
                 type="date"
                 required
                 class="w-full bg-cream border border-navy/30 rounded-lg p-2 focus:ring-1 focus:ring-navy-light"
          />
        </div>
        <div>
          <label for="data_ritorno" class="block mb-1 font-semibold">Data Ritorno</label>
          <input id="data_ritorno"
                 name="data_ritorno"
                 type="date"
                 required
                 class="w-full bg-cream border border-navy/30 rounded-lg p-2 focus:ring-1 focus:ring-navy-light"
          />
        </div>
      </div>

      <!-- Budget -->
      <div>
        <label for="budget" class="block mb-1 font-semibold">Budget (€)</label>
        <input id="budget"
               name="budget"
               type="number"
               min="0"
               step="0.01"
               required
               placeholder="1000.00"
               class="w-full bg-cream border border-navy/30 rounded-lg p-3 focus:ring-1 focus:ring-navy-light"
        />
      </div>

      <!-- Tipo di viaggio -->
      <div>
        <span class="block mb-2 font-semibold">Tipo di viaggio</span>
        <div class="flex flex-wrap gap-6">
          <label class="flex items-center space-x-2">
            <input type="radio"
                   name="compagnia"
                   value="coppia"
                   class="h-5 w-5 text-navy"
                   required
            />
            <span>Coppia</span>
          </label>
          <label class="flex items-center space-x-2">
            <input type="radio"
                   name="compagnia"
                   value="gruppo"
                   class="h-5 w-5 text-navy"
                   required
            />
            <span>Gruppo</span>
          </label>
        </div>
      </div>

      <!-- Vacanza da sogno: 1 col su mobile, 2 col su sm+ -->
      <div>
        <span class="block mb-2 font-semibold">Vacanza da sogno</span>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
          <?php
          $options = [
            'spiaggia'   => '🏖️ Spiaggia',
            'musei'      => '🏛️ Musei',
            'ristoranti' => '🍴 Ristoranti',
            'natura'     => '⛰️ Natura'
          ];
          foreach ($options as $val => $label) {
            echo "<label class='flex items-center bg-white/70 rounded-lg p-3 hover:bg-white cursor-pointer'>";
            echo   "<input type='radio' name='tipo_viaggio' value='" . htmlspecialchars($val) . "' class='mr-3' required>";
            echo   "<span>$label</span>";
            echo "</label>";
          }
          ?>
        </div>
      </div>

      <!-- Foto (opzionale) -->
      <div>
        <label for="foto" class="block mb-1 font-semibold">Foto (opzionale)</label>
        <input id="foto"
               name="foto"
               type="file"
               accept="image/*"
               class="w-full bg-cream border border-navy/30 rounded-lg p-2 cursor-pointer"
        />
      </div>

      <!-- Pulsante di invio -->
      <div class="text-center">
        <button type="submit" class="bg-navy text-cream px-8 py-3 rounded-lg font-semibold hover:bg-navy-light transition">
          Crea Viaggio
        </button>
      </div>
    </form>
  </div>

  <!-- Script per geolocalizzazione (se necessario) -->
  <script src="js/cerca_coordinate.js"></script>
</body>
</html>
