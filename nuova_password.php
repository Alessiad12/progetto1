<?php
session_start();
// Se stai facendo un reset via email, normalmente non serve controllare la sessione.
// Qui assumiamo però che l’utente sia già loggato e abbia già passato il controllo di sessione:
if (!isset($_SESSION['id_utente'])) {
    header('Location: login.html');
    exit;
}

require_once __DIR__ . '/connessione.php';
$error = '';
$userId = intval($_SESSION['id_utente']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuovaPass  = trim($_POST['nuova_password']  ?? '');
    $conferma   = trim($_POST['conferma_password'] ?? '');

    if ($nuovaPass === '' || $conferma === '') {
        $error = 'Entrambi i campi sono obbligatori.';
    }
    elseif ($nuovaPass !== $conferma) {
        $error = 'Le password non corrispondono.';
<<<<<<< HEAD
    }
    else {
        // 1) Recuperiamo l’hash corrente dal DB
        $sql = "SELECT password FROM utenti WHERE id = $1";
        $res = pg_query_params($dbconn, $sql, [$userId]);

        if (!$res || pg_num_rows($res) === 0) {
            $error = 'Utente non trovato.';
        }
        else {
            $row     = pg_fetch_assoc($res);
            $oldHash = $row['password'];

            // 2) Controlliamo che la “nuova” non sia uguale alla “vecchia”
            if (password_verify($nuovaPass, $oldHash)) {
                $error = 'La nuova password non può essere uguale a quella attuale.';
            }
            else {
                // 3) Hashiamo e aggiorniamo
                $newHash   = password_hash($nuovaPass, PASSWORD_DEFAULT);
                $updateSql = "UPDATE utenti SET password = $1 WHERE id = $2";
                $updateRes = pg_query_params($dbconn, $updateSql, [$newHash, $userId]);

                if ($updateRes) {
                    // **Qui mostriamo il popup di successo e poi rimandiamo a login.html**
                    echo "<script>
                            alert('Password cambiata con successo!');
                            window.location.href = 'login.html';
                          </script>";
                    exit;
                }
                else {
                    $error = 'Errore durante l’aggiornamento. Riprova più tardi.';
                }
            }
        }
=======
    } else {
        // Logica di salvataggio nel DB (es. hash e UPDATE)
        // $hashed = password_hash($nuovaPass, PASSWORD_DEFAULT);
        // $sql = "UPDATE utenti SET password = $1 WHERE id = $2";
        // pg_query_params($dbconn, $sql, [$hashed, $_SESSION['id_utente']]);
        header('Location: /login.html');
        exit;
>>>>>>> 1040fa7bf7afbc2c25de10210182dfcd886cfd48
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nuova Password – Wanderlust</title>

  <!-- TailwindCSS + palette personalizzata -->
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
    .bg-overlay {
      position: absolute;
      inset: 0;
      background-color: rgba(255, 255, 255, 0.3);
    }
  </style>
</head>

<body class="text-navy flex flex-col">
  <!-- HEADER FISSO -->
  <header class="fixed top-0 left-0 w-full bg-white/80 backdrop-blur-sm z-20 border-b border-gray-200">
    <div class="flex items-center px-4 sm:px-6 py-3">
      <img src="immagini/logo.png" alt="Logo Wanderlust" class="h-8 w-8 object-contain" />
      <span class="ml-2 text-2xl font-semibold text-navy" style="font-family: 'secondo_font', serif;">
        Wanderlust
      </span>
    </div>
  </header>

  <div class="mt-16"></div>
  <div class="bg-overlay"></div>

  <!-- BOX CENTRALE: “Nuova Password” -->
  <div class="flex-grow flex items-center justify-center px-4">
    <div class="relative w-full max-w-md bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg">
      <div class="px-6 py-8">
        <h1 class="text-2xl font-semibold mb-6 text-center">Nuova Password</h1>

        <!-- Se c’è un errore, lo mostro qui sotto -->
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
                   class="w-full bg-cream border border-navy/30 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-navy-light" />
          </div>

          <!-- Conferma Password -->
          <div>
            <label for="conferma_password" class="block mb-1 font-medium">Conferma Password</label>
            <input id="conferma_password"
                   name="conferma_password"
                   type="password"
                   required
                   class="w-full bg-cream border border-navy/30 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-navy-light" />
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
  </div>
</body>
</html>
