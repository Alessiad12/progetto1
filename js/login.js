// Funzione per registrare un nuovo utente

  
  // Funzione per effettuare il login
  function login() {
    const email = document.getElementById("login-email").value;
    const password = document.getElementById("login-password").value;
  
    const errorDiv = document.getElementById("login-error");
    const successDiv = document.getElementById("login-success");
    const loginForm = document.getElementById("login-form");
  
    // Pulisci precedenti messaggi e animazioni
    errorDiv.textContent = "";
    successDiv.textContent = "";
    errorDiv.classList.remove("shake");
    loginForm.classList.remove("shake");
  
    // Recupera la lista utenti
    const users = JSON.parse(localStorage.getItem("users")) || [];
  
    // Trova l'utente
    const user = users.find(u => u.email === email && u.password === password);
  
    if (!user) {
      // Errore: credenziali non valide
      errorDiv.textContent = "Email o password errate.";
      loginForm.classList.add("shake");
      return;
    }
  
    // Se login riuscito, mostra messaggio di successo
    successDiv.textContent = `Benvenuto, ${user.name}! Stai per essere reindirizzato...`;
  
    // Attendi un attimo e reindirizza (opzionale)
    setTimeout(() => {
      window.location.href = "itinerario.html";
    }, 1000);
  }
  