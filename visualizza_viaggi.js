const profileIcon = document.querySelector('.profile-icon');
const dropdownMenu = document.getElementById('dropdownMenu');

profileIcon.addEventListener('click', (e) => {
  e.stopPropagation(); // evita la chiusura immediata
  dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
});

// Chiudi se clicchi fuori
window.addEventListener('click', (e) => {
  if (!dropdownMenu.contains(e.target) && !profileIcon.contains(e.target)) {
    dropdownMenu.style.display = 'none';
  }
});


const container = document.getElementById('cardContainer');

function getTopCard() {
  const cards = container.querySelectorAll('.card');
  return cards[cards.length - 1]; // ultima = visibile sopra
}

async function swipeCard(direction) {
  const card = getTopCard();
  if (!card) {
    console.warn('Nessuna card trovata da swipare');
    return;
  }

  const tripId = card.dataset.viaggioId;
  const like = direction === 'right' ? '1' : '0';
  console.log('Preparazione swipe:', { tripId, like, direction });

  // Applica animazione manuale
  const offset = direction === 'left' ? '-150%' : '150%';
  card.style.transform = `translate(${offset}, -50%) rotate(${direction === 'left' ? '-' : ''}20deg)`;
  card.style.opacity = 0;

  // Registra swipe sul server
  try {
    console.log('Invio richiesta al server...');

    const form = new FormData();
    form.append('tripId', tripId);
    form.append('like', like);

    const res = await fetch('swipe.php', {
      method: 'POST',
      credentials: 'same-origin',
      body: form
    });

    console.log('Risposta HTTP ricevuta, status:', res.status);

    if (!res.ok) throw new Error('Errore di rete: ' + res.status);

    const data = await res.json();
    console.log('Risposta JSON dal server:', data);


  } catch (err) {
    console.error('Errore durante lo swipe:', err);
  }

  // Rimuovi la card dopo l'animazione
  setTimeout(() => {
    card.remove();
    console.log('Card rimossa dal DOM');
  }, 600);
}


// Swipe da tastiera
document.addEventListener('keydown', (e) => {
  if (e.key === 'ArrowRight') swipeCard('right');
  if (e.key === 'ArrowLeft') swipeCard('left');
});

function enableMouseSwipe(card) {
  let startX = 0;
  let currentX = 0;
  let isDragging = false;

  const handleMouseDown = (e) => {
    startX = e.clientX;
    isDragging = true;
    card.style.transition = 'none';
  };

  const handleMouseMove = (e) => {
    if (!isDragging) return;
    currentX = e.clientX - startX;
    card.style.transform = `translate(${currentX}px, -50%) rotate(${currentX / 10}deg)`;
  };

  const handleMouseUp = () => {
    if (!isDragging) return;
    isDragging = false;
    card.style.transition = 'transform 0.3s ease';

    if (Math.abs(currentX) > 100) {
      const direction = currentX > 0 ? 'right' : 'left';
      swipeCard(direction);
    } else {
      // Torna in posizione se non è uno swipe valido
      card.style.transform = 'translate(-50%, -50%) rotate(0deg)';
    }

    startX = 0;
    currentX = 0;
  };

  card.addEventListener('mousedown', handleMouseDown);
  window.addEventListener('mousemove', handleMouseMove);
  window.addEventListener('mouseup', handleMouseUp);
}


function enableSwipe(card) {
  let startX = 0;
  let currentX = 0;
  let isDragging = false;

  const start = (x) => {
    startX = x;
    isDragging = true;
    card.style.transition = 'none';
  };

  const move = (x) => {
    if (!isDragging) return;
    currentX = x - startX;
    card.style.transform = `translate(${currentX}px, -50%) rotate(${currentX / 10}deg)`;
  };

  const end = () => {
    if (!isDragging) return;
    isDragging = false;
    card.style.transition = 'transform 0.3s ease';

    if (Math.abs(currentX) > 100) {
      const direction = currentX > 0 ? 'right' : 'left';
      swipeCard(direction);
    } else {
      card.style.transform = 'translate(-50%, -50%) rotate(0deg)';
    }

    startX = 0;
    currentX = 0;
  };

  // Mouse events
  card.addEventListener('mousedown', (e) => start(e.clientX));
  window.addEventListener('mousemove', (e) => move(e.clientX));
  window.addEventListener('mouseup', end);

  // Touch events
  card.addEventListener('touchstart', (e) => start(e.touches[0].clientX));
  card.addEventListener('touchmove', (e) => {
    move(e.touches[0].clientX);
    e.preventDefault(); // Evita lo scroll durante lo swipe
  }, { passive: false });
  card.addEventListener('touchend', end);
}



//componenti viaggio
document.querySelectorAll('.card').forEach(card => {
    const idViaggio = card.dataset.viaggioId;
    console.log('viaggio ID', idViaggio);
    const wrapper = card.querySelector('.componenti-wrapper');
  
    fetch(`get_componenti.php?id_viaggio=${idViaggio}`)
      .then(res => res.json())
      .then(data => {
        console.log('Componenti ricevuti per viaggio ID', idViaggio, data);
        data.forEach(componente => {
          const pallino = document.createElement('a');
          pallino.classList.add('pallino-componente');
          pallino.href = `get_profilo.html?id=${componente.id_utente}`;
          pallino.title = componente.username;
          const img = document.createElement('img');
          img.src = componente.immagine_profilo || 'immagini/default.jpg'; // fallback se non presente
          img.alt = componente.nome;
          img.classList.add('img-pallino'); // stile da definire in CSS
          pallino.appendChild(img);
          wrapper.appendChild(pallino);
        });
      })
      .catch(error => {
        console.error('Errore nel caricamento componenti:', error);
      });
  });

