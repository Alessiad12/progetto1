<?php
session_start();
require 'connessione.php';

?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifiche</title>
  <style>
    body {
      margin: 0;
      font-family: Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #bbeffc, #007cf8);
      min-height: 100vh;
      padding: 1rem;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    h1 {
      color: #ffffff;
      font-size: 2rem;
      margin-bottom: 1rem;
      margin-top: 2rem;
    }

    #notifiche {
      width: 100%;
      max-width: 500px;
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .notifica {
      background-color: #fff;
      border-radius: 16px;
      padding: 1rem;
      margin-bottom: 1rem;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      transition: transform 0.2s;
    }

    .notifica:hover {
      transform: scale(1.02);
    }

    .notifica img {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      margin-right: 1rem;
      object-fit: cover;
    }

    .testo {
      flex: 1;
      color: #333;
    }
  </style>
</head>
<body>

  <h1>notifiche</h1>

  <ul id="notifiche">
    <!-- Notifiche dinamiche qui -->
  </ul>

  <script>
    const notificheRicevute = [
      {
        testo: "Hai fatto match con Giulia!",
        avatar: "https://randomuser.me/api/portraits/women/44.jpg"
      },
      {
        testo: "Marco ti ha mandato un messaggio!",
        avatar: "https://randomuser.me/api/portraits/men/23.jpg"
      },
      {
        testo: "Emma ha messo like al tuo profilo.",
        avatar: "https://randomuser.me/api/portraits/women/65.jpg"
      }
    ];

    const lista = document.getElementById('notifiche');

    notificheRicevute.forEach(({ testo, avatar }) => {
      const li = document.createElement('li');
      li.className = 'notifica';

      li.innerHTML = `
        <img src="${avatar}" alt="Avatar">
        <div class="testo">${testo}</div>
      `;

      lista.appendChild(li);
    });
  </script>
    <script>
    const socket = io('http://localhost:3000');

    const myUserId = 1; // Simula l'organizzatore (chi riceve la notifica)
    socket.emit('join', myUserId);

    socket.on('swipeNotification', data => {
      console.log('Notifica ricevuta:', data);
      document.getElementById('notifica').innerText =
        `Hai ricevuto interesse da utente ${data.fromUser} per il viaggio "${data.tripTitle}"`;
    });
  </script>

</body>
</html>
