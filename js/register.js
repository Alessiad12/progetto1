/*function register() {
    const name = document.getElementById("reg-name").value;
    const email = document.getElementById("reg-email").value;
    const password = document.getElementById("reg-password").value;
  
    const errorDiv = document.getElementById("register-error");
    const successDiv = document.getElementById("register-success");
    const formReg = document.getElementById("form-registrazione");
  
    // Pulisci eventuali messaggi e animazioni precedenti
    errorDiv.textContent = "";
    successDiv.textContent = "";
    errorDiv.classList.remove("shake");
    formReg.classList.remove("shake");

    if (!name || !email || !password) {
      document.getElementById("register-error").textContent = "Compila tutti i campi!";
      return;
  }
    // Creazione dell'oggetto utente
    const user = { name, email, password };
  
    // Recupera la lista degli utenti
    const users = JSON.parse(localStorage.getItem("users")) || [];
  
    // Controllo se l'email esiste già
    let email_esistente = false;
    for (let i = 0; i < users.length; i++) {
      if (users[i].email === email) {
        email_esistente = true;
        break;
      }
    }
  
    if (email_esistente) {
      // Errore: Email già registrata
      errorDiv.textContent = "Email già registrata! Effettua il login.";
      formReg.classList.add("shake");
      return; // Ferma l’esecuzione
    }
  
    // Altrimenti aggiungiamo l’utente
    users.push(user);
    localStorage.setItem("users", JSON.stringify(users));
  
    // Salva il nome dell'utente attivo
localStorage.setItem("userName", name);

// Messaggio di successo
successDiv.textContent = "Registrazione avvenuta con successo! Verrai reindirizzato al login.";

setTimeout(() => {
    window.location.href = "login.html";
}, 2500);
  }*/ function register() {
  const nameInput = document.getElementById("reg-name");
  const emailInput = document.getElementById("reg-email");
  const passwordInput = document.getElementById("reg-password");

  const name = nameInput.value.trim();
  const email = emailInput.value.trim();
  const password = passwordInput.value.trim();

  const errorDiv = document.getElementById("register-error");
  const successDiv = document.getElementById("register-success");
  const formReg = document.getElementById("form-registrazione");

  // Reset dei messaggi e delle classi animazione
  errorDiv.textContent = "";
  successDiv.textContent = "";
  errorDiv.classList.remove("shake");
  formReg.classList.remove("shake");

  // (1) Controlla se i campi sono vuoti
  if (!name || !email || !password) {
    errorDiv.textContent = "Compila tutti i campi!";
    formReg.classList.add("shake");
    if (!name) nameInput.focus();
    else if (!email) emailInput.focus();
    else if (!password) passwordInput.focus();
    return;
  }

  // (1) Validazione formato email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    errorDiv.textContent = "Inserisci un indirizzo email valido.";
    formReg.classList.add("shake");
    emailInput.focus();
    return;
  }

  // (1) Controllo lunghezza minima della password (ad es. 6 caratteri)
  if (password.length < 6) {
    errorDiv.textContent = "La password deve avere almeno 6 caratteri.";
    formReg.classList.add("shake");
    passwordInput.focus();
    return;
  }
  
  // (2) Recupera la lista degli utenti da localStorage
  const users = JSON.parse(localStorage.getItem("users")) || [];
  
  // Controlla se l'email esiste già
  const esiste = users.some(u => u.email === email);
  if (esiste) {
    errorDiv.textContent = "Email già registrata! Effettua il login.";
    formReg.classList.add("shake");
    return;
  }
  
  // Crea l'oggetto utente
  const user = { name, email, password };
  users.push(user);
  localStorage.setItem("users", JSON.stringify(users));
  
  // (2) Salva il nome dell'utente per il profilo (opzionale)
  localStorage.setItem("userName", name);
  
  // (4) Messaggio di successo
  successDiv.textContent = "Registrazione avvenuta con successo! Verrai reindirizzato al login.";
  
  setTimeout(() => {
    window.location.href = "login.html";
  }, 2500);
}
