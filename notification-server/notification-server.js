const express = require('express');
const http = require('http');
const bodyParser = require('body-parser');
const cors = require('cors');
const { Server } = require('socket.io');

const app = express();
const server = http.createServer(app);
const io = new Server(server, { cors: { origin: '*' } });

app.use(cors());
app.use(bodyParser.json());

// Serve il client di Socket.IO
app.use('/socket.io', express.static('node_modules/socket.io/client-dist'));

io.on('connection', socket => {
  console.log('WS client connesso:', socket.id);
  socket.on('join', userId => {
    socket.join(`user_${userId}`);
    console.log(`Socket ${socket.id} entrato in stanza user_${userId}`);
  });
});

app.post('/notify-swipe', (req, res) => {
  const { userId, fromUser, tripId, tripTitle } = req.body;
  io.to(`user_${userId}`).emit('swipeNotification', {
    fromUser, tripId, tripTitle
  });
  console.log(`Notifica swipe inviata a user_${userId}:`, req.body);
  res.sendStatus(200);
});

const PORT = 3000;
server.listen(PORT, () => {
  console.log(`Notification server in ascolto su http://localhost:${PORT}`);
});
