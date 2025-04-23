// Mostra/nasconde profilo
function toggleProfileContainer() {
  document.querySelector('.profile-container').classList.toggle('active');
}

// Chiudi cliccando fuori
window.onclick = (e) => {
  if (!e.target.closest('.profile-toggle') && document.querySelector('.profile-container').classList.contains('active')) {
    document.querySelector('.profile-container').classList.remove('active');
  }
};

function caricaModificaProfilo() {
  window.location.href = 'modifica_profilo.php';
}

// Carica dati profilo all'avvio
document.addEventListener('DOMContentLoaded', () => {
  fetch('/profilo.php')
    .then(res => res.json())
    .then(data => {
      if (!data.error) {
        document.querySelector('.profile-name').textContent = `${data.nome}, ${data.eta}`;
        document.querySelector('.profile-bio').innerHTML = data.bio.replace(/\n/g, '<br>');
        document.querySelector('.profile-pic').src =  data.immagine_profilo;
        document.querySelector('.profile-icon').src =   data.immagine_profilo;
        document.querySelector('.profile-container').style.backgroundColor = data.colore_sfondo;
      } else {
        console.error(data.error);
      }
    });
});

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

// Mouse click per swipe
container.addEventListener('click', e => {
  const card = getTopCard();
  if (!card || isDragging) return;
  const rect = card.getBoundingClientRect();
  swipeCard(e.clientX - rect.left > rect.width / 2 ? 'right' : 'left');
});

// Swipe da tastiera
document.addEventListener('keydown', e => {
  if (e.key === 'ArrowRight') swipeCard('right');
  else if (e.key === 'ArrowLeft') swipeCard('left');
});

// Swipe touch
let startX = 0;
container.addEventListener('touchstart', e => startX = e.touches[0].clientX);
container.addEventListener('touchend', e => {
  const diffX = e.changedTouches[0].clientX - startX;
  if (diffX > 50) swipeCard('right');
  else if (diffX < -50) swipeCard('left');
});

// Pulsanti su ogni card
container.addEventListener('click', e => {
  if (e.target.classList.contains('accept')) swipeCard('right');
  if (e.target.classList.contains('reject')) swipeCard('left');
});

// Drag manuale stile Tinder
let isDragging = false;
let startPos = { x: 0, y: 0 };
let currentCard = null;

container.addEventListener('mousedown', e => {
  currentCard = getTopCard();
  if (!currentCard) return;
  isDragging = true;
  startPos = { x: e.clientX, y: e.clientY };
  currentCard.style.transition = 'none';
});

container.addEventListener('mousemove', e => {
  if (!isDragging || !currentCard) return;
  const dx = e.clientX - startPos.x;
  const dy = e.clientY - startPos.y;
  currentCard.style.transform = `translate(${dx}px, ${dy}px) rotate(${dx * 0.05}deg)`;
});

container.addEventListener('mouseup', e => {
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

// Esportazione funzioni globali
window.toggleProfileContainer = toggleProfileContainer;
window.caricaModificaProfilo = caricaModificaProfilo;
