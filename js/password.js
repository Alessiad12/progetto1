// forgot.js
document.addEventListener("DOMContentLoaded", () => {
    const input = document.querySelector(".forgot-input");
    const btn = document.querySelector(".send-link-btn");
  
    btn.addEventListener("click", () => {
      const emailOrUsername = input.value.trim();
      if (!emailOrUsername) {
        alert("Inserisci un indirizzo email o un nome utente.");
        return;
      }
      // Esempio: controlla se esiste in localStorage
      const users = JSON.parse(localStorage.getItem("users")) || [];
      const user = users.find(u => 
        u.email === emailOrUsername || 
        u.name === emailOrUsername
      );
      if (!user) {
        alert("Non troviamo un account con queste informazioni.");
      } else {
        alert(`Ok! (Simulazione) Ti abbiamo inviato un link di accesso a ${emailOrUsername}`);
      }
    });
  });
  