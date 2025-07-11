// ==== Import comuni ====
const express = require('express');
const path = require('path');
const http = require('http');
const cors = require('cors');
const bodyParser = require('body-parser');
const { Server } = require('socket.io');
const { Pool } = require('pg');
const cron = require('node-cron');
const { sendEmail } = require('./email');

// ==== Setup Express + HTTP + Socket.IO ====
const app = express();
const server = http.createServer(app);
const io = new Server(server, { cors: { origin: '*' } });

// ==== Middleware ====
app.use(cors());
app.use(bodyParser.json());
app.use(express.static(path.join(__dirname, 'public'))); // static files (chat AI UI)

// ==== Config DB ====
const pool = new Pool({
  host: 'localhost',
  port: 5432,
  database: 'ConnessionePHP',
  user: 'postgres',
  password: 'html'
});

// ==== WebSocket ====
io.on('connection', (socket) => {
  console.log('✅ Nuovo client connesso:', socket.id);

  socket.on('joinChat', (viaggioId) => {
    socket.join(`chat_${viaggioId}`);
    console.log(`🟢 Utente ${socket.id} in chat viaggio ${viaggioId}`);
  });

  socket.on('sendMessage', async (messageData) => {
    const { viaggio_id, mittente_id, messaggio } = messageData;

    try {
      await pool.query(
        'INSERT INTO chat_viaggio (viaggio_id, utente_id, messaggio) VALUES ($1, $2, $3)',
        [viaggio_id, mittente_id, messaggio]
      );

      io.to(`chat_${viaggio_id}`).emit('newMessage', {
        mittente_id,
        messaggio,
        data_creazione: new Date()
      });

      console.log(`💬 Messaggio per viaggio ${viaggio_id} inviato`);
    } catch (error) {
      console.error('❌ Errore salvataggio messaggio:', error);
    }
  });

  socket.on('join', (userId) => {
    socket.join(`user_${userId}`);
    console.log(`🔔 Socket ${socket.id} unito a user_${userId}`);
  });

  socket.on('disconnect', () => {
    console.log('🚪 Client disconnesso:', socket.id);
  });
});

// ==== Route: AI Chat (Ollama) ====
app.post('/chat', async (req, res) => {
  const prompt = req.body.prompt;

  try {
    const response = await fetch('http://localhost:11434/api/generate', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ model: 'mistral', prompt, stream: false })
    });

    const data = await response.json();
    res.json({ reply: data.response });
  } catch (error) {
    console.error('❌ Errore Ollama:', error);
    res.status(500).send('Errore nel server');
  }
});

// ==== Route: Notifiche swipe ====
app.post('/notify-swipe', async (req, res) => {
  const { userId, fromUser, tripId, tripTitle, tipo } = req.body;

  try {
    await pool.query(
      'INSERT INTO notifiche (utente_id, mittente_id, viaggio_id, titolo_viaggio, tipo) VALUES ($1, $2, $3, $4, $5)',
      [userId, fromUser, tripId, tripTitle, tipo]
    );

    if (tipo === 'like') {
      io.to(`user_${userId}`).emit('swipeNotification', { fromUser, tripId, tripTitle, tipo });
    } else if (tipo === 'match_accepted') {
      io.to(`user_${userId}`).emit('matchAcceptedNotification', { fromUser, tripId, tripTitle, tipo });
    }

    console.log(`🔔 Notifica ${tipo} inviata`);
    res.sendStatus(200);
  } catch (err) {
    console.error('❌ Errore notifica:', err);
    res.status(500).send('Errore nel salvataggio');
  }
});

// ==== CRON: Notifiche viaggi terminati ====
cron.schedule('* * * * *', async () => {
  console.log('[CRON] Controllo viaggi terminati...');

  try {
    const result = await pool.query(`
      SELECT id AS viaggio_id, user_id AS organizzatore_id, destinazione AS titolo
      FROM viaggi WHERE DATE(data_ritorno) <= CURRENT_DATE
    `);

    for (const viaggio of result.rows) {
      const { viaggio_id, organizzatore_id, titolo } = viaggio;

      const partecipantiResult = await pool.query(
        'SELECT user_id FROM viaggi_utenti WHERE viaggio_id = $1', [viaggio_id]
      );

      for (const partecipante of partecipantiResult.rows) {
        const userId = partecipante.user_id;

        const notificaEsistente = await pool.query(`
          SELECT 1 FROM notifiche WHERE utente_id = $1 AND viaggio_id = $2 AND tipo = 'registra_viaggio' LIMIT 1
        `, [userId, viaggio_id]);

        if (notificaEsistente.rows.length === 0) {
          await pool.query(`
            INSERT INTO notifiche (utente_id, mittente_id, viaggio_id, titolo_viaggio, tipo)
            VALUES ($1, $2, $3, $4, 'registra_viaggio')
          `, [userId, organizzatore_id, viaggio_id, titolo]);

          io.to(`user_${userId}`).emit('registraViaggioNotification', {
            tripId: viaggio_id, tripTitle: titolo, tipo: 'registra_viaggio'
          });

          console.log(`[CRON] Notifica inviata a utente ${userId} per viaggio "${titolo}"`);
        }
      }
    }
  } catch (err) {
    console.error('[CRON] Errore:', err);
  }
});

// ==== Route: Password reset ====
app.post('/forgot-password', async (req, res) => {
  const { email } = req.body;
  if (!email) return res.status(400).send('Email richiesta');

  try {
    const resetLink = `http://localhost:3000/nuova_password.php?email=${email}`;
    await sendEmail({
      to: email,
      subject: 'Reset password',
      text: `Hai richiesto di resettare la password. Clicca qui: ${resetLink}`,
      html: `<p>Clicca qui per resettare: <a href="${resetLink}">${resetLink}</a></p>`
    });

    res.send('Email inviata');
  } catch (error) {
    console.error('Errore invio email reset:', error);
    res.status(500).send('Errore nell\'invio');
  }
});

// ==== Route: Beta signup ====
app.post('/beta-signup', async (req, res) => {
  const { email } = req.body;

  if (!email || !email.includes('@')) {
    return res.status(400).send('Email non valida');
  }

  try {
    await sendEmail({
      to: email,
      subject: 'Benvenuto nella beta di Wanderlust ✈️',
      html: `<p>Grazie per esserti iscritto!</p><p>Ti aggiorneremo presto 🚀</p>`
    });

    res.status(200).send('Email inviata con successo!');
  } catch (error) {
    console.error('Errore email beta:', error);
    res.status(500).send('Errore durante l\'invio');
  }
});

// ==== Avvio server ====
const PORT = 4000;
server.listen(PORT, () => {
  console.log(`✅ Server unificato attivo su http://localhost:${PORT}`);
});
