const express = require('express');
const http = require('http');
const bodyParser = require('body-parser');
const cors = require('cors');
const { Server } = require('socket.io');
const { Pool } = require('pg');

const pool = new Pool({
  host: 'localhost',
  port: 5432,
  database: 'ConnessionePHP',
  user: 'postgres',
  password: 'html'
});

const app = express();
const server = http.createServer(app);
const io = new Server(server, { cors: { origin: '*' } });

app.use(cors());
app.use(bodyParser.json());

// Mantieni la connessione WebSocket
io.on('connection', (socket) => {
  console.log('Nuovo client connesso:', socket.id);

  socket.on('join', (userId) => {
    socket.join(`user_${userId}`);
    console.log(`Socket ${socket.id} entrato in stanza user_${userId}`);
  });

  socket.on('disconnect', () => {
    console.log('Client disconnesso:', socket.id);
  });
});

// Endpoint per inviare la notifica
app.post('/notify-swipe', async (req, res) => {
    const { userId, fromUser, tripId, tripTitle } = req.body;
  
    try {
      // 1. Salva la notifica nel database
      await pool.query(
        'INSERT INTO notifiche (utente_id, mittente_id, viaggio_id, titolo_viaggio) VALUES ($1, $2, $3, $4)',
        [userId, fromUser, tripId, tripTitle]
      );
  
      // 2. Invia la notifica in tempo reale
      io.to(`user_${userId}`).emit('swipeNotification', {
        fromUser, tripId, tripTitle
      });
  
      console.log(`Notifica swipe inviata a user_${userId}:`, req.body);
      res.sendStatus(200);
    } catch (err) {
      console.error('Errore nel salvataggio:', err);
      res.status(500).send('Errore nel salvataggio');
    }
  });

// Avvio del server
const PORT = 4000;
server.listen(PORT, () => {
  console.log(`Notification server in ascolto su http://localhost:${PORT}`);
});
