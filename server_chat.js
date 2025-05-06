// Server (Node.js + Socket.IO)
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');

const app = express();
const server = http.createServer(app);
const io = new Server(server, { cors: { origin: '*' } });

app.use(express.json());

// Gestione connessione WebSocket
io.on('connection', (socket) => {
  console.log('Nuovo client connesso:', socket.id);

  // Unisciti alla chat del viaggio
  socket.on('joinChat', (viaggioId) => {
    socket.join(`chat_${viaggioId}`);
    console.log(`Utente ${socket.id} è entrato nella chat del viaggio ${viaggioId}`);
  });

  // Gestisci il messaggio in arrivo
  socket.on('sendMessage', (messageData) => {
    const { viaggio_id, mittente_id, messaggio } = messageData;

    // Invia il messaggio a tutti gli utenti del viaggio
    io.to(`chat_${viaggio_id}`).emit('newMessage', {
      mittente_id,
      messaggio
    });

    console.log(`Messaggio inviato nel viaggio ${viaggio_id}: ${messaggio}`);
  });

  // Disconnessione dell'utente
  socket.on('disconnect', () => {
    console.log('Utente disconnesso:', socket.id);
  });
});

// Avvia il server
const PORT = 4000;
server.listen(PORT, () => {
  console.log(`Server in ascolto su http://localhost:${PORT}`);
});
