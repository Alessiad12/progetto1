const express = require('express');
const http = require('http');
const cors = require('cors');
const bodyParser = require('body-parser');
const { Server } = require('socket.io');
const { Pool } = require('pg');

// Config DB
const pool = new Pool({
  host: 'localhost',
  port: 5432,
  database: 'ConnessionePHP',
  user: 'postgres',
  password: 'html'
});

// Setup Express + HTTP + Socket.IO
const app = express();
const server = http.createServer(app);
const io = new Server(server, { cors: { origin: '*' } });

// Middleware
app.use(cors());
app.use(bodyParser.json());

// --- WebSocket ---
io.on('connection', (socket) => {
  console.log('Nuovo client connesso:', socket.id);

  // CHAT VIAGGIO
  socket.on('joinChat', (viaggioId) => {
    socket.join(`chat_${viaggioId}`);
    console.log(`Utente ${socket.id} è entrato nella chat del viaggio ${viaggioId}`);
  });

  socket.on('sendMessage', (messageData) => {
    const { viaggio_id, mittente_id, messaggio } = messageData;

    io.to(`chat_${viaggio_id}`).emit('newMessage', {
      mittente_id,
      messaggio
    });

    console.log(`Messaggio inviato nel viaggio ${viaggio_id}: ${messaggio}`);
  });

  // NOTIFICHE
  socket.on('join', (userId) => {
    socket.join(`user_${userId}`);
    console.log(`Socket ${socket.id} entrato in stanza user_${userId}`);
  });

  socket.on('disconnect', () => {
    console.log('Client disconnesso:', socket.id);
  });
});

// --- REST API ---
app.post('/notify-swipe', async (req, res) => {
  const { userId, fromUser, tripId, tripTitle, tipo } = req.body;

  try {
    // Salva notifica
    await pool.query(
      'INSERT INTO notifiche (utente_id, mittente_id, viaggio_id, titolo_viaggio, tipo) VALUES ($1, $2, $3, $4, $5)',
      [userId, fromUser, tripId, tripTitle, tipo]
    );

    // Invia notifica realtime
    if (tipo === 'like') {
      io.to(`user_${userId}`).emit('swipeNotification', {
        fromUser, tripId, tripTitle, tipo
      });
    } else if (tipo === 'match_accepted') {
      io.to(`user_${fromUser}`).emit('matchAcceptedNotification', {
        fromUser, tripId, tripTitle, tipo
      });
    }

    console.log(`Notifica ${tipo} inviata`);
    res.sendStatus(200);
  } catch (err) {
    console.error('Errore nel salvataggio:', err);
    res.status(500).send('Errore nel salvataggio');
  }
});

// Avvio del server
const PORT = 4000;
server.listen(PORT, () => {
  console.log(`Server unificato in ascolto su http://localhost:${PORT}`);
});