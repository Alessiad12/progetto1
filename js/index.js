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

function getTopCard() {
  const cards = container.querySelectorAll('.card');
  return cards.length ? cards[cards.length - 1] : null;
}

function swipeCard(direction) {
  const card = getTopCard();
  if (!card) return;
  card.classList.add(direction === 'right' ? 'accept' : 'reject');
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
let currentCard = null;

container.addEventListener('mousedown', (e) => {
  currentCard = getTopCard();
  if (!currentCard) return;
  isDragging = true;
  startPos = { x: e.clientX, y: e.clientY };
  currentCard.style.transition = 'none';
});

container.addEventListener('mousemove', (e) => {
  if (!isDragging || !currentCard) return;
  const dx = e.clientX - startPos.x;
  const dy = e.clientY - startPos.y;
  currentCard.style.transform = `translate(${dx}px, ${dy}px) rotate(${dx * 0.05}deg)`;
});

container.addEventListener('mouseup', (e) => {
  if (!isDragging || !currentCard) return;
  const dx = e.clientX - startPos.x;
  currentCard.style.transition = 'transform 0.5s ease';
  if (dx > 150) swipeCard('right');
  else if (dx < -150) swipeCard('left');
  else currentCard.style.transform = '';
  isDragging = false;
  currentCard = null;
});

container.addEventListener('mouseleave', () => {
  if (isDragging && currentCard) {
    currentCard.style.transition = 'transform 0.5s ease';
    currentCard.style.transform = '';
    isDragging = false;
    currentCard = null;
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

