const express = require('express');
const http = require('http');
const cors = require('cors');
const bodyParser = require('body-parser');
const { Server } = require('socket.io');
const { Pool } = require('pg');
const cron = require('node-cron'); 
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
  socket.on('sendMessage', async (messageData) => {
  const { viaggio_id, mittente_id, messaggio } = messageData;

  try {
    // 1. Salva nel database
    await pool.query(
      'INSERT INTO chat_viaggio (viaggio_id, utente_id, messaggio) VALUES ($1, $2, $3)',
      [viaggio_id, mittente_id, messaggio]
    );

    // 2. Invia il messaggio a tutti nella stanza
    io.to(`chat_${viaggio_id}`).emit('newMessage', {
      mittente_id,
      messaggio,
      data_creazione: new Date() // opzionale: lo invii anche ai client
    });

    console.log(`Messaggio salvato e inviato per il viaggio ${viaggio_id}`);
  } catch (error) {
    console.error('Errore durante l\'inserimento del messaggio:', error);
  }
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
// da modificare, con uno al giorno
//*/5: Esegui ogni 5 minuti
//*: Ogni ora
//*: Ogni giorno
//*: Ogni mese
//*: Ogni giorno della settimana
cron.schedule('* * * * *', async () => {
  console.log('[CRON] Controllo viaggi terminati...');

  try {
    // 1. Trova i viaggi che sono terminati oggi o prima
    const result = await pool.query(`
      SELECT id AS viaggio_id, user_id AS organizzatore_id, destinazione AS titolo
      FROM viaggi
      WHERE DATE(data_ritorno) <= CURRENT_DATE
    `);

    const viaggiTerminati = result.rows;

    for (const viaggio of viaggiTerminati) {
      const { viaggio_id, organizzatore_id, titolo } = viaggio;

      // 2. Trova i partecipanti del viaggio
      const partecipantiResult = await pool.query(`
        SELECT user_id FROM viaggi_utenti WHERE viaggio_id = $1
      `, [viaggio_id]);

      const partecipanti = partecipantiResult.rows;
      
      // 3. Invia notifica a ciascun partecipante se non già esistente
      for (const partecipante of partecipanti) {
        const userId = partecipante.user_id;

        const notificaEsistente = await pool.query(`
          SELECT 1 FROM notifiche
          WHERE utente_id = $1 AND viaggio_id = $2 AND tipo = 'registra_viaggio'
          LIMIT 1
        `, [userId, viaggio_id]);

        if (notificaEsistente.rows.length === 0) {
          await pool.query(`
            INSERT INTO notifiche (utente_id, mittente_id, viaggio_id, titolo_viaggio, tipo)
            VALUES ($1, $2, $3, $4, $5)
          `, [userId, organizzatore_id, viaggio_id, titolo, 'registra_viaggio']);

          io.to(`user_${userId}`).emit('registraViaggioNotification', {
            tripId: viaggio_id,
            tripTitle: titolo,
            tipo: 'registra_viaggio'
          });

          console.log(`[CRON] Notifica inviata per viaggio "${titolo}" a utente ${userId}`);
        } else {
          console.log(`[CRON] Notifica già esistente per viaggio "${titolo}" e utente ${userId}`);
        }
      }
    }

  } catch (err) {
    console.error('[CRON] Errore durante l\'invio delle notifiche:', err);
  }
});
const { sendEmail } = require('./email');

app.post('/forgot-password', async (req, res) => {
  const { email } = req.body;

  if (!email) return res.status(400).send('Email richiesta');

  try {
    // Qui idealmente cerchi l'utente e generi un token reset (opzionale)
    // Per semplicità, inviamo direttamente la mail

    const resetLink = `http://localhost:3000/nuova_password.php?email=${email}`;

    await sendEmail({
      to: email,
      subject: 'Reset password',
      text: `Hai richiesto di resettare la password. Clicca qui per farlo: ${resetLink}`,
      html: `<p>Hai richiesto di resettare la password.</p><p>Clicca qui per farlo: <a href="${resetLink}">${resetLink}</a></p>`
    });

    res.send('Email inviata, controlla la tua casella');
  } catch (error) {
    console.error('Errore invio email reset password:', error);
    res.status(500).send('Errore nell\'invio email');
  }
});

// Avvio del server
const PORT = 4000;
server.listen(PORT, () => {
  console.log(`Server unificato in ascolto su http://localhost:${PORT}`);
});
