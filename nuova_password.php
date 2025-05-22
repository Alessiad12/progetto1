<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_index.css">
    <link rel="stylesheet" href="css/style_pagina_profilo.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Nuova Password</title>
    <style>
        @font-face {
            font-family: 'CustomFont';
            src: url('../font/8e78142e2f114c02b6e1daaaf3419b2e.woff2') format('woff2');
            font-display: swap;
        }
        @font-face {
            font-family: 'secondo_font';
            src: url('../font/Arimo.7ac02a544211773d9636e056e9da6c35.7.f8f199f09526f79e87644ed227e0f651.woff2') format('woff2');
            font-display: swap;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'CustomFont', sans-serif;
            background-color: #f5f1de;
            color: rgb(8, 7, 91);
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2rem;
            color: rgb(13, 10, 143);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
        } 
    </style>
</head>
<body>
    <div class="container">
        <h1>Nuova Password</h1>
        <form action="nuova_password.php" method="POST">
            <div class="form-group">
                <label for="nuova_password">Nuova Password</label>
                <input type="password" class="form-control" id="nuova_password" name="nuova_password" required>
            </div>
            <div class="form-group">
                <label for="conferma_password">Conferma Password</label>
                <input type="password" class="form-control" id="conferma_password" name="conferma_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Cambia Password</button>
        </form>
        <?php
require_once 'connessione.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Controllo che id_utente sia in sessione
    if (!isset($_SESSION['id_utente'])) {
        echo "<div class='alert alert-danger mt-3'>Sessione scaduta o utente non autenticato.</div>";
        exit;
    }

    $nuova_password = $_POST['nuova_password'] ?? '';
    $conferma_password = $_POST['conferma_password'] ?? '';
    $id = $_SESSION['id_utente'];

    // Debug per verificare che l'id venga preso correttamente
    // print_r($id);

    if ($nuova_password === '' || $conferma_password === '') {
        echo "<div class='alert alert-danger mt-3'>Compila entrambi i campi password.</div>";
        exit;
    }

    if ($nuova_password !== $conferma_password) {
        echo "<div class='alert alert-danger mt-3'>Le password non corrispondono. Riprova.</div>";
        exit;
    }

    // Hashiamo la password
    $hashed_password = password_hash($nuova_password, PASSWORD_BCRYPT);

    // Eseguiamo l'update in modo sicuro
    $sql = "UPDATE utenti SET password = $1 WHERE id = $2";
    $res = pg_query_params($dbconn, $sql, [$hashed_password, $id]);

    if (!$res) {
        echo "<div class='alert alert-danger mt-3'>Errore durante l'aggiornamento della password. Riprova.</div>";
    } else {
        echo "<div class='alert alert-success mt-3'>Password cambiata con successo!</div>";
    }
}


        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("dropdownMenu");
            dropdown.classList.toggle("show");
        }
        window.onclick = function(event) {
            if (!event.target.matches('.profile-icon')) {
                var dropdowns = document.getElementsByClassName("dropdown-menu");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            var dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach(function(dropdown) {
                dropdown.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });
        });
    </script>
</body>