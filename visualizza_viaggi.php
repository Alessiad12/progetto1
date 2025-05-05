<?php
require_once 'connessione.php'; // Include il file di configurazione per la connessione al DB

if (!$dbconn) {
    die("Errore connessione DB: " . pg_last_error());
}

// Recupera tutti i viaggi
$query = "SELECT * FROM viaggi ORDER BY id DESC";
$result = pg_query($dbconn, $query);

$viaggi = [];
while ($row = pg_fetch_assoc($result)) {
    $viaggi[] = $row;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Card Viaggio con Sfondo Dinamico</title>
  <link rel="stylesheet" href="css/style_index.css">
  <link rel="stylesheet" href="css/style_swipe.css">
  <style>
    /* CSS come prima */
    body {
      font-family: 'Arial', sans-serif;
      background-color: #fef6e4;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .card-container {
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: rgba(255, 255, 255, 0.8);
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      overflow: hidden;
      transition: transform 0.5s ease;
    }

    .card-container:hover {
      transform: scale(1.05);
    }
    .card {
    position: absolute;
    width: 67%;
    top: 47%;
    max-width: 1200px;
    height: 589px; 
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    justify-content: center;
    align-items: center;
    display: absolute;
    flex-direction: row;
    background-color: transparent;
    background-size: cover; 
    background-position: center;
    border-radius: 10px;
  }


    .card-content {
      width: 50%;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      color: #fff; /* Testo chiaro sopra lo sfondo scuro */
      position: absolute;
      top: 0;
      left: 0;
      background-color: rgba(0, 0, 0, 0.5); /* Fondo traslucido per il testo */
      border-radius: 10px;
    }

    .card-content h2 {
      font-size: 32px;
      font-weight: 600;
      margin-bottom: 15px;
      color: #FF7F50;
    }

    .card-content .destination {
      font-size: 20px;
      color: #FFFAFA;
      font-style: italic;
      margin-bottom: 15px;
    }

    .card-content p {
      font-size: 16px;
      color: #FFFAFA;
      margin-bottom: 20px;
      line-height: 1.6;
    }

    .card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top: 1px solid #ddd;
      padding-top: 15px;
      color: #FFFAFA;
    }

    .card-footer span {
      font-size: 14px;
    }

    .card-footer .budget {
      font-weight: bold;
      color: #FF7F50;
    }

    .card-footer .date {
      color: #888;
    }

    .btn:hover {
      background-color: #FF6A3D;
    }

    .btn:active {
      transform: scale(0.98);
    }

    .info-list {
      margin-top: 20px;
      font-size: 14px;
      color: #FFFAFA;
    }

    .info-list li {
      margin-bottom: 8px;
    }
   
  </style>
</head>
<body>
<div class="card-container" id="cardContainer">
<?php foreach ($viaggi as $viaggio): ?>
    <div class="card" id="card-<?php echo $viaggio['id']; ?>">

      <div class="card-content">
        <h2><?php echo $viaggio['destinazione']; ?></h2>
        <p class="destination">Destinazione: <?php echo $viaggio['destinazione']; ?></p>
        <p><?php echo $viaggio['descrizione']; ?></p>

        <ul class="info-list">
          <li><strong>Data partenza:</strong> <?php echo $viaggio['data_partenza']; ?></li>
          <li><strong>Data ritorno:</strong> <?php echo $viaggio['data_ritorno']; ?></li>
          <li><strong>Budget:</strong> <?php echo $viaggio['budget']; ?></li>
          <li><strong>Tipo di viaggio:</strong> <?php echo $viaggio['tipo_viaggio']; ?></li>
          <li><strong>Lingua:</strong> <?php echo $viaggio['lingua']; ?></li>
          <li><strong>Compagnia ideale:</strong> <?php echo $viaggio['compagnia']; ?></li>
          <li><strong>Descrizione:</strong> <?php echo $viaggio['descrizione']; ?></li>
        </ul>

        <div class="card-footer">
          <span class="budget">Budget: <?php echo $viaggio['budget']; ?></span>
          <span class="date">Partenza: <?php echo $viaggio['data_partenza']; ?></span>
        </div>
      </div>
    </div>
<?php endforeach; ?>
</div>
  <!-- Menu Profilo -->
  <div class="profile-menu-wrapper">
    <img src="immagini/new-york-city.jpg" alt="Foto Profilo" class="profile-icon" onclick="toggleDropdown()" />
    <div class="dropdown-menu" id="dropdownMenu">
      <a href="pagina_profilo.php">Profilo</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <script type="module" src="/js/index.js"></script>
<script>
  // Funzione per selezionare una foto casuale in base al tipo di viaggio
  function setCardBackground(tipoViaggio, id) {
    let images = [];

    if (tipoViaggio === 'relax') {
      images = [
        'https://i.pinimg.com/736x/ea/9e/4c/ea9e4c1491381155264cfb705e3d70bc.jpg',
        'https://i.pinimg.com/736x/6b/35/e5/6b35e5581c9478b41f04e5daf064181f.jpg',
        'https://i.pinimg.com/736x/5d/e7/25/5de7255c7ef36023e25ac38fe9fec211.jpg',
        'https://i.pinimg.com/736x/2b/3d/38/2b3d38dce1663d192c3014aa18694c90.jpg'
      ];
    } else if (tipoViaggio === 'party') {
      images = [
        'https://i.pinimg.com/736x/ea/9e/4c/ea9e4c1491381155264cfb705e3d70bc.jpg',
        'https://i.pinimg.com/736x/ea/9e/4c/ea9e4c1491381155264cfb705e3d70bc.jpg',
        'https://i.pinimg.com/736x/ea/9e/4c/ea9e4c1491381155264cfb705e3d70bc.jpg'
      ];
    } else if (tipoViaggio === 'Cultura') {
      images = [
        'https://i.pinimg.com/736x/53/0f/d1/530fd184b61c6a9100e6a9a8df3da270.jpg',
        'https://i.pinimg.com/736x/53/0f/d1/530fd184b61c6a9100e6a9a8df3da270.jpg',
        'https://i.pinimg.com/736x/53/0f/d1/530fd184b61c6a9100e6a9a8df3da270.jpg'
      ];
    }

    const randomImage = images[Math.floor(Math.random() * images.length)];
    document.getElementById('card-' + id).style.backgroundImage = `url(${randomImage})`;
  }

  // Esegui la funzione per ogni viaggio
  document.addEventListener('DOMContentLoaded', function() {
    const cards = <?php echo json_encode($viaggi); ?>;
    cards.forEach(function(viaggio) {
      setCardBackground(viaggio.tipo_viaggio, viaggio.id);
    });
  });
</script>


</body>
</html>
