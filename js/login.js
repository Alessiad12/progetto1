// Funzione per registrare un nuovo utente
function register() {
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
  
    // Messaggio di successo (nessun popup)
    successDiv.textContent = "Registrazione avvenuta con successo! Ora effettua il login.";
  }
  
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
  