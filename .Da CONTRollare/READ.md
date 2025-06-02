
######FILE JAVASCRIPT (.JS)

-OTTIENI COORDINATE:
recupera coordinate geografiche tramite una chiamata a aun servizio esterno (OPENSTREETMAP)

.usa una fetch per chiamare l'api che restituisce le coordinate geografiche
    se ci sono risultati prende latitudine e longitudine e le restituisce come numeri
.interazione asincrona client server attraverso una fetch
    restituisce una promise gestita tramite await

.uso preventdefault per evitare il comportamento predefinito cioè invio del form e ricarica della pagina 

.sfruttamento ajax per chiamare api esterna


-INDEX:
comunicazione col server attraverso formData, fetch e http post

-NOTIFICHE REAL TIME:
gestisce in tempo reale una notifica di match tramite Websocket con socket.io 

.permette di ricevere notifiche di match accettati mentre l'utente è online,mostrando una finestra modale che annuncia il match 

.const socket = io('http://localhost:4000');
inizia la connessione websocket al server socket.io in ascolto sulla porta 4000, io stabilisce una connessione persistente 
    protocollo full-duplex
    socket.io è una libreria che astrae websocket con fallback automatici

.una volta connessi il client emette un evento join inviando l'userid,
    questo permette al server di identificare l'utente connesso 

.server invia un evento matchacceptednotification così il client
    recupera e mostra una modale con il tutolo del viaggio
    imposta un bottone per chiudere la modale

-ORGANIZER:
gestisce organizzazione di un viaggio su una mappa e l'elenco delle tappe giornaliere 

.costruisce una mappa interattiva con Leaflet(libreria javascript per mappe interattive) e un elenco dei luoghi visitati associandoli a marker numerati.

.const map = L.map('map').setView([0, 0], 2);
creo mappa con coordinate centrate sull'equatore

-PASSWORD:
.inserisco email su password.html

fetch('http://localhost:4000/forgot-password', {
  method: 'POST',
  body: JSON.stringify({ email })
})
.invia al backend node.js una richiesta post con l'email

.il server riceve la richiesta(server.js)
    genera un link di reset contenente l'email
    chiama sendemail implementato in email.js , che usa nodemailer
    invia una mail con link cliccabile

.nella mail compare link cliccabile che porta l'utente a una pagina php in cui si può inserire e salvare la nuova password 

.in server.js ci sono notifiche websocket via socket.io
io.to(`user_${userId}`).emit('matchAcceptedNotification', {...});
    il server emette eventi real time verso utenti connessi 

.cron task ogni minuto, controllo se ci sono viaggi terminati e notifica i partecipanti
    uso postgresql per verificare le date

.raggiungo nuova_password.php tramite il link inviato per mail

.uso tailwind css 

-REGISTER:
.invia una richiesta post al backend Node
    e in base alla risposta compie un'azione
    200-> mostra una modale
    errore->mostra un'alert col messaggio

-VISUALIZZA VIAGGI:
visualizzazione viaggi compatibili con funzionalità interattive come swipe,animazioni,gestioni preferenze e logout

.effettua una chiamata get al server per ottenere i viaggi compatibili con l'utente

.salvo la preferenza nel database lato server



########FILE PHP O HTML
-BACKUP.SH:
script di salvataggio del database

-COOMINGSOON:
utilizzo di tailwind

.funzione javascript per invio al server 
    fetch per inviare email in json al backend 

-CREAPROFILO:
carico dati già esistenti con una get

.utilizzo di fontAwesome 

.uso una fetch per riempire automaticamente i campi che avevo già inserito nel registrati

.invio dati con una post a save_profile.php
    il server valida i dati , salva immagine in una cartella e aggiorna i campi nel database

-ACCETTA NOTIFICA:
utente accetta richiesta , lo script
    recupera i dati della notifica nel dtabase
    crea una nuova notifica per l'altro utente
    inserisce l'utente nel viaggio
    invia una notifica realtime al mittente usando node.js+ socket.io

.fa una curl per inviare post a localost
    endpoint gestito da node.js che riceve dati e notifica il mittente tramite socket.io

.✅ Backend ibrido: PHP + PostgreSQL + cURL + Node.js + Socket.io

-CARD:
recupero sfondo,ecc dal database

.l'ultima preferenza salvata dell'utente è quella su cui basare i viaggi

.getlimiticontinente , restituisce i limiti di latitudine e longitudine per continenti noti 

.websocket tramite socket.io 
    connessione a server node.js locale
    utente entra in una stanza privata 
    altro utente accetta il match, riceve un evento realtime
    mostra modal con conferma del match

.backend= accetta_notifica.php+server.js

-CHAT:
.connessione in tempo reale con node.js 

.socket.emit('joinChat', <?php echo $viaggio_id; ?>);
socket.emit('sendMessage', { viaggio_id, mittente_id, messaggio });

.utente entra in una stanza relativa al viaggio grazie a joinchat
.invia messaggio tramite sendmessage

.scrittura del messaggio nel db avviene interamente sul server node.js  (collegamento a server.js che riceve evento , salva messaggio nel db e invia il nuovo messaggio a tutti client collegati a quella stanza)

.inserisco separatore ogni volta che la data cambia 

-CONNESSIONE:
punto d'accesso al database 

.file incluso in tutti i file che interagiscono col database 

-CREA ITINERARIO:
.utilizza openstreetmap nominatim per geocoding 

.invio tramite fetch post a save_itinerario 
    nome itinerario, lista luoghi, dove e id viaggio

-CREA PREFERENZE VIAGGI:
gestisce post recuperando dati del form, calcola un budegt e gestisce upload di una foto

.uso di tailwind per stile chiaro e moderno 

.uso di javascript vanilla per mostrare il menu destinazioni

-CREA VIAGGIO:
.utile per salvare il membro del viaggio con ruolo autore 

.uso OPENstreetMAP per geocodifica coordinate 

-GET ITINERARIO:
pagina responsive con mappa interattiva(leaflet+openstreetmap), sidebar con luoghi ordinati per giorno , marker numerati e collegamenti tra luoghi

.mappa inizializzata centrata sulle coordinate salvate nel viaggio

.luoghi convenrtiti in coordinate reali via API Openstreetmap

.luoghi divisi per giorno: ogni due luofhi , un giorno nuovo , informazioni mostrate con list-group BOOTSTRAP

-GET PROFILO:
.utilizzo di un carosello bootstrap per visualizzazione foto della destinazione

.utilizzo iframe per includere mappa dei luoghi visitati dall'utente

.javascript dinamico attraverso ajax recupero dati sull'utente,colore dello sfondo del profilo e poszione dell'immagine profilo

-LOGIN:
.login asincrono , invia dati in background via fetch, senza ricaricare la pagina 

-MAPPAMONDO:
.mappa interattiva dei viaggi effettuati da un utente usando leaflet.js

.Visualizzare su una mappa tutti i viaggi completati da un utente, posizionando dei marker geografici cliccabili che portano alla pagina dell’itinerario corrispondente

.uso di openstreetmap

-MAPPAMONDO(GET_PROFILO):
visualizzazione tutti i viaggi completati da un utente specificato via id

.leaflet.js

-MODIFICA PROFILO:
invio dati tramite fetch , ajax per inviare form senza ricaricare lapagina e in caso di successo torno a card.php

-NOTIFICHE.PHP:
.visualizzazione delle notifiche utente e interazione in tempo reale con socket.io

.websocket con socket.io permette notifiche in tempo reale

-ORGANIZER:
restituisce i dettagli del piano di viaggio in formato json

-PAGINA PROFILO:
html+css+bootstrap

.mappa interattiva

.modal bootstrap con carosello per vedere le foto

-PROVA:
mostra il dettaglio di un singolo viaggio terminato

-REGISTER:
.maggiore sicurezza con hash della password attraverso funzione e brcrypt

-SWIPE:
se il valore è like invia una notifica realtime all'organizzazione del viaggio

-VISUALIZZA VIAGGI:



