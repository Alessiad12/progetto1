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
  }