// Funzione per registrare un nuovo utente
function register() {
    let name = document.getElementById("reg-name").value;
    let email = document.getElementById("reg-email").value;
    let password = document.getElementById("reg-password").value;

    // Creazione un oggetto utente
    let user = { name, email, password };

    // Recuperiamo utenti già registrati da localStorage
    let users = JSON.parse(localStorage.getItem("users")) || [];

    // Controlliamo se l'email è già registrata
    let email_esistente=false;
    for(let i=0; i<users.length;i++){
        if(users[i].email===email){
            email_esistente=true;
            break;
        }
    }
    if(email_esistente) alert("email già registrata!Effettua il login")

    // Aggiungiamo il nuovo utente e salviamo
    users.push(user);
    localStorage.setItem("users", JSON.stringify(users));

    alert("Registrazione avvenuta con successo! Ora effettua il login.");
}

// Funzione per effettuare il login
function login() {
    let email = document.getElementById("login-email").value;
    let password = document.getElementById("login-password").value;

    let users = JSON.parse(localStorage.getItem("users")) || [];

    // Verifica credenziali
    let user = users.find(u => u.email === email && u.password === password);
    
    if (user) {
        alert(`Benvenuto, ${user.name}!`);
        // Redirige alla home degli itinerari
        window.location.href = "itinerario.html";
    } else {
        alert("Email o password errate.");
    }
}
