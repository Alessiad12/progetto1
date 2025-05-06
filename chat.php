<?php
session_start();
// Se non loggato, reindirizza al login
if (!isset($_SESSION['id_utente'])) {
  header('Location: login.php');
  exit;
}
$utente = $_SESSION['id_utente']; // ID dell'utente loggato
$viaggio_id = $_GET['viaggio_id']; // ID del viaggio passato tramite URL
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Viaggio</title>
    <style>
        /* Stili per la chat */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .chat-container {
            width: 80%;
            max-width: 600px;
            height: 80vh;
            display: flex;
            flex-direction: column;
            border: 1px solid #ccc;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .chat-messages {
            flex-grow: 1;
            padding: 10px;
            overflow-y: auto;
            border-bottom: 1px solid #ccc;
        }
        .chat-input {
            display: flex;
            border-top: 1px solid #ccc;
            padding: 10px;
        }
        .chat-input input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .chat-input button {
            margin-left: 10px;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .chat-input button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <!-- Contenitore della chat -->
    <div class="chat-container">
        <div id="chat-container" class="chat-messages">
            <!-- I messaggi verranno qui -->
        </div>
        <div class="chat-input">
            <input type="text" id="messageInput" placeholder="Scrivi un messaggio...">
            <button onclick="sendMessage(<?php echo $viaggio_id; ?>, <?php echo $utente; ?>)">Invia</button> <!-- Passa viaggio_id e id_utente -->
        </div>
    </div>

    <!-- Include la libreria Socket.io -->
    <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
    
    <script>
        const socket = io('http://localhost:4000');  // URL del server Node.js

        // Funzione per inviare un messaggio
        function sendMessage(viaggioId, mittenteId) {
            const messageInput = document.getElementById('messageInput');
            const messaggio = messageInput.value;

            if (messaggio.trim() !== "") {
                // Invia il messaggio al server
                socket.emit('sendMessage', {
                    viaggio_id: viaggioId,
                    messaggio: messaggio,
                    mittente_id: mittenteId
                });

                // Aggiungi il messaggio alla chat (locale, immediato)
                const chatContainer = document.getElementById('chat-container');
                const newMessage = document.createElement('div');
                chatContainer.appendChild(newMessage);

                // Scrolla verso il basso per vedere il messaggio inviato
                chatContainer.scrollTop = chatContainer.scrollHeight;

                // Pulisci il campo di input
                messageInput.value = '';
            }
        }

        // Unirsi alla chat del viaggio (quando l'utente accede alla chat)
        function joinChat(viaggioId) {
            socket.emit('joinChat', viaggioId);
        }

        // Ricevere il messaggio in tempo reale
        socket.on('newMessage', (data) => {
            console.log('Nuovo messaggio:', data);
            // Aggiungi il messaggio alla chat del viaggio
            const chatContainer = document.getElementById('chat-container');
            const newMessage = document.createElement('div');
            newMessage.textContent = `Utente ${data.mittente_id}: ${data.messaggio}`;
            chatContainer.appendChild(newMessage);

            // Scrolla verso il basso per vedere il nuovo messaggio
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });

        // Unirsi alla chat di un viaggio specifico
        joinChat(<?php echo $viaggio_id; ?>);  // Passa l'ID del viaggio alla funzione
    </script>

</body>
</html>
