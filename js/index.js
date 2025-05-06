// Funzione per aprire/chiudere il menu
function toggleDropdown() {
  console.log("toggleDropdown chiamato");
  const menu = document.getElementById("dropdownMenu");
  menu.style.display = menu.style.display === "block" ? "none" : "block";
}

// Chiudi il menu se si clicca fuori dal menu
window.addEventListener("click", function (e) {
  const wrapper = document.querySelector(".profile-menu-wrapper");
  const menu = document.getElementById("dropdownMenu");
  if (!wrapper.contains(e.target)) {
    menu.style.display = "none";
  }
});

// Gestisci il click sul profilo
document.querySelector(".profile-icon").addEventListener("click", (e) => {
  e.stopPropagation();  // Evita che il click venga propagato al body e chiuda il menu
  toggleDropdown();
});

// Funzione per ottenere la carta superiore
const container = document.getElementById('cardContainer');

const modal     = document.getElementById('match-modal');
const modalText = document.getElementById('modal-text');
const btnChat   = document.getElementById('btn-chat');
const btnClose  = document.getElementById('btn-close');
let   currentConv = null;


function getTopCard() {
  const cards = container.querySelectorAll('.card');
  return cards.length ? cards[cards.length - 1] : null;
}

// Chiama il backend per registrare lo swipe
async function registerSwipe(tripId, like) {
  const form = new FormData();
  form.append('tripId', tripId);
  form.append('like', like);
  const res = await fetch('swipe.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: form
  });
  if (!res.ok) throw new Error('Network error');
  return res.json(); // { success, isMatch, conversationId, tripTitle }
}

// Mostra il modal di match
function showMatchModal(tripTitle, conversationId) {
  currentConv = conversationId;
  modalText.textContent = `Hai mostrato interesse per “${tripTitle}”!`;
  modal.classList.remove('hidden');
}

async function swipeCard(direction) {
  const card = getTopCard();
  if (!card) return;

  const tripId = card.dataset.viaggioId;
  const like   = direction === 'right' ? '1' : '0';

  // animazione visiva
  card.classList.add(direction === 'right' ? 'accept' : 'reject');

  // registra lo swipe sul server
  try {
    const data = await registerSwipe(tripId, like);
    if (data.isMatch) {
      showMatchModal(data.tripTitle, data.conversationId);
    }
  } catch (err) {
    console.error('Errore swipe:', err);
  }

  // rimuovi la card dopo l'animazione
  setTimeout(() => card.remove(), 600);
}


// Swipe da tastiera
document.addEventListener('keydown', (e) => {
  if (e.key === 'ArrowRight') swipeCard('right');
  else if (e.key === 'ArrowLeft') swipeCard('left');
});

// Swipe touch
let startX = 0;
container.addEventListener('touchstart', (e) => startX = e.touches[0].clientX);
container.addEventListener('touchend', (e) => {
  const diffX = e.changedTouches[0].clientX - startX;
  if (diffX > 50) swipeCard('right');
  else if (diffX < -50) swipeCard('left');
});

// Drag manuale stile Tinder
let isDragging = false;
let startPos = { x: 0, y: 0 };
let draggedCard = null;

container.addEventListener('mousedown', (e) => {
  draggedCard = getTopCard();
  if (!draggedCard) return;
  isDragging = true;
  startPos = { x: e.clientX, y: e.clientY };
  draggedCard.style.transition = 'none';
});

container.addEventListener('mousemove', (e) => {
  if (!isDragging || !draggedCard) return;
  const dx = e.clientX - startPos.x;
  const dy = e.clientY - startPos.y;
  draggedCard.style.transform = `translate(${dx}px, ${dy}px) rotate(${dx * 0.05}deg)`;
});

container.addEventListener('mouseup', (e) => {
  if (!isDragging || !draggedCard) return;
  const dx = e.clientX - startPos.x;
  draggedCard.style.transition = 'transform 0.5s ease';
  if (dx > 150) swipeCard('right');
  else if (dx < -150) swipeCard('left');
  else draggedCard.style.transform = '';
  isDragging = false;
  draggedCard = null;
});

container.addEventListener('mouseleave', () => {
  if (isDragging && draggedCard) {
    draggedCard.style.transition = 'transform 0.5s ease';
    draggedCard.style.transform = '';
    isDragging = false;
    draggedCard = null;
  }
});


async function loadProfilePicture() {
  try {
    // Effettua una richiesta al file profilo.php
    const response = await fetch('/profilo.php');
    const data = await response.json();

    if (data.immagine_profilo) {
      // Aggiorna l'attributo src dell'immagine con la classe profile-icon
      const profileIcon = document.querySelector('.profile-icon');
      profileIcon.src = data.immagine_profilo;
    } else {
      console.error('Errore: Immagine del profilo non trovata');
    }
  } catch (error) {
    console.error('Errore durante il caricamento dell\'immagine del profilo:', error);
  }
}


// Chiama la funzione quando la pagina è caricata
document.addEventListener('DOMContentLoaded', loadProfilePicture);

btnChat.addEventListener('click', () => {
  window.location.href = `chat.php?conv=${currentConv}`;
});
btnClose.addEventListener('click', () => {
  modal.classList.add('hidden');
});
