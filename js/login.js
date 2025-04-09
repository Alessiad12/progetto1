/*function login() {
  console.log("LOGIN FUNCTION RICHIESTA");

  const email = document.getElementById("login-email").value;
  const password = document.getElementById("login-password").value;

  const errorDiv = document.getElementById("login-error");
  const successDiv = document.getElementById("login-success");
  const loginForm = document.getElementById("login-form");

  // Pulisci messaggi precedenti
  errorDiv.textContent = "";
  successDiv.textContent = "";
  errorDiv.classList.remove("shake");
  loginForm.classList.remove("shake");

  // 1. Controlla se email o password sono vuoti
  if (!email || !password) {
    errorDiv.textContent = "Inserisci email e password.";
    loginForm.classList.add("shake");
    return;
  }

  // 2. Recupera la lista utenti
  const users = JSON.parse(localStorage.getItem("users")) || [];

  // 3. Trova l’utente con email e password corrispondenti
  const user = users.find(u => u.email === email && u.password === password);

  // 4. Se l’utente NON esiste, mostra errore e interrompi
  if (!user) {
    errorDiv.textContent = "Email o password errate.";
    loginForm.classList.add("shake");
    return;
  }

  // 5. Se login riuscito, mostra messaggio di successo
  successDiv.textContent = `Benvenuto, ${user.name}! Reindirizzamento...`;

  // 6. Reindirizza dove preferisci, dopo un attimo o subito
  setTimeout(() => {
    // Esempio: prima "profilo.html"
    window.location.href = "profilo.html";
    // Oppure se vuoi passare subito a "itinerario.html", cambia qui:
    // window.location.href = "itinerario.html";
  }, 1000);
}*/
function login() {
  console.log("LOGIN FUNCTION RICHIESTA");

  const emailInput = document.getElementById("login-email");
  const passwordInput = document.getElementById("login-password");
  const email = emailInput.value.trim();
  const password = passwordInput.value.trim();

  const errorDiv = document.getElementById("login-error");
  const successDiv = document.getElementById("login-success");
  const loginForm = document.getElementById("login-form");

  // Reset dei messaggi e rimozione dell'animazione shake
  errorDiv.textContent = "";
  successDiv.textContent = "";
  errorDiv.classList.remove("shake");
  loginForm.classList.remove("shake");

  // (1) Controlla se i campi sono vuoti
  if (!email || !password) {
    errorDiv.textContent = "Inserisci email e password.";
    loginForm.classList.add("shake");
    if (!email) emailInput.focus();
    else if (!password) passwordInput.focus();
    return;
  }

  // (1) Validazione del formato email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    errorDiv.textContent = "Inserisci un indirizzo email valido.";
    loginForm.classList.add("shake");
    emailInput.focus();
    return;
  }

 

  // (2) Recupera la lista degli utenti da localStorage
  const users = JSON.parse(localStorage.getItem("users")) || [];

  // Cerca l'utente con email e password corrispondenti
  const user = users.find(u => u.email === email && u.password === password);

  if (!user) {
    errorDiv.textContent = "Email o password errate.";
    loginForm.classList.add("shake");
    return;
  }

  // (2) Salva l'utente corrente (intero o solo l'email)
  localStorage.setItem("currentUser", JSON.stringify(user));

  // (4) Messaggio di successo
  successDiv.textContent = `Benvenuto, ${user.name}! Reindirizzamento...`;

  // Reindirizza dopo 1 secondo
  setTimeout(() => {
    window.location.href = "profilo.html";
  }, 1000);
}
