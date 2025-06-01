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
    $nuovaPass  = $_POST['nuova_password'] ?? '';
    $conferma   = $_POST['conferma_password'] ?? '';

    if ($nuovaPass === '' || $conferma === '') {
        $error = 'Entrambi i campi sono obbligatori.';
    } elseif ($nuovaPass !== $conferma) {
        $error = 'Le password non corrispondono.';
    } else {
        // Logica di salvataggio nel DB (es. hash e UPDATE)
        // $hashed = password_hash($nuovaPass, PASSWORD_DEFAULT);
        // $sql = "UPDATE utenti SET password = $1 WHERE id = $2";
        // pg_query_params($dbconn, $sql, [$hashed, $_SESSION['id_utente']]);
        header('Location: /pagina_profilo.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nuova Password</title>

  <!-- TailwindCSS -->
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

  <style>
    /* Body con sfondo immagine e overlay */
    body {
      position: relative;
      min-height: 100vh;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-image: url('immagini/mongo.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }
    /* Overlay chiaro sopra l'immagine di sfondo */
    .bg-overlay {
      position: absolute;
      inset: 0;
      background-color: rgba(255, 255, 255, 0.3);
      /* aumenta o diminuisci l'opacità per variare l'effetto */
    }
  </style>
</head>

<body class="text-navy flex items-center justify-center p-4">
  <!-- Overlay bianco semitrasparente -->
  <div class="bg-overlay"></div>

  <!-- Container centrale -->
  <div class="relative w-full max-w-md bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg">
    <div class="px-6 py-8">
      <h1 class="text-2xl font-semibold mb-6 text-center">Nuova Password</h1>

      <?php if ($error): ?>
        <div class="mb-4 px-4 py-2 bg-red-100 text-red-700 rounded">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form action="nuova_password.php" method="POST" class="space-y-4">
        <!-- Nuova Password -->
        <div>
          <label for="nuova_password" class="block mb-1 font-medium">Nuova Password</label>
          <input id="nuova_password"
                 name="nuova_password"
                 type="password"
                 required
                 class="w-full bg-cream border border-navy/30 rounded-lg p-2 focus:ring-2 focus:ring-navy-light" />
        </div>

        <!-- Conferma Password -->
        <div>
          <label for="conferma_password" class="block mb-1 font-medium">Conferma Password</label>
          <input id="conferma_password"
                 name="conferma_password"
                 type="password"
                 required
                 class="w-full bg-cream border border-navy/30 rounded-lg p-2 focus:ring-2 focus:ring-navy-light" />
        </div>

        <!-- Pulsante Cambia Password -->
        <div class="text-center pt-4">
          <button type="submit"
                  class="bg-navy text-cream px-6 py-2 rounded-lg font-medium hover:bg-navy-light transition">
            Cambia Password
          </button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
