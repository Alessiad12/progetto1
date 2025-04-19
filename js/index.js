   // Funzione per mostrare/nascondere il contenitore del profilo
   function toggleProfileContainer() {
    const container = document.querySelector('.profile-container');
    container.classList.toggle('active'); // Usa 'active' per mostrare/nascondere
  }

  // Chiudi il menu se clicchi fuori dal contenitore
  window.onclick = function(event) {
    if (!event.target.matches('.profile-toggle')) {
      const container = document.querySelector('.profile-container');
      if (container.classList.contains('active')) {
        container.classList.remove('active'); // Usa 'active' per chiudere il menu
      }
    }
  }

  function caricaModificaProfilo() {
    window.location.href = 'modificaprofilo.html';
  }

  // Caricamento delle informazioni del profilo all'avvio della pagina
  document.addEventListener('DOMContentLoaded', () => {
    fetch('profilo.php')
      .then(response => response.json())
      .then(data => {
        if (!data.error) {
          document.querySelector('.profile-name').textContent = `${data.nome}, ${data.eta}`;
          document.querySelector('.profile-bio').innerHTML = data.bio.replace(/\n/g, '<br>');
          document.querySelector('.profile-container').style.backgroundColor = data.colore_sfondo;
        } else {
          console.error(data.error);
        }
      })
      .catch(error => console.error('Errore:', error));
  });

const container = document.getElementById('cardContainer');
  
function getTopCard() {
  const cards = container.querySelectorAll('.card');
  return cards.length ? cards[cards.length - 1] : null;
}

function swipeCard(direction) {
  const card = getTopCard();
  if (!card) return;

  if (direction === 'right') {
    card.classList.add('accept');
  } else if (direction === 'left') {
    card.classList.add('reject');
  }

  setTimeout(() => {
    card.remove();
  }, 600);
}

// Click su card
container.addEventListener('click', (e) => {
  const card = getTopCard();
  if (!card || isDragging) return;

  const rect = card.getBoundingClientRect();
  const x = e.clientX - rect.left;

  if (x > rect.width / 2) {
    swipeCard('right');
  } else {
    swipeCard('left');
  }
});

// Keyboard
document.addEventListener('keydown', (e) => {
  if (e.key === 'ArrowRight') swipeCard('right');
  else if (e.key === 'ArrowLeft') swipeCard('left');
});

// Touch swipe
let startX = 0;
container.addEventListener('touchstart', e => {
  startX = e.touches[0].clientX;
});
container.addEventListener('touchend', e => {
  const endX = e.changedTouches[0].clientX;
  const diffX = endX - startX;

  if (diffX > 50) swipeCard('right');
  else if (diffX < -50) swipeCard('left');
});

// Pulsanti
document.getElementById('acceptBtn').addEventListener('click', () => swipeCard('right'));
document.getElementById('rejectBtn').addEventListener('click', () => swipeCard('left'));

// === DRAG col mouse (Tinder style) ===
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

  if (dx > 150) {
    currentCard.classList.add('accept');
    setTimeout(() => currentCard.remove(), 500);
  } else if (dx < -150) {
    currentCard.classList.add('reject');
    setTimeout(() => currentCard.remove(), 500);
  } else {
    currentCard.style.transform = 'translate(-50%, -50%)';
  }

  isDragging = false;
  currentCard = null;
});

container.addEventListener('mouseleave', () => {
  if (isDragging && currentCard) {
    currentCard.style.transition = 'transform 0.5s ease';
    currentCard.style.transform = 'translate(-50%, -50%)';
    isDragging = false;
    currentCard = null;
  }
});

window.toggleProfileContainer = toggleProfileContainer;
window.caricaModificaProfilo = caricaModificaProfilo;


