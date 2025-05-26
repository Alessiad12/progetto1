const socket = io('http://localhost:4000');

socket.on('connect', () => {
  socket.emit('join', userId); // userId dev’essere disponibile globalmente
});

socket.on('matchAcceptedNotification', (data) => {
  const modal = document.getElementById("matchModal");
  const text = document.getElementById("matchText");
  text.innerHTML = `<strong>Hai un nuovo match!</strong><br>Viaggio: <strong>"${data.tripTitle}"</strong>`;

  modal.style.display = "block";

  document.getElementById("closeModal").onclick = () => {
    modal.style.display = "none";
  };
});
