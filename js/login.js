function login() {
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
}
