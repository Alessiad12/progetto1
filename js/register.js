document.getElementById('register-form').addEventListener('submit', async function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  const res = await fetch('register.php', {
    method: 'POST',
    body: formData
  });

  const text = await res.text();
  const modal = document.getElementById('modal-message');
  modal.innerHTML = text;
  modal.classList.add('show');

  // Chiude la modale dopo 3 secondi
  setTimeout(() => modal.classList.remove('show'), 3000);
});