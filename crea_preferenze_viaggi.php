<?php
session_start();

if (!isset($_SESSION['id_utente'])) {
  header('Location: login.php');
  exit;
}

require_once __DIR__ . '/connessione.php';
$error = '';
$userId = intval($_SESSION['id_utente']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destinazione  = trim($_POST['destinazione'] ?? '');
    $dataPartenza  = $_POST['data_partenza'] ?? null;
    $dataRitorno   = $_POST['data_ritorno'] ?? null;
    $budgetMin     = floatval($_POST['budget_min'] ?? 0);
    $budgetMax     = floatval($_POST['budget_max'] ?? 0);
    $compagnia     = $_POST['compagnia'] ?? '';
    $tipoViaggio   = $_POST['tipo_viaggio'] ?? '';

    // Calcolo budget medio
    $budget = $budgetMin ."-". $budgetMax;

    // Upload immagine (opzionale)
    $photoPath = null;
    if (!empty($_FILES['foto']['tmp_name']) && is_uploaded_file($_FILES['foto']['tmp_name'])) {
        $uploadsDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
        $filename = uniqid() . '_' . basename($_FILES['foto']['name']);
        $target = $uploadsDir . $filename;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $photoPath = 'uploads/' . $filename; // salva path relativo per DB
        }
    }

    // Inserimento nel DB
 $sql = "UPDATE preferenze_utente_viaggio
        SET destinazione = $1,
            data_partenza = $2,
            data_ritorno = $3,
            budget = $4,
            tipo_viaggio = $5,
            compagnia = $6
        WHERE utente_id = $7";

    $params = [
        $destinazione,
        $dataPartenza,
        $dataRitorno,
        $budget,
        $tipoViaggio,
        $compagnia,
        $userId,
    ];

    $res = pg_query_params($dbconn, $sql, $params);

    if ($res) {
        header('Location: visualizza_viaggi.php'); // oppure una pagina di conferma
        exit;
    } else {
        $error = 'Errore durante l\'inserimento: ' . pg_last_error($dbconn);
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Preferenze Viaggio – Wanderlust</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { colors: { cream: '#FDF7E3', navy: '#0A2342', 'navy-light':'#12315C' } } } };
  </script>
</head>
<body class="bg-cream text-navy min-h-screen flex items-center justify-center">
  <div class="w-full max-w-xl bg-white/60 backdrop-blur-sm rounded-2xl shadow-lg p-8">
    <h1 class="text-2xl font-bold mb-6 text-center">Imposta le tue preferenze di viaggio</h1>
    <?php if ($error): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form  id="form-viaggio" action="crea_preferenze_viaggi.php" method="POST" enctype="multipart/form-data" class="space-y-5">
      <div>
        <label class="block mb-1 font-semibold">Destinazione</label>
        <input name="destinazione" type="text" required placeholder="Roma, Parigi..."
          class="w-full bg-cream border border-navy/30 rounded-lg p-3 focus:ring-1 focus:ring-navy-light"/>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block mb-1 font-semibold">Data Partenza</label>
          <input name="data_partenza" type="date" required
            class="w-full bg-cream border border-navy/30 rounded-lg p-2" />
        </div>
        <div>
          <label class="block mb-1 font-semibold">Data Ritorno</label>
          <input name="data_ritorno" type="date" required
            class="w-full bg-cream border border-navy/30 rounded-lg p-2" />
        </div>
      </div>
        <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block mb-1 font-semibold">Budget min</label>
          <input name="budget_min" type="number" required
            class="w-full bg-cream border border-navy/30 rounded-lg p-2" />
        </div>
        <div>
          <label class="block mb-1 font-semibold">Budget max</label>
          <input name="budget_max" type="number" required
            class="w-full bg-cream border border-navy/30 rounded-lg p-2" />
        </div>
      </div>
      <div>
        <span class="block mb-2 font-semibold">Tipo di viaggio</span>
        <div class="flex gap-3">
          <label class="flex items-center"><input type="radio" name="compagnia" value="coppia"><span class="ml-2">Coppia</span></label>
          <label class="flex items-center"><input type="radio" name="compagnia" value="gruppo"><span class="ml-2">Gruppo</span></label>
        </div>
      </div>
      <div>
        <span class="block mb-2 font-semibold">Vacanza da sogno</span>
        <div class="grid grid-cols-2 gap-2">
          <?php
          $options = ['spiaggia'=>'🏖️ Spiaggia','musei'=>'🏛️ Musei','ristoranti'=>'🍴 Ristoranti','natura'=>'⛰️ Natura'];
          foreach ($options as $val => $label) {
            echo "<label class='flex items-center bg-white/70 rounded-lg p-3 hover:bg-white cursor-pointer'>";
            echo "<input type='radio' name='tipo_viaggio' value='".htmlspecialchars($val)."' class='mr-3' required>";
            echo "<span>$label</span></label>";
          }
          ?>
        </div>
      </div>
      <div>
      <div class="text-center">
        <button type="submit" class="bg-navy text-cream px-8 py-3 rounded-lg font-semibold hover:bg-navy-light">Salva</button>
      </div>
    </form>
  </div>
  <script src="js/cerca_coordinate.js"></script>
</body>
</html>
